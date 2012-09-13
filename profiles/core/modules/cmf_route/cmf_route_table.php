<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

// xxx move some of the route stuff to the framework folder

class Cmf_Route_Table {
  static private $_path = '';
  static private $_routeCollection = NULL; // Cmf_Route_Collection object

  const Prepared_Statement_Library = 'cmf_route_table_prepared_statement_library';

  public static function initialise () {
    if (Request::queryString('q') != '') {
      self::$_path = Request::queryString('q');
    }
    else {
      self::$_path = Request::path();
    }

    Debug::logMessage('Path', self::$_path);

    self::$_routeCollection = new Cmf_Route_Collection;

    self::_registerStoredRoutes();
    Event_Dispatcher::attachObserver(Cmf_Application_Event::execute, __CLASS__ . '::registerRoutes');
    Event_Dispatcher::attachObserver(Cmf_Application_Event::terminate, __CLASS__ . '::processRequest');
  }

  public static function routes () {
    return self::$_routeCollection;
  }

  public static function addStoredRoute (Cmf_Route $route) {
    // xxx finish
  }

  public static function getStoredRoute ($routeName) {
    // xxx finish
  }

  public static function removeStoredRoute ($routeName) {
    // xxx finish
  }

  // Stored routes won't always match the routes held in the route collection
  public static function getAllStoredRoutes ($active = 1, $deleted = 0) {
    $routes = Cmf_Database::call('cmf_route_table_get_all', self::Prepared_Statement_Library);
    $routes->bindValue(':rt_active', $active);
    $routes->bindValue(':rt_deleted', $deleted);
    $routes->bindValue(':s_id', Config::getValue('site', 'id'));
    $routes->execute();

    $routes = $routes->fetchAll(PDO::FETCH_ASSOC);

    if ($routes == FALSE) {
      $routes = array();
    }

    return $routes;
  }

  private static function _registerStoredRoutes () {
    $routeArguments = array();

    $routes = self::getAllStoredRoutes();

    $query = Cmf_Database::call('cmf_route_table_get_all_route_arguments', self::Prepared_Statement_Library);
    $query->bindValue(':rtarg_active', 1);
    $query->bindValue(':rtarg_deleted', 0);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();

    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
      $routeArguments[$row['rt_id']]['defaultValues'][$row['rtarg_name']] = $row['rtarg_default_value'];
      $routeArguments[$row['rt_id']]['masks'][$row['rtarg_name']] = $row['rtarg_mask'];
    }

    foreach ($routes as $route) {
      $defaultValues = array();
      $masks = array();

      if (isset($routeArguments[$route['rt_id']]['defaultValues'])) {
        $defaultValues = $routeArguments[$route['rt_id']]['defaultValues'];
      }

      if (isset($routeArguments[$route['rt_id']]['masks'])) {
        $masks = $routeArguments[$route['rt_id']]['masks'];
      }
      
      $route = new Cmf_Route($route['rt_name'], $route['rt_url'], $defaultValues, $masks);
      self::$_routeCollection->addRoute($route);
    }
  }

  public static function registerRoutes () {
    Event_Dispatcher::notifyObservers(Cmf_Route_Table_Event::registerRoutes);
  }

  public static function processRequest () {
    $route = self::getActiveRoute();
    $controllerFactory = Controller_Builder::getControllerFactory();
    $controllerFactory->createController($route);
  }

  // xxx the first argument should be a Request object
  // xxx or if not supplied should default to Application_Context::getCurrentContext()->getRequest())
  // xxx to make it context aware
  public static function getActiveRoute ($reset = FALSE) {
    static $route = NULL;

    if ($reset == FALSE && $route !== NULL) {
      return $route;
    }

    $controllerFactory = Controller_Builder::getControllerFactory();
    $routeRequest = new Event_Data;
    $routeRequest->path = Request::path();

    Event_Dispatcher::notifyObservers(Cmf_Route_Table_Event::rewriteRequestPath, $routeRequest);

    foreach (self::$_routeCollection->getAllRoutes() as $route) {
      // Check to see if the route mask matches our path
      if (preg_match($route->getUrlMask(), $routeRequest->path) == TRUE) {
        try {
          // Get route aliases
          Event_Dispatcher::notifyObservers(Cmf_Route_Table_Event::rewriteActiveRoute, $route);

          $controller = $route->getArgumentValue('controller');
          $action = $route->getArgumentValue('action');

          if ($controller == '' || $action == '') {
            Debug::logMessage('Activating Route', $route->getUrl(array(), FALSE));
            Debug::logMessage('Activating Route', "The route '{$route->getName()}' contained an empty controller '{$controller}' and/or action '{$action}'");
            continue;
          }

          $controller = $controllerFactory->normaliseControllerName($controller);

          Debug::logMessage('Activating Route', $controller . '::' . $action);

          if (Cmf_Controller_Cache::controllerExists($controller) == FALSE) {
            Debug::logMessage('Activating Route', "The controller specified '{$controller}' could not be found.");
            continue;
          }

          // We now need to load the controller so that we can check if the required action exists
          $controllerFactory->loadController($controller);

          // Check if the controller is accessible
          $controllerPath = $controllerFactory->getControllerPath($controller);

          $pathsDeniedAccess = $route->getPathsDenied();
          if (count($pathsDeniedAccess) > 0) {
            foreach ($pathsDeniedAccess as $pathDeniedAccess) {
              $mask = '#^' . preg_quote($pathDeniedAccess, '#') . '#';
              if (preg_match($mask, $controllerPath) == TRUE) {
                throw new Unauthorised_Access_Exception("Route '" . $route->getName() . "' is not permitted to access path '{$controllerPath}'");
              }
            }
          }

          $pathsAllowedAccess = $route->getPathsAllowed();
          if (count($pathsAllowedAccess) > 0) {
            $pathAllowed = FALSE;

            foreach ($pathsAllowedAccess as $pathAllowedAccess) {
              $mask = '#^' . preg_quote($pathAllowedAccess, '#') . '#';
              if (preg_match($mask, $controllerPath) == TRUE) {
                $pathAllowed = TRUE;
                break;
              }
            }

            if ($pathAllowed == FALSE) {
              throw new Unauthorised_Access_Exception("Route '" . $route->getName() . "' is not permitted to access path '{$controllerPath}'");
            }
          }

          // Check if action exists
          if (method_exists($controller, $action) == TRUE) {
            return $route;
          }

          Debug::logMessage('Activating Route', "The action specified '{$action}' could not be found.");
        }
        catch (Exception $ex) {
          Debug::logMessage('Activating Route', $ex->getMessage());
          continue;
        }
      }
      else {
        Debug::logMessage('Activating Route', $route->getUrl(array(), FALSE));
        Debug::logMessage('Activating Route', "The route '{$route->getName()}' did not match the URL");
      }
    }

    throw new Http_Exception("No matching route could be found for path '" . Request::path() . "'");
  }
}

?>