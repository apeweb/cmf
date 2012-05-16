<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

abstract class Cmf_Html_Element_Control extends Cmf_Control {
  public $cssClass = NULL;
  public $id = NULL;
  public $style = NULL;
  public $toolTip = NULL;
  public $lang = NULL;
  public $direction = NULL;
}

?>

