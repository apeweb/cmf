<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

interface iConfig_Driver extends iDriver {
  public function setValue($key, $value);
  public function deleteValue($key);
  public function getValue($key);
  public function valueExists($key);
}

?>