<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

interface iDriver {
  public function setName($name);
  public function getName();
  public function setOption($name, $value, $merge = FALSE);
  public function getOption($name);
}

?>