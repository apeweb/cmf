<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

define('Control', NULL);

abstract class Control {
  #Region "protected variables"
  // framework attributes
  protected $_enabled = TRUE;
  #End Region

  protected $_control = '';

  public function __toString () {
    $this->_build();
    return $this->_control;
  }

  protected function _build () {
    if ($this->_enabled == TRUE) {
      $this->_control = '';
    }
  }
}

?>