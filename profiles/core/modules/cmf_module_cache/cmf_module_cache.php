<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise amcess to this request externally.");
}

// xxx need to add a cache manager which clears all caches by triggering an event that this listens on

class Cmf_Module_Cache {
  const Prepared_Statement_Library = 'cmf_module_cache_prepared_statement_library';

  public static function install () {
    Config::setValue(CMF_REGISTRY, 'cache', 'module', 'rebuild_every_request', FALSE);
    self::compileCache();
  }

  public static function initialise () {
    if (Config::getValue('cache', 'module', 'rebuild_every_request') == TRUE) {
      self::rebuildCache();
    }
  }

  public static function getModulePath ($moduleName, $reset = FALSE) {
    static $cache = array();

    if ($reset == FALSE && isset($cache[$moduleName])) {
      return $cache[$moduleName];
    }

    $query = Cmf_Database::call('cmf_module_cache_get_path', self::Prepared_Statement_Library);
    $query->bindValue(':m_name', $moduleName);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();

    $path = $query->fetchColumn();
    
    if ($path == FALSE) {
      throw new RuntimeException("Module '{$moduleName}' not found");
    }

    $cache[$moduleName] = $path;

    return $path;
  }

  public static function moduleExists ($moduleName, $modulePath = '') {
    Assert::isString($moduleName);
    Assert::isString($modulePath);

    try {
      $path = self::getModulePath($moduleName);
    }
    catch (Exception $ex) {
      return FALSE;
    }

    if ($modulePath == '' || $modulePath == $path) {
      return TRUE;
    }

    return FALSE;
  }

  public static function addModule ($module) {
    Assert::isArray($module);

    // xxx check array has values expected, if not use a default value, ideally at some point the module should
    // become a Cmf_Module class or something

    if (self::moduleExists($module['system_name']) == TRUE) {
      throw new RuntimeException("Module '{$module['system_name']}' already exists");
    }

    if (isset($module['initialise_function']) == FALSE) {
      $module['initialise_function'] = '';
    }

    if (isset($module['default_weight']) == FALSE) {
      $module['default_weight'] = 0;
    }

    $path = preg_replace('#^' . CMF_ROOT . '#', '', $module['path']);

    $query = Cmf_Database::call('cmf_module_cache_add', self::Prepared_Statement_Library);
    $query->bindValue(':m_path', $path);
    $query->bindValue(':m_name', $module['system_name']);
    $query->bindValue(':m_initialise_function', $module['initialise_function']);
    $query->bindValue(':m_weight', $module['default_weight']);
    $query->bindValue(':m_ini', base64_encode(serialize($module)));
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();
  }

  public static function updateModulePath ($moduleName, $path) {
    Assert::isString($moduleName);
    Assert::isString($path);

    if (self::moduleExists($moduleName) == FALSE) {
      throw new RuntimeException("Module '{$moduleName}' not found");
    }

    $path = preg_replace('#^' . CMF_ROOT . '#', '', $path);

    $query = Cmf_Database::call('cmf_module_cache_update', self::Prepared_Statement_Library);
    $query->bindValue(':m_name', $moduleName);
    $query->bindValue(':m_path', $path);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();
  }

  public static function removeModule ($moduleName) {
    Assert::isString($moduleName);

    if (self::moduleExists($moduleName) == FALSE) {
      throw new RuntimeException("Module '{$moduleName}' not found");
    }

    $query = Cmf_Database::call('cmf_module_cache_remove', self::Prepared_Statement_Library);
    $query->bindValue(':m_name', $moduleName);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();
  }

  public static function getAllModules () {
    $query = Cmf_Database::call('cmf_module_cache_get_all', self::Prepared_Statement_Library);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function rebuildCache () {
    $existingModules = array();
    $missingModules = array();

    $siteSpecificDirectory = preg_replace('#^' . CMF_ROOT . '#', '', Cmf_Settings::getSiteSettingsDirectory());

    // Get the current cache
    foreach (self::getAllModules() as $row) {
      if (preg_match('#^sites/#', $row['m_path']) == TRUE && preg_match('#^' . $siteSpecificDirectory . '#', $row['m_path']) == FALSE) {
        continue;
      }

      if (is_dir($row['m_path']) == TRUE) {
        $existingModules[$row['m_name']] = $row['m_path'];
      }
      else {
        $missingModules[$row['m_name']] = $row['m_path'];
      }
    }

    foreach (Dir::getFileSystemEntries(CMF_ROOT, '#module\.ini#', TRUE) as $moduleIniPath) {
      $modulePath = dirname($moduleIniPath) . DIRECTORY_SEPARATOR;
      $module = parse_ini_file($moduleIniPath);
      $module['path'] = $modulePath;
      $module['system_name'] = self::getModuleSystemName($module['name']);

      // Make sure that we don't include any controllers from within the wrong site
      if (preg_match('#^sites/#', $moduleIniPath) == TRUE && preg_match('#^' . $siteSpecificDirectory . '#', $modulePath) == FALSE) {
        unset($existingModules[$module['system_name']]);
        unset($missingModules[$module['system_name']]);
        continue;
      }

      if (isset($existingModules[$module['system_name']]) == TRUE) {
        continue;
      }

      if (isset($missingModules[$module['system_name']]) == TRUE) {
        self::updateModulePath($module['system_name'], $modulePath);
        unset($missingModules[$module['system_name']]);
      }
      else {
        self::addModule($module);
      }
    }

    // xxx need some function for this
    if (Environment::isCommandLine() == FALSE) {
      $prefix = '[Warning]';
      $newline = '<br />';
    }
    else {
      if (DIRECTORY_SEPARATOR == '/') {
        $prefix = "\033[0;33m[Warning]\033[0m";
      }
      else {
        $prefix = '[Warning]';
      }

      $newline = "\r\n";
    }

    foreach ($missingModules as $missingModule) {
      echo $prefix . ' Module in database not found on filesystem: ' . $missingModule . $newline;
    }
  }

  public static function compileCache () {
    self::clearCache();

    $siteSpecificDirectory = preg_replace('#^' . CMF_ROOT . '#', '', Cmf_Settings::getSiteSettingsDirectory());

    foreach (Dir::getFileSystemEntries(CMF_ROOT, '#module\.ini#', TRUE) as $moduleIniPath) {
      // Make sure that we don't include any controllers from within the wrong site
      if (preg_match('#^sites/#', $moduleIniPath) == TRUE && preg_match('#^' . $siteSpecificDirectory . '#', $moduleIniPath) == FALSE) {
        continue;
      }

      $modulePath = dirname($moduleIniPath) . DIRECTORY_SEPARATOR;
      $module = parse_ini_file($moduleIniPath);
      $module['path'] = $modulePath;
      $module['system_name'] = self::getModuleSystemName($module['name']);

      self::addModule($module);
    }
  }

  public static function clearCache () {
    Cmf_Database::call('cmf_module_cache_truncate', self::Prepared_Statement_Library)->execute();
    Config::setValue(CMF_REGISTRY, 'cache', 'module', 'built', FALSE);
  }

  public static function getModuleSystemName ($moduleName) {
    // xxx replace international characters
    $moduleName = strtolower(str_replace(' ', '_', $moduleName));
    return preg_replace('[^a-z0-9_\x7f-\xff]', '', $moduleName);
  }
}

?>