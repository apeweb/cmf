<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

require_once(ROOT . 'framework/network.php');
require_once(ROOT . 'framework/control/form_control/abstract.php');
require_once(ROOT . 'framework/security/sanitisation.php');

define('Text_Box', NULL);

/**
 * BUG:
 * At present when you use $foo->text after a postback, the value returned
 * is the value posted back, if this has been changed then the postback
 * value continues to override the value that was set. Something needs to
 * be added to say whether the postback value has been modified or not and
 * if so the postback value should not be returned, the modified version
 * should be.
 *
 * This is the same with all form controls
 *
 * Something like:
 * $this->_textModified = FALSE;
 *
 * And then when... $x->text = 'x';
 * $this->_textModified = TRUE;
 */
class Text_Box extends Form_Control {
  #Region "protected variables"
  protected $_textMode = Text_Box_Mode::singeline;

  protected $_name = '';
  protected $_text = '';
  protected $_readOnly = FALSE;
  protected $_disabled = FALSE;
  protected $_maxLength = -1;

  protected $_rows = 2; // <textbox/> specific
  protected $_cols = 20; // <textbox/> specific

  protected $_fieldSize = -1; // <input type="text"/> specific

  /**
   * Setting this to TRUE will prevent posted back data to be used as the
   * default value when re-rendered
   */
  protected $_defaultRenderedValue = FALSE;
  protected $_defaultValue = NULL;
  #End Region

  #Region "private variables"
  private $_request = Request;
  #End Region

  #Region "magic methods"
  public function __construct () {
    $this->_request = new Request;
  }

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

      // framework attributes
      case 'enabled':
      case 'defaultRenderedValue':

      // html attributes
      case 'name':
      case 'accessKey':
      case 'tabIndex':

      // textbox settings
      case 'textMode':
      case 'readOnly':
      case 'disabled':
      case 'maxLength':
      case 'rows':
      case 'cols':
      case 'fieldSize':
        return $this->{'_' . $variableName};

      // data value
      case 'text':
        $this->_collectPostBackData();
        return $this->{'_' . $variableName};

      default:
        // legacy error reporting
        trigger_error("Undefined variable: '" . $variableName . "()'", E_USER_NOTICE);
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

      // html attributes
      case 'name':
      case 'accessKey':
        if (is_object($value) == FALSE && is_array($value) == FALSE) {
          $this->{'_' . $variableName} = $value;
          return;
        }
        else {
          throw new Exception ("Conversion from '" . gettype($value) . "' to type 'string' in '" . $variableName . "' is not valid.");
        }

      // data value
      case 'text':
        if (is_object($value) == FALSE && is_array($value) == FALSE) {
          $this->_text = $this->_defaultValue = $value;
          return;
        }
        else {
          throw new Exception ("Conversion from '" . gettype($value) . "' to type 'string' in '" . $variableName . "' is not valid.");
        }

      // framework attributes
      case 'enabled':
      case 'defaultRenderedValue':

      // html attributes
      case 'readOnly':
      case 'disabled':
        if (is_bool($value) == TRUE || (is_numeric($value) == TRUE && is_string($value) == FALSE)) {
          $this->{'_' . $variableName} = $value;
          return;
        }
        else {
          throw new Exception ("Conversion from '" . gettype($value) . "' to type 'boolean' in '" . $variableName . "' is not valid.");
        }

      // html attributes
      case 'tabIndex':
      case 'maxLength':
      case 'rows':
      case 'cols':
      case 'fieldSize':
        if (is_numeric($value) == TRUE && is_string($value) == FALSE) {
          $this->{'_' . $variableName} = $value;
          return;
        }
        else {
          throw new Exception ("Conversion from '" . gettype($value) . "' to type 'integer' in '" . $variableName . "' is not valid.");
        }

      // textbox settings
      case 'textMode':
        $this->{'_' . $variableName} = $value;
        return;

      default:
        // legacy error reporting
        throw new Exception ("'" . $variableName . "' is not a member of '" . __CLASS__ . "'");
    }
  }

  public function __toString () {
    $this->_build();
    return $this->_control;
  }
  #End Region

  #Region "protected methods"
  protected function _build () {
    if ($this->_enabled == TRUE) {
      switch ($this->_textMode) {
        case Text_Box_Mode::multiline:
          $this->_control = '<textarea';

          $this->_setCommonAttributes();

          $this->_control .= ' rows="' . $this->_rows . '"';
          $this->_control .= ' cols="' . $this->_cols . '"';

          if ($this->_defaultRenderedValue == FALSE) {
            $this->_control .= '>' . htmlspecialchars($this->text) . '</textarea>';
          }
          else {
            $this->_control .= '>' . htmlspecialchars($this->_defaultValue) . '</textarea>';
          }
          break;

        case Text_Box_Mode::password:
          $this->_control = '<input type="password"';

          $this->_setCommonAttributes();

          if ($this->_fieldSize > -1) {
            $this->_control .= ' size="' . $this->_fieldSize . '"';
          }

          if ($this->_maxLength > -1) {
            if ($this->_maxLength > $this->_fieldSize) {
              $this->_maxLength = $this->_fieldSize;
            }
            $this->_control .= ' maxlength="' . $this->_maxLength . '"';
          }

          $this->_control .= ' />';
          break;

        case Text_Box_Mode::singeline:
          $this->_control = '<input type="text"';

          $this->_setCommonAttributes();

          if (($this->_defaultRenderedValue == FALSE) && trim($this->text) != '') {
            $this->_control .= ' value="' . htmlspecialchars($this->text) . '"';
          }
          elseif ($this->_defaultRenderedValue == TRUE && trim($this->_defaultValue) != '') {
            $this->_control .= ' value="' . htmlspecialchars($this->_defaultValue) . '"';
          }

          if ($this->_fieldSize > -1) {
            $this->_control .= ' size="' . $this->_fieldSize . '"';
          }

          if ($this->_maxLength > -1) {
            if ($this->_maxLength > $this->_fieldSize) {
              $this->_maxLength = $this->_fieldSize;
            }
            $this->_control .= ' maxlength="' . $this->_maxLength . '"';
          }

          $this->_control .= ' />';
          break;

        default:
          throw new Exception ("'" . $this->_textMode . "' is not a valid text mode.");
      }
    }
  }

  protected function _collectPostBackData () {
    if ($this->_request->method == 'POST') {
      if (empty($this->_name) == FALSE && isset($_POST[$this->_name]) == TRUE) {
        // using FILTER_UNSAFE_RAW as the content should be filtered later
        $this->_text = filter_input(INPUT_POST, $this->_name, FILTER_UNSAFE_RAW);
      }
    }
  }
  #End Region

  #Region "private methods"
  private function _setCommonAttributes () {
    // core attributes
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

    // all others
    if (trim($this->_name) != '') {
      $this->_control .= ' name="' . $this->_name . '"';
    }

    if ($this->_disabled == TRUE) {
      $this->_control .= ' disabled="disabled"';
    }

    if ($this->_readOnly == TRUE) {
      $this->_control .= ' readonly="readonly"';
    }

    if ($this->_tabIndex > -1) {
      $this->_control .= ' tabindex="' . $this->_tabIndex . '"';
    }

    if (trim($this->_accessKey) != '') {
      $this->_control .= ' accesskey="' . $this->_accessKey . '"';
    }
  }
  #End Region
}

class Text_Box_Mode {
  const singeline = 0;
  const multiline = 1;
  const password  = 2;
}

?>nd Region
}

class Text_Box_Mode {
  const singeline = 0;
  const multiline = 1;
  const password  = 2;
}

?>