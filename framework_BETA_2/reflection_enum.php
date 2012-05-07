<?php

/**
 * Copyright (c) 2011 Ape Web Ltd.  All rights reserved.
 *
 * The use and distribution terms for this software are contained in the file
 * named license.txt, which can be found in the root of this distribution.
 * By using this software in any fashion, you are agreeing to be bound by the
 * terms of this license.
 *
 * You must not remove this notice, or any other, from this software.
 */

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Reflection_Enum implements Reflector {
  private $_class; // ReflectionClass

  public function __construct ($class) {
    $this->_class = new ReflectionClass($class);
  }

  public function __toString () {
    return strval($this->_class);
  }

  static public function hasConstant ($class, $constant) {
    $reflector = new ReflectionClass($class);
    return in_array($constant, $reflector->getConstants());
  }

  static public function export ($class, $return = FALSE) {
    $reflector = new ReflectionClass($class);
    if ($return == FALSE) {
      $reflector->export($class);
    }
    else {
      return $reflector;
    }
  }

  static public function getConstantName ($class, $constantValue) {
    $class = new ReflectionClass($class);
    $constants = $class->getConstants();

    foreach ($constants as $name => $value) {
      if ($value == $constantValue) {
        return $name;
      }
    }

    throw new RuntimeException('Value not found');
  }
}

?>