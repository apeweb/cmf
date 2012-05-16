<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

//class Data_Row_Collection extends ArrayObject implements Iterator {
class Data_Row_Collection extends ArrayObject {
  public function __get($key) {
    return $this[$key];
  }

  public function row ($key) {
    if (array_key_exists($key, $this)) {
      return $this[$key];
    }
    else {
      throw new OutOfRangeException ('Cannot find table ' . $key);
    }
  }

  public function rewind () {
    reset($this);
  }

  public function current () {
    return current($this);
  }

  public function key () {
    return key($this);
  }

  public function next () {
    return next($this);
  }

  public function valid () {
    return $this->current() !== FALSE;
  }

  public function removeAt ($key) {
    if (isset($this[$key])) {
      unset($this[$key]);
    }
    else {
      throw new OutOfRangeException ('Cannot find table ' . $key);
    }
  }

  public function add (Data_Row $dataRow) {
    $this[$this->count()] = $dataRow;
  }
}

?>