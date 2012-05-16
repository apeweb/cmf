<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

// xxx maintains a list of supported cultures and which is the current culture

class Culture_Info {
  const invariantCulture = '';

  private static $_currentCulture = NULL;

  public static function current () {
    if (self::$_currentCulture !== NULL) {
      return self::$_currentCulture;
    }
    else {
      return Culture_Info::invariantCulture;
    }
  }

  public static function setCulture ($culture) {
    // xxx need to verify culture exists?

    self::$_currentCulture = $culture;
  }
}

?>