<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Cmf_Button_Control_Type extends Enum {
  const submit = 0;
  const image = 1;
  const button  = 2;
  const reset  = 3;
}

?>