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

require_once('framework_BETA_2/cmf_application.php');

Cmf_Application::initialise();

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