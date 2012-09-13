<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Cmf_Route_Normalise {
  public static function initialise () {
    Event_Dispatcher::attachObserver(Cmf_Route_Table_Event::rewriteRequestPath, __CLASS__ . '::processRequestPath');
    Event_Dispatcher::attachObserver(Cmf_Route_Table_Event::rewriteActiveRoute, __CLASS__ . '::processActiveRoute');
  }

  public static function processRequestPath (Event_Data $routeRequest) {
    $routeRequest->path = preg_replace('#/+#', '/', $routeRequest->path);
  }

  public static function processActiveRoute (Cmf_Route $route) {
    try {
      foreach ($route->getArguments() as $name => $value) {
        switch ($name) {
          case 'parent_controller':
          case 'controller':
          case 'child_controller':
            $value = str_replace('-', '_', $value);
          break;

          case 'parent_action':
          case 'action':
          case 'child_action':
            $value = lcfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', $value))));
          break;

          case 'parent_id':
          case 'id':
          case 'child_id':
            if ($value == 'draft') {
              $value = 0;
            }
          break;
        }

        $route->setNewArgumentValue($name, $value);
      }
    }
    catch (Exception $ex) {}
  }
}

?>