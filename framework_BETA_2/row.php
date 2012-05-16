<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Data_Row {
  private $_columnCollection = array();

  public function __construct (array $row = array()) {
    $this->_columnCollection = $row;
  }

  public function __get ($key) {
    if (isset($this->_columnCollection[$key])) {
      return $this->_columnCollection[$key];
    }
    else {
      throw new OutOfRangeException ('Cannot find column ' . $key);
    }
  }

  public function __set ($key, $value) {
    $this->_columnCollection[$key] = $value;
  }
}

?>