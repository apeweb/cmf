<?php

require_once('framework_BETA_2/cmf_application.php');

Cmf_Application::initialise();

// xxx need to fix the error where we get:
// PHP Fatal error:  Class 'Cmf_Controller_Cache' not found in /disk3/www/aw_framework/cc.php on line 17

/* uncomment if we want to force this so the cc can only be ran in the commandline
if (Environment::isCommandLine() == FALSE) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}*/

// xxx not sure why the controller can't be found, need to check
//Admin_Controller::shared();

// xxx Cmf_Cache::clear(); should run all of the following
Cmf_Controller_Cache::compileCache(); // xxx shouldn't this be rebuild cache?
Cmf_Control_Cache::compileCache(); // xxx shouldn't this be rebuild cache?
Cmf_Module_Cache::rebuildCache();
echo "Cache cleared\r\n";

exit;

?>