<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

require 'cmf_button_control_type' . PHP_EXT;

class Cmf_Button_Control extends Cmf_Html_Element_Control {
  public $name = '';
  public $value = '';
  public $type = Cmf_Button_Control_Type::submit;

  public $clickable = TRUE;
  public $accessKey = NULL;

  public $imageUrl = NULL;
  public $alternativeText = NULL;
  public $imageWidth = NULL;
  public $imageHeight = NULL;

  static public function render (Cmf_Button_Control $control) {
    if ($control->enabled == TRUE) {
      $control->_content = '<input';

      switch ($control->type) {
        case Cmf_Button_Control_Type::submit:
          $control->_content .= ' type="submit"';
          break;

        case Cmf_Button_Control_Type::image:
          $control->_content .= ' type="image"';

          $control->_content .= ' src="' . $control->imageUrl . '"';

          $control->_content .= ' alt="' . $control->alternativeText . '"';

          if ($control->imageWidth > -1) {
            $control->_content .= ' width="' . $control->imageWidth . '"';
          }

          if ($control->imageHeight > -1) {
            $control->_content .= ' width="' . $control->imageHeight . '"';
          }

          break;

        case Cmf_Button_Control_Type::button:
          $control->_content .= ' type="button"';
          break;

        case Cmf_Button_Control_Type::reset:
          $control->_content .= ' type="reset"';
          break;
      }

      // core attibutes
      if (trim($control->cssClass) != '') {
        $control->_content .= ' class="' . $control->cssClass . '"';
      }
      if (trim($control->id) != '') {
        $control->_content .= ' id="' . $control->id . '"';
      }
      if (trim($control->style) != '') {
        $control->_content .= ' style="' . $control->style . '"';
      }
      if (trim($control->toolTip) != '') {
        $control->_content .= ' title="' . $control->toolTip . '"';
      }

      // i18n attributes
      if (trim($control->lang) != '') {
        $control->_content .= ' lang="' . $control->lang . '" xml:lang="' . $control->lang . '"';
      }
      if (trim($control->direction) != '') {
        $control->_content .= ' dir="' . $control->direction . '"';
      }

      if (trim($control->name) != '') {
        $control->_content .= ' name="' . $control->name . '"';
      }

      if ($control->clickable == FALSE) {
        $control->_content .= ' disabled="disabled"';
      }

      if (trim($control->value) != '') {
        $control->_content .= ' value="' . $control->value . '"';
      }

      if (trim($control->accessKey) != '') {
        $control->_content .= ' accesskey="' . $control->accessKey . '"';
      }

      $control->_content .= ' />';
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
      $this->_content .= ' style="' . $this->style . '"';
    }

    if (trim($this->toolTip) != '') {
      $this->_content .= ' title="' . $this->toolTip . '"';
    }

    // i18n attributes
    if (trim($this->lang) != '') {
      $this->_content .= ' lang="' . $this->lang . '" xml:lang="' . $this->lang . '"';
    }

    if (trim($this->direction) != '') {
      $this->_content .= ' dir="' . $this->direction . '"';
    }
  }
}

?>