<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Cmf_Route_Normalise {
  public static function initialise () {
    Event_Dispatcher::attachObserver(Cmf_Route_Table_Event::rewriteActiveRoute, __CLASS__ . '::processActiveRoute');
  }

  public static function processActiveRoute (Cmf_Route $route) {
    try {
      foreach ($route->getArguments() as $name => $value) {
        $route->setNewArgumentValue($name, trim($value, '/'));
      }
    }
    catch (Exception $ex) {}
  }
}

?>