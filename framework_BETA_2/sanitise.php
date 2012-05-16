<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Sanitise {
  public static function stripMagicQuotes ($value) {
    if ((bool) get_magic_quotes_gpc() == FALSE) {
      return $value;
    }

		if (is_array($value) == TRUE || is_object($value) == TRUE) {
			foreach ($value as $key => $val) {
				// Recursively clean each value
				$value[$key] = Sanitise::stripMagicQuotes($val);
			}
		}
		elseif (is_string($value) == TRUE) {
			$value = stripslashes($value);
		}

		return $value;
	}
}

?>