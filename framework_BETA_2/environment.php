<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

// xxx make wrapper for $_ENV

class Environment {
  static public function isCommandLine () {
    return (isset($_SERVER['SERVER_SOFTWARE']) == FALSE && (PHP_SAPI == 'cli' || (is_numeric($_SERVER['argc']) && $_SERVER['argc'] > 0)));
  }
}

?>