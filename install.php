<?php

require_once('framework_BETA_2/cmf_application.php');

Cmf_Application::initialisePaths();
Cmf_Application::initialiseAutoloader();

foreach (Dir::getFileSystemEntries(CMF_ROOT, '#.*_controller' . preg_quote(PHP_EXT) . '$#', TRUE) as $controllerPath) {
  if (preg_match('#^sites/#', $controllerPath) == TRUE) {
    continue;
  }

  set_include_path(dirname(realpath($controllerPath)) . PATH_SEPARATOR . get_include_path());
}

foreach (Dir::getFileSystemEntries(CMF_ROOT, '#.*_control' . preg_quote(PHP_EXT) . '$#', TRUE) as $controlPath) {
  if (preg_match('#^sites/#', $controlPath) == TRUE) {
    continue;
  }

  set_include_path(dirname(realpath($controlPath)) . PATH_SEPARATOR . get_include_path());
}

foreach (Dir::getFileSystemEntries(CMF_ROOT, '#module\.ini#', TRUE) as $moduleIniPath) {
  if (preg_match('#^sites/#', $moduleIniPath) == TRUE) {
    continue;
  }

  set_include_path(dirname(realpath($moduleIniPath)) . PATH_SEPARATOR . get_include_path());
}

Install_Controller::run();

?>