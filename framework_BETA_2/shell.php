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

class Shell {
  /**
   * Runs a system command in the background, allowing PHP to continue executing
   *
   * This is a very unsafe method to use, and you are better off running
   * commands yourself or creating an endless loop in PHP that performs a task,
   *
   * @param string $cmd the command to run
   * @param string $log the location of the log file or leave blank to not log output
   * @param integer $priority -20 being the highest to 19 being the lowest
   */
  static public function execInBackground ($cmd, $log = '', $priority = 19) {
    // xxx windows version needs testing on windows
    if (substr(strtolower(php_uname()), 0, 3) == 'win'){
      switch ($priority) {
        case 19:
        case 18:
        case 17:
          $priority = '/LOW';
        break;

        case 16:
        case 15:
        case 14:
          $priority = '/BELOWNORMAL';
        break;

        case 13:
        case 12:
        case 11:
        case 10:
        case 9:
        case 8:
        case 7:
          $priority = '/NORMAL';
        break;

        case 6:
        case 5:
        case 4:
        case 3:
        case 2:
        case 1:
        case 0:
        case -1:
        case -2:
          $priority = '/ABOVENORMAL';
        break;

        case -3:
        case -4:
        case -5:
        case -6:
        case -7:
        case -8:
        case -9:
        case -10:
        case -11:
          $priority = '/HIGH';
        break;

        case -12:
        case -13:
        case -14:
        case -15:
        case -16:
        case -17:
        case -18:
        case -19:
        case -20:
          $priority = '/REALTIME';
        break;

        default:
          $priority = '';
      }

      if (trim($log) != '') {
        pclose(popen('start ' . escapeshellarg($priority) . ' /B ' . $cmd . ' > ' . escapeshellarg($log)), 'r');
      }
      else {
        pclose(popen('start ' . escapeshellarg($priority) . ' /B ' . $cmd), 'r');
      }
    }
    else {
      if (trim($log) != '') {
        exec('nice -n' . escapeshellarg(intval($priority)) . ' ' . $cmd . ' > ' . escapeshellarg($log) . ' &');
      }
      else {
        exec('nice -n' . escapeshellarg(intval($priority)) . ' ' . $cmd . ' > /dev/null &');
      }
    }
  }
}

?>