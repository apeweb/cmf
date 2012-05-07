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

final class Cmf_Application {
  const VERSION = '0.4.0.01 (BETA)';

  const PHASE_INITIALISE = 0;
  const PHASE_EXECUTE = 1;
  const PHASE_TERMINATE = 2;

  static private $_phase = NULL;

  static public function run () {
    self::initialise();
    self::execute();
    self::terminate();
  }

  /**
   * Used to inform helper classes whether the application has initialised
   * @return TRUE if the application has initialised or FALSE otherwise
   */
  static public function hasInitialised () {
    return (self::$_phase > self::PHASE_INITIALISE);
  }

  /**
   * Gets the current phase of the application
   * @return int the ID of the bootstrap phase currently at
   */
  static public function getPhase () {
    return self::$_phase;
  }

  /**
   * Wrapper for initialisation tasks
   */
  static public function initialise () {
    static $initialising = FALSE;

    // The phase should never go down but just in case it does we check for it anyway to prevent any errors
    if (self::$_phase < self::PHASE_INITIALISE) {
      throw new RuntimeException("CMF application cannot initialise, unknown run phase");
    }

    if ($initialising == TRUE) {
      throw new RuntimeException("CMF application already initialising");
    }

    if (self::$_phase > self::PHASE_INITIALISE) {
      throw new RuntimeException("CMF application already initialised");
    }

    $initialising = TRUE;

    self::$_phase = self::PHASE_INITIALISE;

    self::_initialisePaths();
    self::_initialiseAutoloader();
    self::_initialiseResponseBuffer();
    self::_initialiseErrorHandler();
    self::_initialiseLogging();
    self::_initialisePhpSettings();
    self::_initialisePhpVariables();
    self::_initialiseRequestSecurity();
    self::_initialiseSiteSettings();
    self::_initialiseAssert();
    self::_initialiseDatabase();
    self::_initialiseCmfRegistry();
    self::_initialiseModules();

    $initialising = FALSE;

    self::_phaseComplete();
  }

  static private function _initialisePaths () {
    // If the CMF_ROOT wasn't defined in the front controller, we must define for modules
    if (defined('CMF_ROOT') == FALSE) {
      define('CMF_ROOT', getcwd() . DIRECTORY_SEPARATOR);
    }

    // If the PHP_EXT wasn't defined in the front controller, we must define for modules
    if (defined('PHP_EXT') == FALSE) {
      define('PHP_EXT', '.php');
    }
  }

  /**
   * Initialise the autoloader so elements of the framework can be loaded
   */
  static private function _initialiseAutoloader () {
    // Make sure the current folder is the first path in the include path, then the framework folder
    $newIncludePath = '.' . PATH_SEPARATOR . dirname(realpath(__FILE__));
    // Any other folder in the include path apart from the current folder
    $currentIncludePaths = str_replace('.' . PATH_SEPARATOR, '', get_include_path());

    if ($currentIncludePaths != '') {
      $newIncludePath .= PATH_SEPARATOR . $currentIncludePaths;
    }

    set_include_path($newIncludePath);
    spl_autoload_extensions(PHP_EXT);
    spl_autoload_register();
  }

  static private function _initialiseResponseBuffer () {
    Response_Buffer::start('Response_Buffer_Event_Helper::notifyFlushEventObservers');
  }

  static private function _initialiseErrorHandler () {
    set_error_handler('Cmf_Exception_Handler::throwErrorException');
    set_exception_handler('Cmf_Exception_Handler::catchUncaught');
    error_reporting(E_ALL | error_reporting());
  }

  static private function _initialisePhpSettings () {
    ini_set('magic_quotes_runtime', '0');
    setlocale(LC_ALL, 'C');
  }

  // Fix for magic quotes
  static private function _initialisePhpVariables () {
    if ((bool) get_magic_quotes_gpc() == TRUE) {
      $_GET = Sanitise::stripMagicQuotes($_GET);
      $_POST = Sanitise::stripMagicQuotes($_POST);
      $_COOKIE = Sanitise::stripMagicQuotes($_COOKIE);
    }
  }

  // Initial bootstrap file logging, ESSENTIAL FOR LOGGING POTENTIAL ATTACKS DETECTED
  static private function _initialiseLogging () {
    Log::setLogWriter(new Cmf_Startup_Log_Writer);
  }

  // Generally help increase security but as a bonus also prevents CLI errors
  static private function _initialiseRequestSecurity () {
    Request::validateGlobals();
    Request::validateReferrer();
    Request::validateProtocol();
    Request::validatePort();
    Request::validateAcceptedEncoding();
    Request::validateHost();
    Request::validateUserAgent();
    Request::validateUrl();
  }

  static private function _initialiseSiteSettings () {
    $siteSettingsPath = Cmf_Settings::getSiteSettingsPath();

    if (is_file($siteSettingsPath) == FALSE) {
      Cmf_Settings::runSiteSettingsDiagnostic();
    }

    Cmf_Settings::loadSiteSettings();
  }
  
  static public function _initialiseAssert () {
    if (Config::getValue('site', 'inProduction') == FALSE) {
      Assert::enabled(TRUE);
    }
  }

  static private function _initialiseDatabase () {
    if (Cmf_Database::isConnected() == FALSE) {
      Cmf_Database::connect();
    }
  }

  static private function _initialiseCmfRegistry () {
    Cmf_Settings::loadRegistrySettings();
  }

  static private function _initialiseModules () {
    // This is where part 1 of the magic happens, all modules are loaded
    Cmf_Module_Manager::initialise();
  }

  static private function _phaseComplete () {
    ++self::$_phase;
  }

  static public function execute () {
    static $executing = FALSE;

    if (self::$_phase < self::PHASE_EXECUTE) {
      throw new RuntimeException("CMF application cannot execute, CMF not initialised");
    }

    if ($executing == TRUE) {
      throw new RuntimeException("CMF application already executing");
    }

    if (self::$_phase > self::PHASE_EXECUTE) {
      throw new RuntimeException("CMF application already executed");
    }

    $executing = TRUE;

    // This is where part 2 of the magic happens, all modules are invoked
    Event_Dispatcher::notifyObservers(Cmf_Application_Event::execute);

    $executing = FALSE;

    self::_phaseComplete();
  }

  /**
   * Performance logging, clean-ups, and other associated things to do. Do not
   * change the name of this to shutdown, exit or quit as the right term for
   * this method is terminate as it terminates the script ensuring all shutdown
   * functions and object destructors are executed.
   */
  static public function terminate () {
    // For rendering the page, caching the page, performing cleanup, adding devel output, crons, etc.
    // This happens BEFORE the output is sent to the browser so any errors can be printed to the screen
    Event_Dispatcher::notifyObservers(Cmf_Application_Event::terminate);

    // Send the response
    Response_Buffer::flush();
    Response::end();

    // Avoid any loops
    exit;
  }
}

?>