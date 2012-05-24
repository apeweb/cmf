<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Activator implements iController_Factory {
  protected $_controllerInstances = array();

  public function createController ($controller)  {
    Assert::isString($controller);

    if (Validator::isValidPhpIdentifier($controller) == FALSE) {
      throw new Argument_Exception("Class '{$controller}' is not a valid class name");
    }

    if (class_exists($controller) == FALSE) {
      throw new RuntimeException("Class '{$controller}' could not be found");
    }

    if (isset($this->_controllerInstances[$controller]) == TRUE) {
      return $this->_controllerInstances[$controller];
    }

    $this->_controllerInstances[$controller] = new $controller;

    return $this->_controllerInstances[$controller];
  }
}

?>
