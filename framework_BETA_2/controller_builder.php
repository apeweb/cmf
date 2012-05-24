<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Controller_Builder {
  private static $_controllerFactory = '';

  static public function setControllerFactory (iController_Factory $controllerFactory, $autoload = FALSE) {
    if ($autoload == TRUE) {
      spl_autoload_unregister(array(self::$_controllerFactory, 'loadController'));
    }

    self::$_controllerFactory = $controllerFactory;

    if ($autoload == TRUE) {
      spl_autoload_register(array(self::$_controllerFactory, 'loadController'));
    }
  }

  static public function getControllerFactory () {
    return self::$_controllerFactory;
  }
}

?>
