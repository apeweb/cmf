<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}
 
class Cmf_Region_Control extends Cmf_Control {
  public static function render (Cmf_Region_Control $control) {
    foreach ($control->children as $child) {
      $child->process();
      $control->_content .= $child;
    }
  }
}

?>