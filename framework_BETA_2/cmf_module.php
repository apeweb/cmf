<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

abstract class Cmf_Module implements iCmf_Module {
  // Stores the required MVC routes and registry settings
  static public function install(){}
  // Remove stored routes and registry settings
  static public function uninstall(){}
  // Set what events to listen on, and what callbacks to use
  static public function initialise(){}
}

?>