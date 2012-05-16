<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

interface iLanguage_Driver extends iDriver {
  public static function phrase($key); // return a string based on the language
  public static function phraseExists($key); // returns whether a phrase exists for the key specified
}

?>