<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

require_once(ROOT . 'framework/control/form_control/abstract.php');

define('Label', NULL);

class Label extends Form_Control {
  #Region "public variables"
  protected $_associatedControlId = '';
  protected $_text = '';
  #End Region

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

      case 'associatedControlId':
      case 'accessKey':

      case 'text':
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

      case 'associatedControlId':
      case 'accessKey':

      case 'text':
        if (is_object($value) == FALSE && is_array($value) == FALSE) {
          $this->{'_' . $variableName} = $value;
          return;
        }
        else {
          throw new Exception ("Conversion from '" . gettype($value) . "' to type 'string' in '" . $variableName . "' is not valid.");
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
      $this->_control = '<label';

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

      // for=idref
      if (trim($this->_associatedControlId) != '') {
        $this->_control .= ' for="' . $this->_associatedControlId . '"';
      }

      // accesskey=character
      if (trim($this->_accessKey) != '') {
        $this->_control .= ' accesskey="' . $this->_accessKey . '"';
      }

      $this->_control .= '>' . $this->_text . '</label>';
    }
  }
  #End Region
}

?>;
      }

      $this->_control .= '>' . $this->_text . '</label>';
    }
  }
  #End Region
}

?>