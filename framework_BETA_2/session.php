<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

/**
 * The Session class is a wrapper to provide a common method for using sessions within the framework, as sessions can
 * only have one driver there is no need to worry about caching or keeping an array of session drivers
 */
class Session {
  private static $_handler = NULL;

  // Pass the class name
  public static function setHandler ($handler) {
    Assert::isString($handler);

    $interfaces = class_implements($handler, FALSE);
    if (in_array('iSession_Handler', $interfaces) == FALSE) {
      throw new RuntimeException("'{$handler}' does not implement the iSession_Handler interface");
    }

    self::$_handler = $handler;
  }

  // Returns the class name
  public static function getHandler () {
    return self::$_handler;
  }

  public static function handlerExists () {
    return (self::$_handler !== NULL);
  }

  public static function load () {
    if (self::handlerExists() == FALSE) {
      // xxx throw exception
    }

    return call_user_func_array(self::$_handler . '::' . __FUNCTION__, func_get_args());
  }

  public static function start () {
    if (self::handlerExists() == FALSE) {
      // xxx throw exception
    }

    return call_user_func_array(self::$_handler . '::' . __FUNCTION__, func_get_args());
  }

  public static function exists () {
    if (self::handlerExists() == FALSE) {
      // xxx throw exception
    }

    return call_user_func_array(self::$_handler . '::' . __FUNCTION__, func_get_args());
  }

  public static function regenerate () {
    if (self::handlerExists() == FALSE) {
      // xxx throw exception
    }

    return call_user_func_array(self::$_handler . '::' . __FUNCTION__, func_get_args());
  }

  public static function destroy () {
    if (self::handlerExists() == FALSE) {
      // xxx throw exception
    }

    return call_user_func_array(self::$_handler . '::' . __FUNCTION__, func_get_args());
  }

  public static function close () {
    if (self::handlerExists() == FALSE) {
      // xxx throw exception
    }

    return call_user_func_array(self::$_handler . '::' . __FUNCTION__, func_get_args());
  }

  public static function write () {
    if (self::handlerExists() == FALSE) {
      // xxx throw exception
    }

    return call_user_func_array(self::$_handler . '::' . __FUNCTION__, func_get_args());
  }

  public static function purge () {
    if (self::handlerExists() == FALSE) {
      // xxx throw exception
    }

    return call_user_func_array(self::$_handler . '::' . __FUNCTION__, func_get_args());
  }

  public static function setValue () {
    if (self::handlerExists() == FALSE) {
      // xxx throw exception
    }

    return call_user_func_array(self::$_handler . '::' . __FUNCTION__, func_get_args());
  }

  public static function getValue () {
    if (self::handlerExists() == FALSE) {
      // xxx throw exception
    }

    return call_user_func_array(self::$_handler . '::' . __FUNCTION__, func_get_args());
  }
  
  public static function valueExists () {
    if (self::handlerExists() == FALSE) {
      // xxx throw exception
    }

    return call_user_func_array(self::$_handler . '::' . __FUNCTION__, func_get_args());
  }

  public static function deleteValue () {
    if (self::handlerExists() == FALSE) {
      // xxx throw exception
    }

    return call_user_func_array(self::$_handler . '::' . __FUNCTION__, func_get_args());
  }
}

?>