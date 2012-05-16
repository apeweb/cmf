<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

define('boolean', 'boolean', TRUE);
define('bool', 'boolean', TRUE);
define('integer', 'integer', TRUE);
define('int', 'integer', TRUE);
define('double', 'double', TRUE);
define('float', 'double', TRUE);
define('string', 'string', TRUE);
define('str', 'string', TRUE);
define('array', 'array', TRUE);
define('object', 'object', TRUE);
define('resource', 'resource', TRUE);

class Invalid_Cast_Exception extends Base_Exception {
  public function __construct ($variable, $expectedType) {
    $message = "Conversion from '" . gettype($variable) . "' to type '" . $expectedType . "' is not valid.";
    parent::__construct($message);
  }
}

?>