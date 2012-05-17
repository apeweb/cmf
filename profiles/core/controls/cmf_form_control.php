<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}
 
class Cmf_Form_Control extends Cmf_Control {
  public $action = '';
  public $method = 'post';

  public static function render (Cmf_Form_Control $control) {
    $control->_content = '<form';

    // Required
    $control->_content .= ' method="' . $control->method . '"';
    $control->_content .= ' action="' . $control->action . '"';

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

    $control->_content .= ">";

    // Children
    foreach ($control->children as $child) {
      $child->process();
      $control->_content .= $child->getContent();
    }

    $control->_content .= '</form>';
  }
}

?>