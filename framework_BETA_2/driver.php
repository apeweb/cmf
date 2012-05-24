<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

abstract class Driver implements iDriver {
  protected $_name = '';
  protected $_options = array();

  public function setName ($name) {
    Assert::isString($name);

    $this->_name = $name;
  }

  public function getName () {
    return $this->_name;
  }

  // $driver->setOption('bindArgumentToParameter', array(array('argument' => 0, 'name' => '$id')));
  public function setOption($name, $value, $merge = FALSE) {
    Assert::isString($name);
    Assert::isBoolean($merge);

    if ($merge == FALSE) {
      $this->_options[$name] = $value;
    }
    else {
      array_merge((array) $this->_options[$name], (array) $value);
    }
  }

  public function getOption ($name) {
    Assert::isString($name);

    if (array_key_exists($name, $this->_options) == TRUE) {
      return $this->_options[$name];
    }

    return NULL;
  }
}


?>