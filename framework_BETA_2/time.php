<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Time {
  static protected $_timezone = '';

  static public function setZone ($timezone) {
    date_default_timezone_set(self::$_timezone = $timezone);
  }

  static public function getZone () {
    if (self::$_timezone == '') {
      self::$_timezone = self::getDefaultZone();
    }

    return self::$_timezone;
  }

  static public function getDefaultZone () {
    if (ini_get('date.timezone') == NULL) {
      ini_set('date.timezone', 'Europe/London');
      // xxx log a warning
    }
    return date_default_timezone_get();
  }
}

?>