<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Response {
  static private $_isEnding = FALSE;

  public static function setCookie (Cookie $cookie) {
    $field = explode(':', $cookie, 2);
    Response_Buffer::setHeader($field[0], ltrim($field[1]));
  }

  // xxx needs testing
  public static function removeCookie ($cookieName, $removeFromVisitorClient = FALSE) {
    $cookies = array();

    try {
      $cookies = Response_Buffer::getHeaders('Set-Cookie');
    }
    catch (RuntimeException $ex) {
      if ($removeFromVisitorClient == FALSE) {
        throw $ex;
      }
    }

    if (isset($ex) == FALSE) {
      foreach ($cookies as $cookie) {
        $currentCookieName = array_shift(explode('=', $cookie, 2));
        if ($currentCookieName == $cookieName) {
          // Remove from response buffer only
          if ($removeFromVisitorClient == FALSE) {
            Response_Buffer::deleteHeader('Set-Cookie', $cookie);
          }
          // Don't remove the header but modify it to send a header telling the client to remove the cookie from the
          // client's machine
          else {
            $cookieParts = explode(';', $cookie);
            foreach ($cookieParts as $partKey => $partValue) {
              $argument = explode('=', $cookie);
              if (trim(strtolower($argument)) == 'expires') {
                $cookieParts[$partKey] = ' Expires=Thu, 01 Jan 1970 00:00:00 GMT';
              }
            }
            $cookie = implode(';', $cookieParts);
            Response_Buffer::setHeader('Set-Cookie', $cookie);
          }
        }
      }
    }
    else {
      if (Request::cookieExists($cookieName) == FALSE) {
        throw new RuntimeException("Cookie '{$cookieName}' could not be found");
      }
      Response_Buffer::setHeader('Set-Cookie', rawurlencode($cookieName).'=; Expires=Thu, 01 Jan 1970 00:00:00 GMT');
    }
  }

  #Region "shared functions"
  public static function redirect ($redirect, $redirectType = 307, $allowExternal = FALSE) {
    if (headers_sent($path, $line) == TRUE) {
      throw new RuntimeException("Headers already sent in '$path' on line '$line'");
    }

    // force redirect within the website
    if ($allowExternal == FALSE) {
      $redirect = Request::baseUrl() . $redirect;
    }

    Response_Buffer::setStatusCode($redirectType);
    Response_Buffer::setHeader('Location', $redirect, TRUE);
  }

  static public function end () {
    self::$_isEnding = TRUE;
  }

  static public function isEnding () {
    return self::$_isEnding;
  }
  #End Region
}

?>