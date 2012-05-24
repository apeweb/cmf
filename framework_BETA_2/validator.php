<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Validator {
  static public function hasValue ($subject) {
    $subject = trim($subject);

    if (strlen($subject) > 0 && ctype_cntrl($subject) == FALSE) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  /**
   *
   * @param bool $subject the string to check
   * @param bool $checkCase if TRUE, checks whether there are both upper and lowercase characters and returns FALSE if not
   * @return bool
   */
  static public function hasTextualValue ($subject, $checkCase = FALSE) {
    if (self::hasValue($subject) && ctype_punct($subject) == FALSE) {
      if ($checkCase == FALSE) {
        return TRUE;
      }
      elseif (ctype_lower($subject) == FALSE) {
        return TRUE;
      }
      else {
        return FALSE;
      }
    }
    else {
      return FALSE;
    }
  }

  static public function isNumeric ($subject) {
    if (ctype_digit($subject) == TRUE) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  static public function isPositive ($subject) {
    if (ctype_digit($subject) == TRUE && $subject > 0) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  static public function isValidEmailAddress ($subject) {
    return (bool) filter_var($subject, FILTER_VALIDATE_EMAIL);
  }

  static public function isValidPhpIdentifier ($subject) {
    if (preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/', $subject) == TRUE) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }
}

?>