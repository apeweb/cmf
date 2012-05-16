<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Missing_Value_Exception extends Base_Exception {
  public function __construct ($key) {
    $message = "Missing value '" . $key . "'";
    parent::__construct($message, 0);
  }
}

?>