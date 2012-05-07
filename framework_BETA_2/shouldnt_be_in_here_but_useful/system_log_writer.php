<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

define('System_Log_Writer', 'System_Log_Writer');
class System_Log_Writer implements iLog_Writer {
  public function write ($log) {
    $backtrace = debug_backtrace();
    error_log('Framework Log: "' . json_encode($log) . '"');
    // Didn't use trigger_error as the line and file would be incorrect
    error_log('PHP Notice: No valid log writer found in ' . $backtrace[0]['file'] . ' on line ' . $backtrace[0]['line']);
  }
}

?>