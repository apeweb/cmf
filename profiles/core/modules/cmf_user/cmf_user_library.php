<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Cmf_User_Library {
  static public function install () {
    Config::setValue(CMF_REGISTRY, 'user', 'user_name', 'min_length', 2);

    // If you increase the max username length you must also change the structure of the field in the database to
    // support the increase in the username size
    Config::setValue(CMF_REGISTRY, 'user', 'user_name', 'max_length', 40);

    Config::setValue(CMF_REGISTRY, 'user', 'user_name', 'strict', FALSE);
    Config::setValue(CMF_REGISTRY, 'user', 'user_name', 'email_format', FALSE);
    Config::setValue(CMF_REGISTRY, 'user', 'password', 'min_length', 6);
    Config::setValue(CMF_REGISTRY, 'user', 'password', 'max_length', 20);
  }

  static public function validateUserName ($userName) {
    Assert::isString($userName);

    if (Config::getValue('user', 'user_name', 'email_format') == FALSE) {
      $label = 'username';
    }
    else {
      $label = 'e-mail address';
    }

    $userName = Filter::userName($userName);

    if (Validator::hasValue($userName) == FALSE) {
      return "You must enter a {$label}.";
    }
    elseif (strpos($userName, '  ') !== FALSE) {
      return "The {$label} cannot contain multiple spaces in a row.";
    }
    elseif (Config::getValue('user', 'user_name', 'strict') && preg_match('/[^\x{80}-\x{F7} a-z0-9@_.\'-]/i', $userName) == TRUE) {
      return "The {$label} contains one or more illegal characters.";
    }
    elseif (Config::getValue('user', 'user_name', 'email_format') && Validator::isValidEmailAddress($userName) == FALSE) {
      return "The {$label} contains one or more illegal characters.";
    }
    elseif (preg_match('/[\x{80}-\x{A0}' .    // Non-printable ISO-8859-1 + NBSP
                    '\x{AD}' .                // Soft-hyphen
                    '\x{2000}-\x{200F}' .     // Various space characters
                    '\x{2028}-\x{202F}' .     // Bidirectional text overrides
                    '\x{205F}-\x{206F}' .     // Various text hinting characters
                    '\x{FEFF}' .              // Byte order mark
                    '\x{FF01}-\x{FF60}' .     // Full-width latin
                    '\x{FFF9}-\x{FFFD}' .     // Replacement characters
                    '\x{0}-\x{1F}]/u',        // NULL byte and control characters
                    $userName) == TRUE) {
      return "The {$label} contains one or more illegal characters";
    }
    elseif (strlen($userName) < Config::getValue('user', 'user_name', 'min_length')) {
      return "The {$label} is too short, it must be " . Config::getValue('user', 'user_name', 'min_length') . " characters or more.";
    }
    elseif (strlen($userName) > Config::getValue('user', 'user_name', 'max_length')) {
      return "The {$label} is too long, it must be " . Config::getValue('user', 'user_name', 'max_length') . " characters or less.";
    }

    return '';
  }

  static public function validatePassword ($password) {
    if (Validator::hasValue($password) == FALSE) {
      return "You must enter a password.";
    }
    elseif (strlen($password) < Config::getValue('user', 'password', 'min_length')) {
      return "The password is too short, it must be " . Config::getValue('user', 'password', 'min_length') . " characters or more.";
    }
    elseif (strlen($password) > Config::getValue('user', 'password', 'max_length')) {
      return "The password is too long, it must be " . Config::getValue('user', 'password', 'max_length') . " characters or less.";
    }

    return '';
  }
}

?>