<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Cmf_Label_Control extends Cmf_Html_Element_Control {
  public $for = NULL;
  public $text = NULL;

  static public function render (Cmf_Label_Control $control) {
    if ($control->enabled == TRUE) {
      $control->_content = '<label';

      if (trim($control->for) != '') {
        $control->_content .= ' for="' . $control->for . '"';
      }

      $control->_setCommonAttributes();

      $control->_content .= '>';

      if (trim($control->text) != '') {
        $control->_content .= $control->text;
      }

      foreach ($control->children as $child) {
        $child->process();
        $control->_content .= $child->getContent();
      }

      $control->_content .= '</label>';

      return $control->_content;
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