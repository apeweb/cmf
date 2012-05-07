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

// Specific to the CMF due to predefined constants
class Cmf_Application_Environment {
  /**
   * Get the path to this script without the filename
   * @return The path to this script without the filename
   */
  static protected function _getDirectory () {
    return dirname(realpath(__FILE__));
  }
  
  /**
   * Get the path to the front controller
   * @return The front controller path
   */
  static public function getFrontControllerPath () {
    return dirname(self::_getDirectory());
  }

  /**
   * Get the path to the core profile
   * @return The core profile path
   */
  static public function getCoreProfilePath () {
    return self::getFrontControllerPath() . DIRECTORY_SEPARATOR . 'profiles' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR;
  }

  static public function getRoot () {
    return CMF_ROOT;
  }
  
  static public function getPhpExtension () {
    return PHP_EXT;
  }
}

?>