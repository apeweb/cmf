<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

/**
 * Application event enum containing a list of events that the application
 * can trigger
 */
 // based on https://secure.wikimedia.org/wikipedia/en/wiki/Global.asax (not finished yet)
final class Mvc_Application_Event extends Enum {
  const init = 1;
  const execute = 2;
  const beginRequest = 3;
  const terminate = 4;
}

?>