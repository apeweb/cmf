<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

// xxx need to add a cache manager which clears all caches by triggering an event that this listens on

class Cmf_Control_Cache {
  const Prepared_Statement_Library = 'cmf_control_cache_prepared_statement_library';

  public static function install () {
    Config::setValue(CMF_REGISTRY, 'cache', 'control', 'rebuild_every_request', FALSE);
    Config::setValue(CMF_REGISTRY, 'cache', 'control', 'built', TRUE);

    self::compileCache();
  }

  public static function initialise () {
    $paths = array();

    if (Config::getValue('cache', 'control', 'rebuild_every_request') == TRUE) {
      self::rebuildCache();
    }

    foreach (self::getAllControls() as $control) {
      $path = CMF_ROOT . dirname($control['ct_path']);
      $paths[$path] = $path;
    }

    $includePaths = implode($paths, PATH_SEPARATOR);

    if ($includePaths != '') {
      set_include_path($includePaths . PATH_SEPARATOR . get_include_path());
    }
  }

  public static function getControlPath ($controlClass, $reset = FALSE) {
    static $cache = array();

    if ($reset == FALSE && isset($cache[$controlClass])) {
      return $cache[$controlClass];
    }

    $query = Cmf_Database::call('cmf_control_cache_get_path', self::Prepared_Statement_Library);
    $query->bindValue(':ct_name', $controlClass);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();

    $path = $query->fetchColumn();
    
    if ($path == FALSE) {
      throw new RuntimeException("Control '{$controlClass}' not found");
    }

    $cache[$controlClass] = $path;

    return $path;
  }

  public static function controlExists ($controlClass, $controlPath = '') {
    Assert::isString($controlClass);
    Assert::isString($controlPath);

    try {
      $path = self::getControlPath($controlClass);
    }
    catch (Exception $ex) {
      return FALSE;
    }

    if ($controlPath == '' || $controlPath == $path) {
      return TRUE;
    }

    return FALSE;
  }

  public static function addControl ($controlClass, $path) {
    Assert::isString($controlClass);
    Assert::isString($path);

    if (self::controlExists($controlClass) == TRUE) {
      throw new RuntimeException("Control '{$controlClass}' already exists");
    }

    $path = preg_replace('#^' . CMF_ROOT . '#', '', $path);

    $query = Cmf_Database::call('cmf_control_cache_add', self::Prepared_Statement_Library);
    $query->bindValue(':ct_name', $controlClass);
    $query->bindValue(':ct_path', $path);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();
  }

  public static function updateControlPath ($controlClass, $path) {
    Assert::isString($controlClass);
    Assert::isString($path);

    if (self::controlExists($controlClass) == FALSE) {
      throw new RuntimeException("Control '{$controlClass}' not found");
    }

    $path = preg_replace('#^' . CMF_ROOT . '#', '', $path);

    $query = Cmf_Database::call('cmf_control_cache_update', self::Prepared_Statement_Library);
    $query->bindValue(':ct_name', $controlClass);
    $query->bindValue(':ct_path', $path);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();
  }

  public static function removeControl ($controlClass) {
    Assert::isString($controlClass);

    if (self::controlExists($controlClass) == FALSE) {
      throw new RuntimeException("Control '{$controlClass}' not found");
    }

    $query = Cmf_Database::call('cmf_control_cache_remove', self::Prepared_Statement_Library);
    $query->bindValue(':ct_name', $controlClass);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();
  }

  public static function getAllControls () {
    $query = Cmf_Database::call('cmf_control_cache_get_all', self::Prepared_Statement_Library);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function rebuildCache () {
    $existingControls = array();
    $missingControls = array();

    // Get the current cache
    foreach (self::getAllControls() as $row) {
      if (is_file($row['ct_path']) == TRUE) {
        $existingControls[$row['ct_name']] = $row['ct_path'];
      }
      else {
        $missingControls[$row['ct_name']] = $row['ct_path'];
      }
    }

    // Loop through all files and update cache entries
    foreach (Dir::getFileSystemEntries(CMF_ROOT, '#.*_control' . preg_quote(PHP_EXT) . '$#', TRUE) as $controlPath) {
      $classes = self::_getPhpClasses($controlPath);
      foreach ($classes as $className) {
        // Control already exists both in the cache and in the file system
        if (isset($existingControls[$className]) == TRUE) {
          continue;
        }

        // Control exists in cache but path is incorrect
        if (isset($missingControls[$className]) == TRUE) {
          self::updateControlPath($className, $controlPath);
          unset($missingControls[$className]);
        }
        // The control is new and should be added to the cache
        else {
          self::addControl($className, $controlPath);
        }
      }
    }

    // Remove all entries in the database for the controls that couldn't be found
    foreach ($missingControls as $missingControl) {
      self::removeControl($missingControl);
    }
  }

  public static function compileCache () {
    self::clearCache();

    $siteSpecificDirectory = preg_replace('#^' . CMF_ROOT . '#', '', Cmf_Settings::getSiteSettingsDirectory());

    $query = Cmf_Database::call('cmf_control_cache_add', self::Prepared_Statement_Library);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));

    foreach (Dir::getFileSystemEntries(CMF_ROOT, '#.*_control' . preg_quote(PHP_EXT) . '$#', TRUE) as $controlPath) {
      $controlPath = preg_replace('#^' . CMF_ROOT . '#', '', $controlPath);

      // Make sure that we don't include any controls from within the wrong site
      if (preg_match('#^sites/#', $controlPath) == TRUE && preg_match('#^' . $siteSpecificDirectory . '#', $controlPath) == FALSE) {
        continue;
      }

      $classes = self::_getPhpClasses($controlPath);
      foreach ($classes as $className) {
        $query->bindParam(':ct_name', $className);
        $query->bindParam(':ct_path', $controlPath);
        $query->execute();
      }
    }
  }

  public static function clearCache () {
    Cmf_Database::call('cmf_control_cache_truncate', self::Prepared_Statement_Library)->execute();
    Config::setValue(CMF_REGISTRY, 'cache', 'control', 'built', FALSE);
  }

  private static function _getPhpClasses ($path) {
    $script = file_get_contents($path);
    $classes = array();
    $tokens = token_get_all($script);
    $count = count($tokens);
    for ($i = 2; $i < $count; $i++) {
      if ($tokens[$i-2][0] == T_CLASS && $tokens[$i-1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING) {
        $className = $tokens[$i][1];
        $classes[] = $className;
      }
    }
    return $classes;
  }
}

?>