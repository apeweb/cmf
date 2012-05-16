<?php


if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

final class Cmf_Application_Event {
  const execute = 'Cmf_Application::_execute';
  const terminate = 'Cmf_Application::terminate';
}

?>