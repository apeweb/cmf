<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

require_once(ROOT . 'framework/control/form_control/abstract.php');
require_once(ROOT . 'framework/security/sanitisation/filter.php');

define('Check_Box', NULL);

class Check_Box extends Form_Control {
  #Region "protected variables"
  protected $_checked = FALSE;
  protected $_name = '';
  protected $_value = '';

  protected $_defaultRenderedValue = FALSE;
  protected $_defaultValue = FALSE;
  #End Region

  #Region "private variables"
  private $_request = Request;
  #End Region

  public function __construct () {
    $this->_request = new Request;
  }

  // the parent class needs to handle the basic attributes and the error
  // reporting for those not set
  public function __get ($variableName) {
    switch ($variableName) {
      // basic attributes
      case 'cssClass':
      case 'id':
      case 'style':
      case 'toolTip':

      // i18n attributes
      case 'lang':
      case 'direction':

      case 'name':
      case 'accessKey':
      case 'value':

      case 'defaultRenderedValue':
        return $this->{'_' . $variableName};

      case 'checked':
        $this->_collectPostBackData();
        return $this->{'_' . $variableName};

      default:
        // legacy error reporting
        trigger_error('Undefined variable: ' . $variableName . '()', E_USER_NOTICE);
    }
  }

  public function __set ($variableName, $value) {
    switch ($variableName) {
      // basic attributes
      case 'cssClass':
      case 'id':
      case 'style':
      case 'toolTip':

      // i18n attributes
      case 'lang':
      case 'direction':

      case 'name':
      case 'accessKey':
      case 'value':

        if (is_object($value) == FALSE && is_array($value) == FALSE) {
          $this->{'_' . $variableName} = $value;
          return;
        }
        else {
          throw new Exception ("Conversion from '" . gettype($value) . "' to type 'string' in '" . $variableName . "' is not valid.");
        }

      case 'defaultRenderedValue':
        if (is_bool($value) == TRUE || (is_numeric($value) == TRUE && is_string($value) == FALSE)) {
          $this->{'_' . $variableName} = $value;
          return;
        }
        else {
          throw new Exception ("Conversion from '" . gettype($value) . "' to type 'boolean' in '" . $variableName . "' is not valid.");
        }

      case 'checked':
        if (is_bool($value) == TRUE || (is_numeric($value) == TRUE && is_string($value) == FALSE)) {
          $this->_checked = $this->_defaultValue = $value;
          return;
        }
        else {
          throw new Exception ("Conversion from '" . gettype($value) . "' to type 'boolean' in '" . $variableName . "' is not valid.");
        }

      default:
        // legacy error reporting
        throw new Exception ("'" . $variableName . "' is not a member of '" . __CLASS__ . "'");
    }
  }

  #Region "magic methods"
  public function __toString () {
    $this->_build();
    return $this->_control;
  }
  #End Region

  #Region "protected methods"
  protected function _build () {
    if ($this->_enabled == TRUE) {
      $this->_control = '<input type="checkbox"';

      // core attibutes
      if (trim($this->_cssClass) != '') {
        $this->_control .= ' class="' . $this->_cssClass . '"';
      }
      if (trim($this->_id) != '') {
        $this->_control .= ' id="' . $this->_id . '"';
      }
      if (trim($this->_style) != '') {
        $this->_control .= ' style="' . $this->_style . '"';
      }
      if (trim($this->_toolTip) != '') {
        $this->_control .= ' title="' . $this->_toolTip . '"';
      }

      // i18n attributes
      if (trim($this->_lang) != '') {
        $this->_control .= ' lang="' . $this->_lang . '" xml:lang="' . $this->_lang . '"';
      }
      if (trim($this->_direction) != '') {
        $this->_control .= ' dir="' . $this->_direction . '"';
      }

      if (trim($this->_name) != '') {
        $this->_control .= ' name="' . $this->_name . '"';
      }

      if (trim($this->_value) != '') {
        $this->_control .= ' value="' . $this->_value . '"';
      }

      $this->_collectPostBackData();
      if ($this->_defaultRenderedValue == FALSE && $this->_checked == TRUE) {
        $this->_control .= ' checked="checked"';
      }
      elseif ($this->_defaultRenderedValue == TRUE && $this->_defaultValue == TRUE) {
        $this->_control .= ' checked="checked"';
      }

      // accesskey=character
      if (trim($this->_accessKey) != '') {
        $this->_control .= ' accesskey="' . $this->_accessKey . '"';
      }

      $this->_control .= ' />';
    }
  }

  // xxx a checkbox can still have a value if selected so need to fix this
  protected function _collectPostBackData () {
    if ($this->_request->method == 'POST') {
      if (empty($this->_name) == FALSE && isset($_POST[$this->_name]) == TRUE) {
        $this->_value = Filter::input($this->_name);
        if (trim($this->_value) != '') {
          $this->_checked = TRUE;
        }
        else {
          $this->_checked = FALSE;
        }
      }
      else {
        $this->_checked = FALSE;
      }
    }
  }
  #End Region
}

?>