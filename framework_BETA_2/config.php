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

class Config {
  static protected $_drivers = array();
  static protected $_cache = array();

  /**
   * When the cache is cleared, it is for every method to avoid inconsistencies
   */
  static public function clearCache () {
    self::$_cache = array();
  }

  static public function addDriver (iConfig_Driver $driver) {
    Assert::isObject($driver);

    if (isset(self::$_drivers[$driver->getName()]) == FALSE) {
      self::$_drivers[$driver->getName()] = $driver;
    }
  }

  static public function addDriverBefore (iConfig_Driver $driver, $existingDriver) {
    Assert::isObject($driver);
    Assert::isString($existingDriver);

    if (empty(self::$_drivers) == TRUE || array_key_exists($existingDriver, self::$_drivers) == FALSE) {
      self::addDriver($driver);
    }
    else {
      $position = array_search($existingDriver, array_keys(self::$_drivers), TRUE);
      $initialDrivers = array_splice(self::$_drivers, 0, $position);
      self::$_drivers = array_merge($initialDrivers, array($driver->getName() => $driver), self::$_drivers);
    }
  }

  static public function addDriverAfter (iConfig_Driver $driver, $existingDriver) {
    Assert::isObject($driver);
    Assert::isString($existingDriver);

    if (empty(self::$_drivers) == TRUE || array_key_exists($existingDriver, self::$_drivers) == FALSE) {
      self::addDriver($driver);
    }
    else {
      $position = array_search($existingDriver, array_keys(self::$_drivers), TRUE) + 1;
      $initialDrivers = array_splice(self::$_drivers, 0, $position);
      self::$_drivers = array_merge($initialDrivers, array($driver->getName() => $driver), self::$_drivers);
    }
  }

  static public function removeDriver ($driver) {
    Assert::isString($driver);

    if (isset(self::$_drivers[$driver])) {
      unset(self::$_drivers[$driver]);
      self::clearCache();
    }
  }

  // Config::setValue('site_registry', 'cache', 'page', 'enabled', TRUE);
  public static function setValue ($driver, $key, $value) {
    Assert::isString($driver);
    Assert::isString($key);
    // don't assert value

    $args = func_get_args();

    $driver = array_shift($args);
    $value = current(array_slice($args, -1, 1));
    $cacheKey = md5(serialize($args));

    if (isset(self::$_drivers[$driver]) == FALSE) {
      throw new RuntimeException("Driver '{$driver}' not found");
    }
    else {
      $driver = self::$_drivers[$driver];
    }

    // If the value cannot be set, an exception will be thrown, which means we are ok to set the
    // following cache values without any checks
    $result = call_user_func_array(array($driver, 'setValue'), $args);
    self::$_cache['hasValue'][$cacheKey] = TRUE;
    self::$_cache['getValue'][$cacheKey] = $value;
    
    return $result;
  }

  public static function deleteValue ($driver, $key) {
    Assert::isString($driver);
    Assert::isString($key);

    $args = func_get_args();

    $driver = array_shift($args);
    $cacheKey = md5(serialize($args));

    if (isset(self::$_drivers[$driver]) == FALSE) {
      throw new RuntimeException('Driver not found');
    }
    else {
      $driver = self::$_drivers[$driver];
    }

    $result = call_user_func_array(array($driver, 'deleteValue'), $args);
    self::$_cache['hasValue'][$cacheKey] = FALSE;
    unset(self::$_cache['getValue'][$cacheKey]);
    
    return $result;
  }

  public static function hasValue ($key) {
    Assert::isString($key);

    $args = func_get_args();
    $cacheKey = md5(serialize($args));
    
    if (isset(self::$_cache[__FUNCTION__][$cacheKey]) == TRUE) {
      return self::$_cache[__FUNCTION__][$cacheKey];
    }

    foreach (self::$_drivers as $driver) {
      $valueExists = call_user_func_array(array($driver, 'valueExists'), $args);
      if ($valueExists == TRUE) {
        self::$_cache[__FUNCTION__][$cacheKey] = TRUE;
        return TRUE;
      }
    }

    self::$_cache[__FUNCTION__][$cacheKey] = FALSE;

    return FALSE;
  }

  public static function getValue ($key) {
    Assert::isString($key);

    $args = func_get_args();
    $cacheKey = md5(serialize($args));
    
    if (isset(self::$_cache[__FUNCTION__][$cacheKey])) {
      return self::$_cache[__FUNCTION__][$cacheKey];
    }

    foreach (self::$_drivers as $driver) {
      $valueExists = call_user_func_array(array($driver, 'valueExists'), $args);
      if ($valueExists == TRUE) {
        $value = call_user_func_array(array($driver, 'getValue'), $args);
        self::$_cache[__FUNCTION__][$cacheKey] = $value;
        self::$_cache['hasValue'][$cacheKey] = TRUE;
        return $value;
      }
    }

    self::$_cache[__FUNCTION__][$cacheKey] = NULL;

    return NULL;
  }
  
  public static function getValueByDriver ($driver, $key) {
    Assert::isString($driver);
    Assert::isString($key);
  
    $args = func_get_args();
    $cacheKey = md5(serialize($args));
    
    if (isset(self::$_cache[__FUNCTION__][$cacheKey])) {
      return self::$_cache[__FUNCTION__][$cacheKey];
    }
  
    if (isset(self::$_drivers[$driver])) {
      $driver = self::$_drivers[$driver];
      $valueExists = call_user_func_array(array($driver, 'valueExists'), $args);
      if ($valueExists == TRUE) {
        $value = call_user_func_array(array($driver, 'getValue'), $args);
        self::$_cache[__FUNCTION__][$cacheKey] = $value;
        return $value;
      }
    }

    self::$_cache[__FUNCTION__][$cacheKey] = NULL;

    return NULL;
  }

  public static function driverExists ($driverName) {
    return array_key_exists($driverName, self::$_drivers);
  }
}

?>