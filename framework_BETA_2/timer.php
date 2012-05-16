<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Timer {
  private static $_timers = array();

  public static function start ($name) {
    self::$_timers[$name]['start'] = microtime(TRUE);
    self::$_timers[$name]['count'] = isset(self::$_timers[$name]['count']) ? ++self::$_timers[$name]['count'] : 1;
  }

  public static function getTime ($name) {
    if (isset(self::$_timers[$name]['start']) == TRUE) {
      $stop = microtime(TRUE);
      $diff = $stop - self::$_timers[$name]['start'];

      if (isset(self::$_timers[$name]['time']) == TRUE) {
        $diff += self::$_timers[$name]['time'];
      }
      return $diff;
    }
    elseif (isset(self::$_timers[$name]['time']) == TRUE) {
      return self::$_timers[$name]['time'];
    }
    return NULL;
  }

  public static function getSeconds ($name) {
    return round(Timer::getTime($name) * 100, 2);
  }

  public static function stop ($name) {
    if (isset(self::$_timers[$name]['start']) == TRUE) {
      $stop = microtime(TRUE);
      $diff = $stop - self::$_timers[$name]['start'];
      if (isset(self::$_timers[$name]['time'])) {
        self::$_timers[$name]['time'] += $diff;
      }
      else {
        self::$_timers[$name]['time'] = $diff;
      }
      unset(self::$_timers[$name]['start']);

      return self::$_timers[$name]['time'];
    }
    elseif (isset(self::$_timers[$name]['time']) == TRUE) {
      return self::$_timers[$name]['time'];
    }
    return NULL;
  }

  // xxx create a function to work out the time difference between $_SERVER['REQUEST_TIME'] and now
}

?>