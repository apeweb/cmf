<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Literal {
  static protected $_cultureDrivers = array(); // Which drivers are assigned to which culture
  static protected $_drivers = array(); // xxx Cache of the drivers? need to create a global assembly cache (GAC)
  static protected $_initialised = FALSE;

  /**
   * Loads in the language driver
   * @param iLiteral_Driver $driver The driver instance to add
   * @param string $culture The culture the driver belonds to
   */
  static public function addDriver (iLiteral_Driver $driver, $culture = NULL) {
    Assert::isObject($driver);
    Assert::isString($culture, TRUE);

    if ($culture === NULL) {
      $culture = Culture_Info::current();
    }

    if (isset(self::$_cultureDrivers[$culture][$driver->getName()]) == FALSE) {
      // xxx create a reference to the driver name and not to the driver itself to save on resources (use GAC)
      self::$_cultureDrivers[$culture][$driver->getName()] = $driver;
    }
    
    self::$_initialised = TRUE;
  }

  /**
   * Unloads the driver
   * @param string $driver The name of the driver to unload and de-reference
   * @param string $culture The culture to remove the driver from
   */
  static public function removeDriverFromCulture ($driver, $culture = NULL) {
    Assert::isString($driver);
    Assert::isString($culture, TRUE);

    if ($culture === NULL) {
      $culture = Culture_Info::current();
    }

    unset(self::$_cultureDrivers[$culture][$driver]);
  }

  static public function removeDriver ($driver) {
    Assert::isString($driver);

    foreach (array_keys(self::$_cultureDrivers) as $culture) {
      unset(self::$_cultureDrivers[$culture][$driver]);
    }
  }

  /**
   * Load a driver and set it to be used before another driver
   * @param iLiteral_Driver $driver The instance of the new driver to add
   * @param string $existingDriver The name of the existing driver to add the new driver before
   * @param string $culture The culture to add the driver to
   */
  static public function addDriverBefore (iLiteral_Driver $driver, $existingDriver, $culture = NULL) {
    Assert::isObject($driver);
    Assert::isString($existingDriver);
    Assert::isString($culture, TRUE);

    if ($culture === NULL) {
      $culture = Culture_Info::current();
    }

    if (empty(self::$_cultureDrivers[$culture]) == TRUE || array_key_exists($existingDriver, self::$_cultureDrivers[$culture]) == FALSE) {
      self::addDriver($driver, $culture);
    }
    else {
      $position = array_search($existingDriver, array_keys(self::$_cultureDrivers[$culture]), TRUE);
      $initialCultureDrivers = array_splice(self::$_cultureDrivers[$culture], 0, $position);
      self::$_cultureDrivers[$culture] = array_merge($initialCultureDrivers, array($driver->getName() => $driver), self::$_cultureDrivers[$culture]);
    }
    
    self::$_initialised = TRUE;
  }

  /**
   * Load a driver and set it to be used after another driver
   * @param iLiteral_Driver $driver The instance of the new driver to add
   * @param string $existingDriver The name of the existing driver to add the new driver after
   * @param string $culture The culture to add the driver to
   */
  static public function addDriverAfter (iLiteral_Driver $driver, $existingDriver, $culture = NULL) {
    Assert::isObject($driver);
    Assert::isString($existingDriver);
    Assert::isString($culture, TRUE);

    if ($culture === NULL) {
      $culture = Culture_Info::current();
    }

    if (empty(self::$_cultureDrivers[$culture]) == TRUE || array_key_exists($existingDriver, self::$_cultureDrivers[$culture]) == FALSE) {
      return self::addDriver($driver, $culture);
    }
    else {
      $position = array_search($existingDriver, array_keys(self::$_cultureDrivers[$culture]), TRUE) + 1;
      $initialCultureDrivers = array_splice(self::$_cultureDrivers[$culture], 0, $position);
      self::$_cultureDrivers[$culture] = array_merge($initialCultureDrivers, array($driver->getName() => $driver), self::$_cultureDrivers[$culture]);
    }
    
    self::$_initialised = TRUE;
  }

  /**
   * Returns a phrase for a key
   * @param string $key The key (id) of the phrase to get
   * @param string $group The group the key should belong to, NULL means no group
   * @param string $culture The culture of the phrase to get, NULL means use culture in use
   * @param string $driver The specific driver to get the phrase from, NULL means check all drivers in order
   */
  static public function getPhrase ($key, $group = NULL, $culture = NULL, $driver = NULL) {
    Assert::isString($key);
    Assert::isString($group, TRUE);
    Assert::isString($culture, TRUE);
    Assert::isString($driver, TRUE);
    
    /**
     * If no drivers have been loaded yet, the chances are that the application hasn't finished
     * booting yet or literals aren't in use
     */
    if (self::$_initialised == FALSE) {
      return $key;
    }

    if ($culture === NULL) {
      $culture = Culture_Info::current();
    }

    if (self::cultureExists($culture) == FALSE) {
      throw new Exception("Culture does not exist");
    }

    if ($driver !== NULL && self::$_cultureDrivers[$culture][$driver]->phraseExists($key, $group) == TRUE) {
      return self::$_cultureDrivers[$culture][$driver]->getPhrase($key, $group);
    }

    // Look in the current/selected language
    foreach (self::$_cultureDrivers[$culture] as $driver) {
      if ($driver->phraseExists($key, $group) == TRUE) {
        return $driver->getPhrase($key, $group);
      }
    }

    // Look in the invariant language
    foreach (self::$_cultureDrivers[Culture_Info::invariantCulture] as $driver) {
      if ($driver->phraseExists($key, $group) == TRUE) {
        return $driver->getPhrase($key, $group);
      }
    }

    return $key;
  }

  // xxx finish, if using stored procedure to get stuff from meta we can then do so
  // like so mb_group = 'country', mb_reference = 'IL', mb_code = '677', w_id = 1, culture = 'en'
  /**
   * mb_id
   * mb_group
   * mb_reference
   * mb_code
   * mb_default // is the value the default?
   * mb_order // show the info in a specific order
   * w_id // site id
   * mb_active
   * mb_deleted

   set $culture = NULL, $driver = NULL, to NULL by default

   Literal::getPhraseByGroup('UKBAADDINF', 'studenteventstatus', 'UKBA');

   Uses default culture and checks all drivers, if nothing is found then checks invariant culture, to specify a
   culture to use you must first change the current culture to the one you want to use be changed before
   using the function

   */
  static public function getPhraseByGroup ($key, $group) {
    Assert::isString($key);
    Assert::isString($group, TRUE);
    
    /**
     * If no drivers have been loaded yet, the chances are that the application hasn't finished
     * booting yet or literals aren't in use
     */
    if (self::$_initialised == FALSE) {
      return $key;
    }

    $culture = Culture_Info::current();

    if (self::cultureExists($culture) == FALSE) {
      throw new Exception("Culture does not exist");
    }

    $arguments = func_get_args();

    // Look in the current/selected language
    foreach (self::$_cultureDrivers[$culture] as $driver) {
      if (call_user_func_array(array($driver, 'phraseExists'), $arguments) == TRUE) {
        return call_user_func_array(array($driver, 'getPhrase'), $arguments);
      }
    }

    // Look in the invariant language
    foreach (self::$_cultureDrivers[Culture_Info::invariantCulture] as $driver) {
      if (call_user_func_array(array($driver, 'phraseExists'), $arguments) == TRUE) {
        return call_user_func_array(array($driver, 'getPhrase'), $arguments);
      }
    }

    return $key;
  }

  /**
   * Returns a formatted phrase for a key
   * @param string $key The key (id) of the phrase to get
   * @param string $group The group the key should belong to, NULL means no group
   * @param string $culture The culture of the phrase to get, NULL means use culture in use
   * @param string $driver The specific driver to get the phrase from, NULL means check all drivers in order
   */
  static public function format ($key, $transformations, $group = NULL, $culture = NULL, $driver = NULL) {
    $phrase = self::getPhrase($key, $group, $culture, $driver);
    return vsprintf($phrase, $transformations);
  }

  /**
   * Checks to see if a phrase exists
   * @param string $key The key (id) of the phrase to check
   * @param string $group The group the key should belong to, NULL means no group
   * @param string $culture The culture of the phrase to get, NULL means use culture in use
   * @param string $driver The specific driver to get the phrase from, NULL means check all drivers in order
   */
  static public function phraseExists ($key, $group = NULL, $culture = NULL, $driver = NULL) {
    Assert::isString($key);
    Assert::isString($group, TRUE);
    Assert::isString($culture, TRUE);
    Assert::isString($driver, TRUE);
    
    /**
     * If no drivers have been loaded yet, the chances are that the application hasn't finished
     * booting yet or literals aren't in use
     */
    if (self::$_initialised == FALSE) {
      return FALSE;
    }

    if ($culture === NULL) {
      $culture = Culture_Info::current();
    }

    if (self::cultureExists($culture) == FALSE) {
      throw new Exception("Culture does not exist");
    }

    if ($driver !== NULL && self::$_cultureDrivers[$culture][$driver]->phraseExists($key, $group) == TRUE) {
      return TRUE;
    }

    // Look in the current/selected language
    foreach (self::$_cultureDrivers[$culture] as $driver) {
      if ($driver->phraseExists($key, $group) == TRUE) {
        return TRUE;
      }
    }

    // Look in the invariant language
    foreach (self::$_cultureDrivers[Culture_Info::invariantCulture] as $driver) {
      if ($driver->phraseExists($key, $group) == TRUE) {
        return TRUE;
      }
    }

    return FALSE;
  }

  // xxx continue updating...

  // xxx move to cultures class
  /**
   * Returns a list of languages currently in use
   *
   * @param driver Show only the languages using in the specific driver
   */
  static public function languages ($driverName = NULL) {
    Assert::isString($driverName, TRUE);
    
    $languages = array();

    if ($driverName === NULL) {
      $languages = array_keys(self::$_languages);
    }
    else {
      if (self::driverExists($driverName) == TRUE) {
        foreach (array_keys(self::$_languages) as $language) {
          if (isset(self::$_languages[$language][$driverName]) == TRUE) {
            $languages[] = $language;
          }
        }
      }
      else {
        throw new Exception("Driver does not exist");
      }
    }

    return $languages;
  }

  /**
   * Returns a list of language drivers (effectively the classes in use)
   *
   * @param language Show only the drivers used in the specific language
   */
  static public function drivers ($language = NULL) {
    Assert::isString($language, TRUE);
    
    if ($language === NULL) {
      return array_keys(self::$_drivers);
    }
    else {
      if (self::languageExists($language) == TRUE) {
        return array_keys(self::$_languages[$language]);
      }
      else {
        throw new Exception("Language does not exist");
      }
    }
  }

  /**
   * Check if language has been set
   *
   * @param language The name of the language, ie en-gb
   */
  static public function cultureExists ($culture) {
    Assert::isString($culture);

    if (array_key_exists($culture, self::$_cultureDrivers) == TRUE) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Checks to see if a driver has been loaded to the point where it can be used
   *
   * @param driverName The name of the language driver, ie English_Db_Lang
   */
  static public function driverExists ($driverName) {
    Assert::isString($driverName);

    if (array_key_exists($driverName, self::$_drivers) == TRUE) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }
}

?>