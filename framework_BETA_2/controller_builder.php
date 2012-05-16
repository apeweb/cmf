<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Controller_Builder {
  private static $_controllerFactory = '';

  static public function setControllerFactory (iController_Factory $controllerFactory) {
    self::$_controllerFactory = $controllerFactory;
  }

  static public function getControllerFactory () {
    return self::$_controllerFactory;
  }
}

?>
