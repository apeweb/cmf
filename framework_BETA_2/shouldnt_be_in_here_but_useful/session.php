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

require_once(FRAMEWORK_PATH . 'data/registry' . EXT);
require_once(FRAMEWORK_PATH . 'file_system/path_library' . EXT);
require_once(FRAMEWORK_PATH . 'system/invalid_operation_exception' . EXT);

/**
 * The session class is a bit like a builder pattern, ensuring sessions are managed in the
 * correct sequence while also allowing various different session drivers to be used without
 * enforcing any particular pattern
 */
define('Session', 'Session');
class Session {
  static protected $_initialised = FALSE;

  static public function load () {
    // xxx in order to test the following, data sources need to be finished
    if (Registry::dataSource()->session_module != FALSE) {
      if (Path_Library::fileExists(Registry::dataSource()->session_module) == TRUE) {
        require_once(Path_Library::getAbsoluteFilePath(Registry::dataSource()->session_driver));
      }
      elseif (Path_Library::directoryExists(Registry::dataSource()->session_module)) {
        // get a list of PHP files only as the session could include other files
        $files = Path_Library::getFiles(Registry::dataSource()->session_module, '/^.+\.' . EXT . '/i');

        if (count($files) > 0) {
          foreach ($files as $file) {
            // Include is used here to allow errors to be handled opposed to stopping everything
            include($file);
          }
        }
      }

      if (class_exists(Registry::dataSource()->session_driver) == TRUE) {
        $class = new ReflectionClass(Registry::dataSource()->session_driver);
        if ($class->implementsInterface('iSession_Driver') == FALSE) {
          throw new Invalid_Operation_Exception('Resource is not type of iSession_Driver');
        }

        Session::$_initialised = call_user_func_array(array(Registry::dataSource()->session_driver, 'load'), func_get_args());

        if (Session::$_initialised == TRUE && Registry::dataSource()->session_autostart != FALSE && Session::exists() == FALSE) {
          Session::start();
        }

        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Start the session
   */
  static public function start () {
    if (Session::$_initialised == TRUE) {
      return call_user_func_array(array(Registry::dataSource()->session_driver, 'start'), func_get_args());
    }
  }

  /**
   * Check to see if the session exists
   */
  static public function exists () {
    if (Session::$_initialised == TRUE) {
      return call_user_func_array(array(Registry::dataSource()->session_driver, 'exists'), func_get_args());
    }
  }

  /**
   * Regenerate the session ID for security
   */
  static public function regenerate () {
    if (Session::$_initialised == TRUE) {
      return call_user_func_array(array(Registry::dataSource()->session_driver, 'regenerate'), func_get_args());
    }
  }

  /**
   * Delete the session
   */
  static public function destroy () {
    if (Session::$_initialised == TRUE) {
      return call_user_func_array(array(Registry::dataSource()->session_driver, 'destroy'), func_get_args());
    }
  }

  /**
   * Close the session
   */
  static public function close () {
    if (Session::$_initialised == TRUE) {
      return call_user_func_array(array(Registry::dataSource()->session_driver, 'close'), func_get_args());
    }
  }

  /**
   * Save the session data
   */
  static public function write () {
    if (Session::$_initialised == TRUE) {
      return call_user_func_array(array(Registry::dataSource()->session_driver, 'write'), func_get_args());
    }
  }

  /**
   * Remove any data and or files relating to expired sessions
   */
  static public function purge () {
    if (Session::$_initialised == TRUE) {
      return call_user_func_array(array(Registry::dataSource()->session_driver, 'purge'), func_get_args());
    }
  }
}

?>