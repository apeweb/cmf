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

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Cmf_Text_Box_Control extends Cmf_Html_Element_Control {
  public $name = NULL; // input name
  private $_text = NULL; // input value
  public $textMode = 'singleLine'; // this renderer supports singleLine, multiLine and password

  public $readOnly = FALSE;
  public $disabled = FALSE;
  public $maxLength = NULL;

  public $rows = 2; // <textbox/> specific
  public $cols = 20; // <textbox/> specific

  public $fieldSize = NULL; // <input type="text"/> specific

  public $tabIndex = NULL;
  public $accessKey = NULL;
  
  public $renderDefaultValueOnly = FALSE;
  public $defaultValue = NULL;

  public function __set ($variableName, $value) {
    if ($variableName == 'text') {
      $this->_text = $value;
    }
    else {
      $this->$variableName = $value;
    }
  }

  public function __get ($variableName) {
    switch ($variableName) {
      // data value
      case 'text':
        $this->_collectPostBackData();
        return $this->{'_' . $variableName};

      default:
        // legacy error reporting
        trigger_error("Undefined variable: '" . $variableName . "()'", E_USER_NOTICE);
    }
  }

  static public function render (Cmf_Text_Box_Control $control) {
    if ($control->enabled == TRUE) {
      $control->_collectPostBackData();

      $control->textMode = strtolower($control->textMode);
      
      switch ($control->textMode) {
        case 'multiline':
          $control->_content = '<textarea';

          $control->_setCommonAttributes();

          $control->_content .= ' rows="' . $control->rows . '"';
          $control->_content .= ' cols="' . $control->cols . '"';

          if ($control->renderDefaultValueOnly == FALSE) {
            $control->_content .= '>' . htmlspecialchars($control->text) . '</textarea>';
          }
          else {
            $control->_content .= '>' . htmlspecialchars($control->defaultValue) . '</textarea>';
          }
          break;

        case 'password':
          $control->_content = '<input type="password"';

          $control->_setCommonAttributes();

          if ($control->text !== NULL) {
            $control->_content .= ' value=""';
          }

          if ($control->fieldSize !== NULL && $control->fieldSize > -1) {
            $control->_content .= ' size="' . $control->fieldSize . '"';
          }

          if ($control->maxLength !== NULL && $control->maxLength > -1) {
            if ($control->maxLength > $control->fieldSize) {
              $control->maxLength = $control->fieldSize;
            }
            $control->_content .= ' maxlength="' . $control->maxLength . '"';
          }

          $control->_content .= ' />';
          break;

        case 'singleline':
          $control->_content = '<input type="text"';

          $control->_setCommonAttributes();

          if (($control->renderDefaultValueOnly == FALSE) && trim($control->text) != '') {
            $control->_content .= ' value="' . htmlspecialchars($control->text) . '"';
          }
          elseif ($control->renderDefaultValueOnly == TRUE && trim($control->defaultValue) != '') {
            $control->_content .= ' value="' . htmlspecialchars($control->defaultValue) . '"';
          }

          if ($control->fieldSize !== NULL && $control->fieldSize > -1) {
            $control->_content .= ' size="' . $control->fieldSize . '"';
          }

          if ($control->maxLength !== NULL && $control->maxLength > -1) {
            if ($control->maxLength > $control->fieldSize) {
              $control->maxLength = $control->fieldSize;
            }
            $control->_content .= ' maxlength="' . $control->maxLength . '"';
          }

          $control->_content .= ' />';
          break;
        
        default:
          throw new Argument_Exception ("'" . $control->textMode . "' is not a valid text mode.");
      }

      return $control->_content;
    }
  }

  private function _collectPostBackData () {
    if (Request::method() == 'POST') {
      if (empty($this->name) == FALSE && isset($_POST[$this->name]) == TRUE) {
        // using FILTER_UNSAFE_RAW as the content should be filtered later
        $this->text = filter_input(INPUT_POST, $this->name, FILTER_UNSAFE_RAW);
      }
    }
  }
  
  private function _setCommonAttributes () {
    // core attributes
    if (trim($this->cssClass) != '') {
      $this->_content .= ' class="' . $this->cssClass . '"';
    }

    if (trim($this->id) != '') {
      $this->_content .= ' id="' . $this->id . '"';
    }

    if (trim($this->style) != '') {
      $this->content .= ' style="' . $this->style . '"';
    }

    if (trim($this->toolTip) != '') {
      $this->content .= ' title="' . $this->toolTip . '"';
    }

    // i18n attributes
    if (trim($this->lang) != '') {
      $this->_content .= ' lang="' . $this->lang . '" xml:lang="' . $this->lang . '"';
    }

    if (trim($this->direction) != '') {
      $this->_content .= ' dir="' . $this->direction . '"';
    }

    // all others
    if ($this->name != NULL) {
      $this->_content .= ' name="' . $this->name . '"';
    }

    if ($this->disabled == TRUE) {
      $this->_content .= ' disabled="disabled"';
    }

    if ($this->readOnly == TRUE) {
      $this->_content .= ' readonly="readonly"';
    }

    if ($this->tabIndex !== NULL && $this->tabIndex > -1) {
      $this->_content .= ' tabindex="' . $this->tabIndex . '"';
    }

    if (trim($this->accessKey) != '') {
      $this->_content .= ' accesskey="' . $this->accessKey . '"';
    }
  }
}

?>