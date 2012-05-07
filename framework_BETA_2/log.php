<?php

/**
 * Copyright (c) 2011 Ape Web Ltd.  All rights reserved.
 *
 * The use and distribution terms for this software are contained in the file
 * named license.txt, which can be found in the root of this distribution.
 * By using this software in any fashion, you are agreeing to be bound by the
 * terms of this license.
 *
 * You must not remove this notice, or any other, from this software.
 */

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Log {
  static protected $_threshold = -1;
  static protected $_logWriter = NULL;

  static public function writeEntry ($title, $message, $type, $level = Log_Level::information, $dump = array()) {
    Assert::isString($title);
    Assert::isString($message);
    Assert::isInteger($type);
    Assert::isInteger($level);
    Assert::isArray($dump);

    // If the log writer has been disabled, simply return
    if (self::$_logWriter === FALSE) {
      return;
    }

    // Don't log anything outside of the threshold
    if ((self::$_threshold < 0 || self::$_threshold & $type) == FALSE) {
      return;
    }

    $dump['Visitor Host Address'] = Request::visitorIpAddress();
    $dump['Timestamp'] = time();
    $dump['Request URL'] = $_SERVER['REQUEST_URI'];

    // xxx add a hook or event to include additional data such as the following...
    // xxx add username
    // xxx add referrer

    if (self::$_logWriter == NULL) {
      throw new RuntimeException('Log writer has not been set');
    }

    self::$_logWriter->write($title, $message, $type, $level, $dump);
  }

  static public function setLogWriter (iLog $logWriter) {
    Assert::isObject($logWriter);
    self::$_logWriter = $logWriter;
  }

  static public function disableCurrentLogWriter () {
    self::$_logWriter = FALSE;
  }

  static public function getLogWriter () {
    return self::$_logWriter;
  }

  // Log::setThreshold(Log_Type::runtime | Log_Type::debug);
  static public function setThreshold ($threshold = NULL) {
    Assert::isInteger($threshold, TRUE);
    self::$_threshold = $threshold;
  }
  
  static public function getThreshold () {
    return self::$_threshold;
  }
}

?>