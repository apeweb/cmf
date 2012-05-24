<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

abstract class Enum {
  private function __construct(){}

  public static function getValues ($class) {
    $reflector = new ReflectionClass($class);
    return $reflector->getConstants();
  }

  public static function hasValue ($class, $const) {
    $reflector = new ReflectionClass($class);
    return in_array($const, $reflector->getConstants());
  }

  public static function isDefined ($class, $const) {
    $reflector = new ReflectionClass($class);
    return array_key_exists($const, $reflector->getConstants());
  }
}

?>