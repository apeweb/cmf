<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Log_Type extends Enum {
  const security = 1; // Notify of security event, such as logon, a series of invalid logon attempts, etc.
  const http = 2; // 404, 403, etc errors
  const framework = 4; // Framework error
  const debug = 8; // Debug messages
  const runtime = 16; // Runtime errors
  const obsolete = 32; // Obsolete classes or methods used
  const audit = 64; // Audit log of user action
}

?>