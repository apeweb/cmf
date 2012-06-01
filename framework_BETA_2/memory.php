<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

// Use this class to store values specific to the request, such as the theme that should be applied to the page
class Memory {
  static private $_locks = array();
  static private $_cache = array();

  public static function setValue ($key, $value) {
    Assert::isString($key);

    list($key, $value, $originalKey) = self::_getArgs(func_get_args());

    if (array_key_exists($key, self::$_locks) == TRUE) {
      throw new RuntimeException("Value " . $originalKey . " is locked");
    }

    self::$_cache[$key] = $value;
  }

  // To lock a value so that it can't be unlocked easily use Memory::lockValue('key1', 'key2', rand());
  public static function lockValue ($key, $password) {
    Assert::isString($key);

    list($key, $password, $originalKey) = self::_getArgs(func_get_args());

    if (array_key_exists($key, self::$_locks) == TRUE) {
      // Have to throw an exception as we need to know if a key is already locked
      throw new RuntimeException("Lock already exists for value " . $originalKey);
    }

    self::$_locks[$key] = $password;
  }

  public static function unlockValue ($key, $password) {
    Assert::isString($key);

    list($key, $password, $originalKey) = self::_getArgs(func_get_args());

    if (isset(self::$_locks[$key]) == FALSE) {
      throw new RuntimeException("Lock does not exists for value " . $originalKey);
    }

    if (self::$_locks[$key] !== $password) {
      throw new RuntimeException("Password for value lock " . $originalKey . " is incorrect");
    }

    unset(self::$_locks[$key]);
  }

  public static function getValue ($key) {
    Assert::isString($key);

    $args = func_get_args();

    $originalKey = '$' . implode('->', $args);
    $key = md5($originalKey);

    if (array_key_exists($key, self::$_cache) == FALSE) {
      throw new Missing_Value_Exception("Value " . $originalKey . " does not exist");
    }

    return self::$_cache[$key];
  }

  private static function _getArgs ($args) {
    $value = array_pop($args);

    $originalKey = '$' . implode('->', $args);
    $key = md5($originalKey);

    return array($key, $value, $originalKey);
  }

  public static function valueExists ($key) {
    Assert::isString($key);

    $args = func_get_args();

    $originalKey = '$' . implode('->', $args);
    $key = md5($originalKey);

    return array_key_exists($key, self::$_cache);
  }
}

?>