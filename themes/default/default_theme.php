<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

define('Default_Theme', 'Default_Theme');
class Default_Theme implements iCmf_Theme {
  // Template engine to use
  static private $_templateEngine = 'Cmf_Template_Engine';

  // The master template
  static private $_masterTemplate = 'template';

  // The regions within the template
  static private $_availableRegions = array(
    'header',
    'content',
    'footer'
  );

  // The paths to the files in the theme
  static private $_paths = array(
    CMF_THEME_CSS_PATH => 'css',
    CMF_THEME_JS_PATH => 'js'
  );

  static public function initialise () {
    Cmf_Theme_Manager::registerTheme(__CLASS__);
    // xxx template engine should be installed as a module, but not set to autoload
    Cmf_Module_Manager::loadModule(self::$_templateEngine);
  }

  static public function getTemplateEngineClassName () {
    return self::$_templateEngine;
  }

  static public function getMasterTemplateFileName () {
    return self::$_masterTemplate . PHP_EXT;
  }
  
  static public function getMasterTemplatePath () {
    return self::getThemePath() . self::$_masterTemplate . PHP_EXT;
  }

  static public function getThemePath () {
    return dirname(__FILE__) . DIRECTORY_SEPARATOR;
  }

  static public function getCssFiles () {
    return self::_getFiles(self::getThemePath() . self::$_paths[CMF_THEME_CSS_PATH], 'css');
  }

  static public function getJsFiles () {
    return self::_getFiles(self::getThemePath() . self::$_paths[CMF_THEME_JS_PATH], 'js');
  }

  static public function getRegions () {
    return self::$_availableRegions;
  }

  static public function _getFiles ($path, $ext) {
    static $files = NULL;
    
    if ($files !== NULL) {
      return $files;
    }
    else {
      $files = array();
      foreach (new DirectoryIterator($path) as $fileInfo) {
        if ($fileInfo->isDot() == TRUE || $fileInfo->isDir() == TRUE || $fileinfo->getExtension() != $ext) {
          continue;
        }
        $files[] = $fileInfo->getPathname();
      }
    }
    
    return $files;
  }
}

?>