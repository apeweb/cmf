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

final class Mvc_Application {
  const VERSION = '0.3.2.23 (BETA)';
  const CODENAME = 'XF';

  static private $_initialised = FALSE;
  static private $_modules = array();

  static public function run () {
    self::_initialise();
    self::_execute();
    self::terminate();
  }

  static private function _initialise () {
    echo xdebug_time_index();
    exit;

    if (self::$_initialised == TRUE) {
      return;
    }

    self::_checkPrerequisites();
    Event_Handler::notifyObservers(Application_Event::init);

    self::$_initialised = TRUE;
  }

  /**
   * Creates the controller
   */
  static private function _execute () {
    if (self::$_initialised == FALSE) {
      return;
    }

    Event_Handler::notifyObservers(Application_Event::execute);
  }

  /**
   * Performance logging, clean-ups, and other associated things to do. Do not
   * change the name of this to shutdown, exit or quit as the right term for
   * this method is terminate as it terminates the script ensuring all shutdown
   * functions and object destructors are executed.
   */
  static public function terminate () {
    Event_Handler::notifyObservers(Application_Event::terminate);
    exit;
  }

  /**
   * Checks to see if the constants that are required to be defined have been
   */
  static private function _checkPrerequisites () {
    if (defined('EXT') == FALSE) {
      throw new Mvc_Exception(Literal::getPhrase('PHP extension not defined', 'native errors'));
    }
    if (defined('FRAMEWORK_PATH') == FALSE) {
      throw new Mvc_Exception(Literal::getPhrase('Framework path not defined', 'native errors'));
    }
    if (defined('APPLICATION_PATH') == FALSE) {
      throw new Mvc_Exception(Literal::getPhrase('Application path not defined', 'native errors'));
    }
  }
}

?>