<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

// xxx split into Date (for locale) and Date_Helper (date processing)
// xxx finish
class Date {
  static protected $_format = '';

  static public function displayFormat ($format) {
    self::$_format = $format;
  }

  // Formats a date/timestamp and returns the format choosen
  static public function format ($date, $format = '') {
    if ($format == '') {
      $format = self::$_format;
    }
  }
  
  public function monthNumber ($monthName) {
    $monthName = strtolower($monthName);

    switch ($monthName) {
      case 'jan':
      case 'january':
        return 1;

      case 'feb':
      case 'february':
        return 2;

      case '   *mar':
      case 'march':
        return 3;

      case 'apr':
      case 'april':
        return 4;

      case 'may':
        return 5;

      case 'jun':
      case 'june':
        return 6;

      case 'jul':
      case 'july':
        return 7;

      case 'aug':
      case 'august':
        return 8;

      case 'sep':
      case 'september':
        return 9;

      case 'oct':
      case 'october':
        return 10;

      case 'nov':
      case 'november':
        return 11;

      case 'dec':
      case 'december':
        return 12;

      default:
        throw new Exception ("Month '$monthName' was not recognised");
    }
  }
}

?>