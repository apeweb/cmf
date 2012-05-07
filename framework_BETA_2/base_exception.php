<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

abstract class Base_Exception extends Exception {
  public function __construct ($message = NULL, $code = 0) {
    if (!$message) {
      throw new $this('Unknown '. get_class($this));
    }
    parent::__construct($message, $code);
  }

  public function __toString () {
    return get_class($this) . " '" . $this->message . "' in " . $this->file . "(" . $this->line . ")\n" . $this->getTraceAsString();
  }
}

?>