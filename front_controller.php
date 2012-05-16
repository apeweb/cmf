<?php

/**
 * This file ideally should not be configured unless you are having issues with the framework
 * incorrectly setting paths or not including the framework at all
 */

/**
 * Uncomment the following line if the framework is setting the CMF_ROOT incorrectly
 */
//define('CMF_ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR);

/**
 * If you use a different extension for your PHP files other than .php then uncomment the
 * following line and set the extension to use prefixed with a period as shown
 *
 * The default PHP extension, we don't support multiple extensions such as .inc and .php
 * because this could lead to rogue files being loaded if the server security is compromised
 * so we only allow one PHP extension to be used
 */
//define('PHP_EXT', '.php');

/**
 * Load the framework files, if you already have the framework and it is located in a different
 * location, or the file system you are using does not support the directory separator in use
 * then uncomment the following line and set it to what you need it to be, and after doing so
 * remove the following line
 */
//require_once(CMF_ROOT . DIRECTORY_SEPARATOR . 'framework_BETA_2' . DIRECTORY_SEPARATOR . 'cmf_application' . PHP_EXT);
require_once('framework_BETA_2/cmf_application.php');

/**
 * Run the CMF application
 */
Cmf_Application::run();

?>