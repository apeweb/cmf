<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

// xxx add dependancy cmf_controller_cache

class Cmf_Controller_Factory implements iController_Factory {
  private $_controller = '';
  private $_action = '';
  private $_arguments = array();

  const Prepared_Statement_Library = 'cmf_controller_factory_prepared_statement_library';

  // tell the controller builder that this factory should be used to create instances of controllers
  public static function initialise () {
    $instance = new Cmf_Controller_Factory;
    Controller_Builder::setControllerFactory($instance, TRUE);
  }

  // checks to see if the controller and action are valid before passing them off to the _activateController method
  public function createController ($route) {
    $this->_arguments = $route->getArguments();

    if (isset($this->_arguments['controller']) == FALSE || trim($this->_arguments['controller']) == '') {
      throw new Http_Exception("A valid controller is not set");
    }

    if (isset($this->_arguments['action']) == FALSE || trim($this->_arguments['action']) == '') {
      throw new Http_Exception("A valid action is not set");
    }

    $this->_controller = $this->_arguments['controller'];
    $this->_controller = $this->normaliseControllerName($this->_controller);
    $route->setNewArgumentValue('controller', $this->_controller);

    $this->_action = $this->_arguments['action'];

    self::_activateController();
  }

  // creates an actual instance of the controller
  private function _activateController () {
    if (class_exists($this->_controller, FALSE) == FALSE) {
      $this->loadController($this->_controller);
    }
    $controller = new $this->_controller;
    $controller->{$this->_action}($this->_arguments);
  }

  public function loadController ($controller) {
    // For the sake of the autoloader, make sure that we are dealing with a controller
    if (preg_match('#_Controller$#', $controller) == FALSE) {
      return;
    }

    // The autoloader isn't the only thing that uses this method, so make sure the controller hasn't already been loaded
    if (class_exists($controller) == TRUE) {
      return;
    }

    $path = Cmf_Controller_Cache::getControllerPath($controller);

    if ($path == FALSE || is_file($path) == FALSE) {
      throw new Http_Exception("A valid controller could not be found");
    }

    // Don't use require_once as this prevents APC from doing optimisations on the autoloader
    require $path;
  }

  // get the controller machine name
  public function normaliseControllerName ($controller) {
    if (preg_match('#_Controller$#', $controller) == FALSE) {
      $controller = str_replace(' ', '_', ucwords(str_replace('_', ' ', $controller))) . '_Controller';
    }
    return $controller;
  }

  public function getControllerPath ($controller) {
    return Cmf_Controller_Cache::getControllerPath($controller);
  }
}

?>