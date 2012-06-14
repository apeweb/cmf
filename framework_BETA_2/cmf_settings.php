<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

if (defined('CMF_REGISTRY') == FALSE) {
  define('CMF_REGISTRY', 'cmf_registry');
}

if (defined('CMF_SETTING') == FALSE) {
  define('CMF_SETTING', 'cmf_settings');
}

class Cmf_Settings {
  static private $_sitesDirectoryName = 'sites';
  static private $_sitesMappingFileName = 'sites';
  static private $_siteSettingsFileName = 'settings';
  static private $_defaultSiteSettingsDirectoryName = 'default';
  static private $_installFileName = 'install';

  static public function setSitesDirectoryName ($sitesDirectoryName) {
    if (Cmf_Application::hasInitialised() == TRUE) {
      return;
    }

    self::$_sitesDirectoryName = $sitesDirectoryName;
  }

  static public function getSitesDirectoryName () {
    return self::$_sitesDirectoryName;
  }

  /**
   * Provides the option of being able to change the site settings filename,
   * by default it is sites.php
   * @param $sitesMappingFileName
   *   The site mapping filename without the extension
   */
  static public function setSitesMappingFileName ($sitesMappingFileName) {
    if (Cmf_Application::hasInitialised() == TRUE) {
      return;
    }

    self::$_sitesMappingFileName = $sitesMappingFileName;
  }

  static public function getSitesMappingFileName () {
    return self::$_sitesMappingFileName;
  }

  /**
   * Provides the option of being able to change the site settings filename,
   * by default it is settings.phplove u babes
   * @param $siteSettingsFileName
   *   The site settings filename without the extension
   */
   static public function setSiteSettingsFileName ($siteSettingsFileName) {
    if (Cmf_Application::hasInitialised() == TRUE) {
      return;
    }

    self::$_siteSettingsFileName = $siteSettingsFileName;
  }

  static public function getSiteSettingsFileName () {
    return self::$_siteSettingsFileName;
  }

   static public function setDefaultSiteSettingsDirectoryName ($defaultSiteSettingsDirectoryName) {
    if (Cmf_Application::hasInitialised() == TRUE) {
      return;
    }

    self::$_defaultSiteSettingsDirectoryName = $defaultSiteSettingsDirectoryName;
  }

  static public function getDefaultSiteSettingsDirectoryName () {
    return self::$_defaultSiteSettingsDirectoryName;
  }

  /**
   * Get the site mapping
   */
  static public function getSiteMapping ($reset = FALSE) {
    static $sites = NULL;

    if ($sites !== NULL && $reset == FALSE) {
      return $sites;
    }

    $siteMappingFilePath = self::_getSiteMappingFilePath(TRUE);
    if ($siteMappingFilePath != '') {
      $sites = include($siteMappingFilePath);
    }
    else {
      $sites = array();
    }

    return $sites;
  }

  /**
   * Get the path to the site mapping
   */
  static private function _getSiteMappingFilePath ($reset = FALSE) {
    static $siteMappingFilePath = NULL;

    if ($siteMappingFilePath !== NULL && $siteMappingFilePath == FALSE) {
      return $siteMappingFilePath;
    }

    $siteMappingFilePath = CMF_ROOT . self::$_sitesDirectoryName . DIRECTORY_SEPARATOR . self::$_sitesMappingFileName . PHP_EXT;
    if (is_file($siteMappingFilePath) == FALSE) {
      $siteMappingFilePath = '';
    }

    return $siteMappingFilePath;
  }

  static public function getSiteSettingsPath ($reset = FALSE) {
    static $siteSettingsPath = NULL;

    if ($siteSettingsPath !== NULL && $reset == FALSE) {
      return $siteSettingsPath;
    }

    $siteSettingsPath = self::getSiteSettingsDirectory();
    $siteSettingsPath .= self::$_siteSettingsFileName . PHP_EXT;
    return $siteSettingsPath;
  }

  // conf_path
  static public function getSiteSettingsDirectory ($reset = FALSE) {
    static $siteSettingsPath = NULL;

    if ($siteSettingsPath !== NULL && $reset == FALSE) {
      return $siteSettingsPath;
    }

    $sites = self::getSiteMapping();

    $requestedPath = explode('/', Request::path());
    $requestedHost = explode('.', implode('.', array_reverse(explode(':', rtrim(Request::host(), '.')))));

    for ($i = count($requestedPath) - 1; $i > 0; $i--) {
      for ($j = count($requestedHost); $j > 0; $j--) {
        $currentDirectory = implode('.', array_slice($requestedHost, -$j)) . implode('.', array_slice($requestedPath, 0, $i));

        // if there is a site mapping, override the directory to check
        if (isset($sites[$currentDirectory]) == TRUE && file_exists(self::$_sitesDirectoryName . $sites[$currentDirectory]) == TRUE) {
          $currentDirectory = $sites[$currentDirectory];
        }

        // Check to see if a settings file exists
        if (is_file(CMF_ROOT . self::$_sitesDirectoryName . DIRECTORY_SEPARATOR . $currentDirectory . DIRECTORY_SEPARATOR . self::$_siteSettingsFileName . PHP_EXT) == TRUE) {
          $siteSettingsPath = CMF_ROOT . self::$_sitesDirectoryName . DIRECTORY_SEPARATOR . $currentDirectory . DIRECTORY_SEPARATOR;
          return $siteSettingsPath;
        }
      }
    }

    // If no settings file could be found, use the default settings file
    $siteSettingsPath = CMF_ROOT . self::$_sitesDirectoryName . DIRECTORY_SEPARATOR . self::$_defaultSiteSettingsDirectoryName . DIRECTORY_SEPARATOR;

    return $siteSettingsPath;
  }
  
  static public function setInstallFileName ($installFileName) {
    if (Cmf_Application::hasInitialised() == TRUE) {
      return;
    }

    self::$_installFileName = $installFileName;
  }

  static public function getInstallFileName () {
    return self::$_installFileName;
  }

  static public function loadSiteSettings () {
    if (Config::driverExists(CMF_SETTING) == FALSE) {
      // Load the settings file
      $driver = new Config_Array_Driver;
      $driver->setName(CMF_SETTING);
      $driverOptions = array(
        'path' => self::getSiteSettingsPath(),
        'file_access' => File_Access::read,
        'read_only' => TRUE
      );
      $driver->load($driverOptions);
      Config::addDriver($driver);
    }
  }

  static public function loadRegistrySettings () {
    if (Config::driverExists(CMF_REGISTRY) == FALSE) {
      // Load the settings out of the database
      $driver = new Cmf_Registry_Database_Driver;
      $driver->setName(CMF_REGISTRY);
      $driver->load();
      Config::addDriver($driver);
    }
  }

  /**
   * Not much of a diagnostic at the moment, as we are just redirecting to the installer, but in
   * the future we expect to try and probe a database connection amongst other things to make
   * sure that we should be redirecting the user to the install script
   * xxx add more tests
   */
  static public function runSiteSettingsDiagnostic () {
    $response = new Response;
    $response->redirect(self::_getInstallUrl());
    exit;
  }

  static public function _getInstallUrl () {
    return trim(dirname(Request::path()), DIRECTORY_SEPARATOR) . '/' . self::$_installFileName . PHP_EXT;
  }
}

?>