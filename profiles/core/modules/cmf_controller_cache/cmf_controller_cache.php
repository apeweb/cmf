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

// xxx need to add a cache manager which clears all caches by triggering an event that this listens on

class Cmf_Controller_Cache {
  const Prepared_Statement_Library = 'cmf_controller_cache_prepared_statement_library';

  public static function install () {
    Config::setValue(CMF_REGISTRY, 'cache', 'controller', 'rebuild_every_request', FALSE);
    Config::setValue(CMF_REGISTRY, 'cache', 'controller', 'built', TRUE);

    self::compileCache();
  }

  public static function initialise () {
    if (Config::getValue('cache', 'controller', 'rebuild_every_request') == TRUE) {
      self::rebuildCache();
    }
  }

  public static function getControllerPath ($controllerClass, $reset = FALSE) {
    static $cache = array();

    if ($reset == FALSE && isset($cache[$controllerClass])) {
      return $cache[$controllerClass];
    }

    $query = Cmf_Database::call('cmf_controller_cache_get_path', self::Prepared_Statement_Library);
    $query->bindValue(':cc_name', $controllerClass);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();

    $path = $query->fetchColumn();
    
    if ($path == FALSE) {
      throw new RuntimeException("Controller '{$controllerClass}' not found");
    }

    $cache[$controllerClass] = $path;

    return $path;
  }

  public static function controllerExists ($controllerClass, $controllerPath = '') {
    Assert::isString($controllerClass);
    Assert::isString($controllerPath);

    try {
      $path = self::getControllerPath($controllerClass);
    }
    catch (Exception $ex) {
      return FALSE;
    }

    if ($controllerPath == '' || $controllerPath == $path) {
      return TRUE;
    }

    return FALSE;
  }

  public static function addController ($controllerClass, $path) {
    Assert::isString($controllerClass);
    Assert::isString($path);

    if (self::controllerExists($controllerClass) == TRUE) {
      throw new RuntimeException("Controller '{$controllerClass}' already exists");
    }

    $path = preg_replace('#^' . CMF_ROOT . '#', '', $path);

    $query = Cmf_Database::call('cmf_controller_cache_add', self::Prepared_Statement_Library);
    $query->bindValue(':cc_name', $controllerClass);
    $query->bindValue(':cc_path', $path);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();
  }

  public static function updateControllerPath ($controllerClass, $path) {
    Assert::isString($controllerClass);
    Assert::isString($path);

    if (self::controllerExists($controllerClass) == FALSE) {
      throw new RuntimeException("Controller '{$controllerClass}' not found");
    }

    $path = preg_replace('#^' . CMF_ROOT . '#', '', $path);

    $query = Cmf_Database::call('cmf_controller_cache_update', self::Prepared_Statement_Library);
    $query->bindValue(':cc_name', $controllerClass);
    $query->bindValue(':cc_path', $path);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();
  }

  public static function removeController ($controllerClass) {
    Assert::isString($controllerClass);

    if (self::controllerExists($controllerClass) == FALSE) {
      throw new RuntimeException("Controller '{$controllerClass}' not found");
    }

    $query = Cmf_Database::call('cmf_controller_cache_remove', self::Prepared_Statement_Library);
    $query->bindValue(':cc_name', $controllerClass);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();
  }

  public static function getAllControllers () {
    $query = Cmf_Database::call('cmf_controller_cache_get_all', self::Prepared_Statement_Library);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function rebuildCache () {
    $existingControllers = array();
    $missingControllers = array();

    // Get the current cache
    foreach (self::getAllControllers() as $row) {
      if (is_file($row['cc_path']) == TRUE) {
        $existingControllers[$row['cc_name']] = $row['cc_path'];
      }
      else {
        $missingControllers[$row['cc_name']] = $row['cc_path'];
      }
    }

    // Loop through all files and update cache entries
    foreach (Dir::getFileSystemEntries(CMF_ROOT, '#.*_controller' . preg_quote(PHP_EXT) . '$#', TRUE) as $controllerPath) {
      $classes = self::_getPhpClasses($controllerPath);
      foreach ($classes as $className) {
        // Controller already exists both in the cache and in the file system
        if (isset($existingControllers[$className]) == TRUE) {
          continue;
        }

        // Controller exists in cache but path is incorrect
        if (isset($missingControllers[$className]) == TRUE) {
          self::updateControllerPath($className, $controllerPath);
          unset($missingControllers[$className]);
        }
        // The controller is new and should be added to the cache
        else {
          self::addController($className, $controllerPath);
        }
      }
    }

    // Remove all entries in the database for the controllers that couldn't be found
    foreach ($missingControllers as $missingController) {
      self::removeController($missingController);
    }
  }

  public static function compileCache () {
    self::clearCache();

    $siteSpecificDirectory = preg_replace('#^' . CMF_ROOT . '#', '', Cmf_Settings::getSiteSettingsDirectory());

    $query = Cmf_Database::call('cmf_controller_cache_add', self::Prepared_Statement_Library);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));

    foreach (Dir::getFileSystemEntries(CMF_ROOT, '#.*_controller' . preg_quote(PHP_EXT) . '$#', TRUE) as $controllerPath) {
      $controllerPath = preg_replace('#^' . CMF_ROOT . '#', '', $controllerPath);

      // Make sure that we don't include any controllers from within the wrong site
      if (preg_match('#^sites/#', $controllerPath) == TRUE && preg_match('#^' . $siteSpecificDirectory . '#', $controllerPath) == FALSE) {
        continue;
      }

      $classes = self::_getPhpClasses($controllerPath);
      foreach ($classes as $className) {
        $query->bindParam(':cc_name', $className);
        $query->bindParam(':cc_path', $controllerPath);
        $query->execute();
      }
    }
  }

  public static function clearCache () {
    Cmf_Database::call('cmf_controller_cache_truncate', self::Prepared_Statement_Library)->execute();
    Config::setValue(CMF_REGISTRY, 'cache', 'controller', 'built', FALSE);
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