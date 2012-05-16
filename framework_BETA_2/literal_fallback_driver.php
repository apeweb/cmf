<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Literal_Fallback_Driver extends Literal_Driver {
  public function getPhrase ($phrase, $group = '') {
    return $phrase;
  }

  public function phraseExists ($phrase, $group = '') {
    return TRUE;
  }

  public function load ($options) {
  }

  public function setOption ($name, $value, $merge = FALSE) {
  }

  public function getOption ($name) {
  }
}

?>