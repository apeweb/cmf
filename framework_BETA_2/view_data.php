<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

// shorthand for views
function View_Data ($key) {
  return View_Data::getValue($key);
}

class View_Data extends Memory {
  public static function getValue ($key) {
    try {
      return call_user_func_array('parent::getValue', func_get_args());
    }
    catch (Exception $ex) {
      // xxx this should log a message
      return NULL;
    }
  }
}

?>