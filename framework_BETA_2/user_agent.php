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

class User_Agent {
  public function __get ($variableName) {
    switch ($variableName) {
      case 'id':
        return $this->id();

      default:
        throw new Missing_Value_Exception($variableName);
    }
  }

  public function __set ($variableName, $value) {
    switch ($variableName) {
      case 'id':
        throw new Missing_Value_Exception($variableName);

      default:
        throw new Missing_Value_Exception($variableName);
    }
  }

  public function id () {
    return md5($_SERVER['HTTP_USER_AGENT']);
  }

  public function __toString () {
    return $_SERVER['HTTP_USER_AGENT'];
  }
}

?>