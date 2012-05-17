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

    // Optional
    if ($control->cssClass != '') {
      $control->_content .= ' class="' . $control->cssClass . '"';
    }
    if ($control->id != '') {
      $control->_content .= ' id="' . $control->id . '"';
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