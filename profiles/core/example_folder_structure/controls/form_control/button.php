<?php

if (!defined('ROOT')) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  error_log(__FILE__ . ' was possibly accessed directly so no ROOT could be found' . PHP_EOL, 3, 'error.log');
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

require_once(ROOT . 'framework/control/form_control/abstract.php');

define('Button', NULL);

class Button extends Form_Control {
  #Region "protected variables"
  protected $_name = '';
  protected $_value = '';
  protected $_type = Button_Type::submit;

  protected $_disabled = FALSE;

  protected $_imageUrl = '';
  protected $_alternativeText = '';
  protected $_imageWidth = -1;
  protected $_imageHeight = -1;
  #End Region

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

      case 'enabled':
      case 'disabled':

      case 'imageUrl':
      case 'alternativeText':
      case 'imageWidth':
      case 'imageHeight':
        return $this->{'_' . $variableName};

      case 'clicked':
        if (!empty($this->_name) && isset($_POST[$this->_name])) {
          return TRUE;
        }
        else {
          return FALSE;
        }

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

      case 'imageUrl':
      case 'alternativeText':
        if (is_object($value) == FALSE && is_array($value) == FALSE) {
          $this->{'_' . $variableName} = $value;
          return;
        }
        else {
          throw new Exception ("Conversion from '" . gettype($value) . "' to type 'string' in '" . $variableName . "' is not valid.");
        }

      case 'enabled':
      case 'disabled':
        if (is_bool($value) == TRUE || (is_numeric($value) == TRUE && is_string($value) == FALSE)) {
          $this->{'_' . $variableName} = $value;
          return;
        }
        else {
          throw new Exception ("Conversion from '" . gettype($value) . "' to type 'boolean' in '" . $variableName . "' is not valid.");
        }

      case 'imageWidth':
      case 'imageHeight':
        if (is_numeric($value) == TRUE && is_string($value) == FALSE) {
          $this->{'_' . $variableName} = $value;
          return;
        }
        else {
          throw new Exception ("Conversion from '" . gettype($value) . "' to type 'integer' in '" . $variableName . "' is not valid.");
        }

      case 'clicked':
        throw new Exception ("Property '" . $variableName . "' is read only.");

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
      $this->_control = '<input';

      switch ($this->_type) {
        case Button_Type::submit:
          $this->_control .= ' type="submit"';
          break;

        case Button_Type::image:
          $this->_control .= ' type="image"';

          $this->_control .= ' src="' . $this->_imageUrl . '"';

          $this->_control .= ' alt="' . $this->_alternativeText . '"';

          if ($this->_imageWidth > -1) {
            $this->_control .= ' width="' . $this->_imageWidth . '"';
          }

          if ($this->imageHeight > -1) {
            $this->_control .= ' width="' . $this->imageHeight . '"';
          }

          break;

        case Button_Type::button:
          $this->_control .= ' type="button"';
          break;

        case Button_Type::reset:
          $this->_control .= ' type="reset"';
          break;
      }

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

      if ($this->_disabled == TRUE) {
        $this->_control .= ' disabled="disabled"';
      }

      if (trim($this->_value) != '') {
        $this->_control .= ' value="' . $this->_value . '"';
      }

      // accesskey=character
      if (trim($this->_accessKey) != '') {
        $this->_control .= ' accesskey="' . $this->_accessKey . '"';
      }

      $this->_control .= ' />';
    }
  }
  #End Region
}

class Button_Type {
  const submit = 0;
  const image = 1;
  const button  = 2;
  const reset  = 3;
}

?>