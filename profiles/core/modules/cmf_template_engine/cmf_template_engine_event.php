<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

final class Cmf_Template_Engine_Event extends Enum {
  const buildContent = 'Cmf_Template_Engine::buildContent';
  const modifyContent = 'Cmf_Template_Engine::modifyContent';
}

?>