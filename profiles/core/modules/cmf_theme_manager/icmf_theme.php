<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

// xxx make loading the CSS a module, make loading the JS a module and so on...

define('iCmf_Theme', 'iCmf_Theme');
interface iCmf_Theme {
  static public function getTemplateEngineClassName();
  static public function getMasterTemplateFileName();
  static public function getMasterTemplatePath();
  static public function getThemePath();
  static public function getCssFiles();
  static public function getJsFiles();
  static public function getRegions();
}

?>