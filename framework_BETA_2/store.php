<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Data_Store extends ArrayObject implements Countable {
  public function __set ($variableName, $value) {
    $this[$variableName] = $value;
  }

  public function __get ($variableName) {
    if (array_key_exists($variableName, $this)) {
      return $this[$variableName];
    }
    else {
      throw new Missing_Value_Exception($variableName);
    }
  }

  public function __isset ($variableName) {
    return isset($this[$variableName]);
  }

  public function __unset ($variableName) {
    unset($this[$variableName]);
  }
  
  public function count () {
    return count($this);
  }
}

?>