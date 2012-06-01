<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

interface iSession_Handler {
  public static function start();
  public static function exists();
  public static function destroy();
  public static function close();
  public static function write();
  public static function purge();
  public static function getStore();
}

?>