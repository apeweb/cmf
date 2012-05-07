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

class Cmf_Module_Manager {
  static private $_modules = array();
  static private $_modulesLoaded = array();

  static public function initialise () {
    $paths = array();

    self::getModuleList();

    foreach (self::$_modules as $module) {
      if (self::hasLoaded($module['m_name']) == FALSE && self::moduleExists($module['m_name']) == TRUE) {
        $paths[$module['m_path']] = $module['m_path'];

        // xxx include subfolders too, this is important if a module is a library of classes that don't do anything by themselves
        // there are pros and cons of doing so, such as if we do so then we are performing lots of stats on the filesystem
      }
    }

    $includePath = implode(PATH_SEPARATOR, $paths);

    Debug::logMessage('Module paths loaded:', $includePath);

    // Quickly set all include paths at once
    if ($includePath != '') {
      set_include_path($includePath . PATH_SEPARATOR . get_include_path());
    }

    foreach (self::$_modules as $module) {
      self::loadModule($module['m_name'], FALSE);
    }
  }

  /**
   * Modules should be loaded by weight, and then alphabetically, if a module depends on
   * another, it will load the other module
   * @return array A list of all active modules
   */
  static public function getModuleList () {
    if (count(self::$_modules) > 0) {
      return self::$_modules;
    }

    $query = Cmf_Database::call('cmf_modules_get_all');
    $query->bindValue(':m_installed', '1');
    $query->bindValue(':m_active', '1');
    $query->bindValue(':m_deleted', '0');
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();

    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
      self::$_modules[$row['m_name']] = $row;
    }

    return self::$_modules;
  }

  static public function loadModule ($moduleName, $setIncludePath = TRUE, $runInitialiseFunction = TRUE) {
    Assert::isString($moduleName);

    // To save on resources we check to see if the module exists only if it isn't loaded
    if (self::hasLoaded($moduleName) == FALSE) {
      if (self::moduleExists($moduleName) == TRUE) {
        self::_loadModuleHelper(self::$_modules[$moduleName], $setIncludePath, $runInitialiseFunction);
        self::$_modulesLoaded[] = $moduleName;
      }
      else {
        throw new Argument_Exception("Module '" . $moduleName . "' not found");
      }
    }
  }

  static public function hasLoaded ($moduleName) {
    Assert::isString($moduleName);

    if (in_array($moduleName, self::$_modulesLoaded) == TRUE) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  static public function moduleExists ($moduleName) {
    Assert::isString($moduleName);

    if (array_key_exists($moduleName, self::$_modules) == TRUE) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  static protected function _loadModuleHelper ($module, $setIncludePath, $runInitialiseFunction) {
    Assert::isArray($module);
    Assert::isBoolean($setIncludePath);
    Assert::isBoolean($runInitialiseFunction);

    // xxx get the list of module dependencies first and load them before loading the module
    // xxx implemented, set all module weights to 0 and make the field only have 3 digits
    // xxx need to make sure that if both modules depend on each other that one loads before the other based on
    // xxx their weight and then alphabetically

    if (is_dir(CMF_ROOT . $module['m_path']) == FALSE) {
      self::_diagnoseMissingModuleFile($module);
      // The diagnostic will prevent us getting executing any more in this method
    }

    if ($setIncludePath == TRUE) {
      set_include_path($module['m_path'] . PATH_SEPARATOR . get_include_path());
    }

    if ($runInitialiseFunction == TRUE && $module['m_initialise_function'] != '') {
      call_user_func($module['m_initialise_function']);
    }
  }

  // Public because sometimes it is good to know what module depends on what
  static public function getModuleDependencies ($moduleName) {
    Assert::isString($moduleName);

    // xxx continue
  }

  static protected function _diagnoseMissingModuleFile () {
    // xxx if the file isn't there, see if we can find where it has gone and update the db
    // xxx otherwise display a graceful error message
  }

  static public function getModulesLoaded () {
    return self::$_modulesLoaded;
  }
}

?>