<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

require_once(ROOT . 'framework/control/abstract.php');

define('Content_Control', NULL);

abstract class Content_Control extends Control {
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

  public function __get ($variableName) {
    switch ($variableName) {
      case 'cssClass':
      case 'id':
      case 'style':
      case 'toolTip':
      case 'lang':
      case 'direction':
      case 'accessKey':
      case 'tabIndex':
      case 'enabled':
        return $this->{'_' . $variableName};

      default:
        // legacy error reporting
        trigger_error("Undefined variable: '" . $variableName . "'", E_USER_NOTICE);
    }
  }

  // set stuff
  // core attributes should be moved to parent and then parent::__set
  // should be called
  public function __set ($variableName, $value) {
    switch ($variableName) {
      case 'cssClass':
      case 'id':
      case 'style':
      case 'toolTip':
      case 'lang':
      case 'direction':
      case 'accessKey':
        if (is_object($value) == FALSE && is_array($value) == FALSE) {
          $this->{'_' . $variableName} = $value;
          return;
        }
        else {
          throw new Exception ("Conversion from '" . gettype($value) . "' to type 'string' in '" . $variableName . "' is not valid.");
        }

      case 'tabIndex':
        if (is_numeric($value) == TRUE && is_string($value) == FALSE) {
          $this->{'_' . $variableName} = $value;
          return;
        }
        else {
          throw new Exception ("Conversion from '" . gettype($value) . "' to type 'integer' in '" . $variableName . "' is not valid.");
        }

      case 'enabled':
        if (is_bool($value) == TRUE || (is_numeric($value) == TRUE && is_string($value) == FALSE)) {
          $this->{'_' . $variableName} = $value;
          return;
        }
        else {
          throw new Exception ("Conversion from '" . gettype($value) . "' to type 'boolean' in '" . $variableName . "' is not valid.");
        }

      // need to be able to set variables on the page
      //default:
      //  throw new Exception ("'" . $variableName . "' is not a member of '" . __CLASS__ . "'");
    }

    $this->$variableName = $value;
  }

  protected function _commonAttributes () {
    $controlAttributes = '';

    // core attibutes
    if (trim($this->_cssClass) != '') {
      $controlAttributes .= ' class="' . $this->_cssClass . '"';
    }
    if (trim($this->_id) != '') {
      $controlAttributes .= ' id="' . $this->_id . '"';
    }
    if (trim($this->_style) != '') {
      $controlAttributes .= ' style="' . $this->_style . '"';
    }
    if (trim($this->_toolTip) != '') {
      $controlAttributes .= ' title="' . $this->_toolTip . '"';
    }

    // i18n attributes
    if (trim($this->_lang) != '') {
      $controlAttributes .= ' lang="' . $this->_lang . '" xml:lang="' . $this->_lang . '"';
    }
    if (trim($this->_direction) != '') {
      $controlAttributes .= ' dir="' . $this->_direction . '"';
    }

    return $controlAttributes;
  }

  public function load ($filename, $placeholder = '_control', $overload = FALSE) {
    $content = '';
    $control = '';

    if ($placeholder == FALSE || trim($placeholder) == '') {
      $placeholder = '_control';
    }

    if (isset($this->$placeholder)) {
      $control = $this->$placeholder;
      unset($this->$placeholder);
    }
    else {
      $control = NULL;
    }

    require($filename);

    if (isset($this->$placeholder) == TRUE && trim($this->$placeholder) != '') {
      $content = $this->$placeholder;
    }

    if ($overload == FALSE) {
      if ($control != NULL) {
        $this->$placeholder = $control;
      }
    }

    return $content;
  }
}

?>