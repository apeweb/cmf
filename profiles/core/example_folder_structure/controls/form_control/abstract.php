<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

require_once(ROOT . 'framework/control/abstract.php');

define('Form_Control', NULL);

abstract class Form_Control extends Control {
  #Region "protected variables"
  protected $_cssClass = '';
  protected $_id = '';
  protected $_style = '';
  protected $_toolTip = ''; // title attribute

  // i18n attributes
  protected $_lang = '';
  protected $_direction = '';

  // keyboard attributes
  protected $_accessKey = '';
  protected $_tabIndex = -1;
}

?>
