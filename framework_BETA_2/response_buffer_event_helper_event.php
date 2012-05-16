<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

// xxx rename to Response_Buffer_Helper_Event
final class Response_Buffer_Event_Helper_Event extends Enum {
  const preprocess = 'Response_Buffer_Event_Helper::preprocess';
  const flush = 'Response_Buffer_Event_Helper::flush';
}

?>