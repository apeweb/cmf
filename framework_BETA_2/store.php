<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Store extends ArrayObject {
  public function setValue ($name, $value) {
    Assert::isString($name);
    $this[$name] = $value;
  }

  public function getValue ($name) {
    Assert::isString($name);

    if (self::valueExists($name) == FALSE) {
      throw new Missing_Value_Exception($name);
    }

    return $this[$name];
  }

  public function valueExists ($name) {
    Assert::isString($name);
    return array_key_exists($name, $this);
  }

  public function deleteValue ($name) {
    Assert::isString($name);
    unset($this[$name]);
  }
}

?>