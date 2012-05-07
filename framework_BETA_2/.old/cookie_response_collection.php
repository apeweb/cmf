<?php

/**
 * NOTE: If you work on this file please provide comments in the following
 *       section!
 *
 * NOTE: In the version please include your author identifiable tag!
 *
 * NOTE: For readability, comments should not be longer than 76 characters
 *       long on any line unless the comment includes a web page address or
 *       something similar that should not be split onto multiple lines.
 *
 * Cookie_Response_Collection
 * Version 1.0.0.0
 * Last edited by: Matthew Bonner
 *
 * Copyright (c) 2009 Ape Web Ltd & 2009-2011 Ape Web LLP
 *
 */

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

/**
 * Sends a cookie to the client
 *
 * @author Matthew Bonner
 */
define('Cookie_Response_Collection', 'Cookie_Response_Collection');
class Cookie_Response_Collection {
  public function add(Cookie $cookie) {
    $cookieString = '';

    $cookieString = 'Set-Cookie: ' . rawurlencode($cookie->name) . '=' . rawurlencode($cookie->value);

    if ($cookie->expire > 0) {
      $cookieString .= '; Expires=' . date('D, d-M-Y H:i:s T', $cookie->expire);
    }

    if (trim($cookie->path) != '') {
      $cookieString .= '; Path=' . $cookie->path;
    }

    if (trim($cookie->domain) != '') {
      $cookieString .= '; Domain=' . $cookie->domain;
    }

    if ($cookie->secure == TRUE) {
      $cookieString .= '; Secure';
    }

    // xxx need to check browser support for this
    if ($cookie->httpOnly == TRUE) {
      $cookieString .= '; HttpOnly';
    }

    header($cookieString);
    //setcookie($cookie->name(), $cookie->value(), $cookie->expire(), $cookie->path(), $cookie->domain(), $cookie->secure(), $cookie->httpOnly());
  }

  // should in the future check if the cookie exists
  public function update(Cookie $cookie) {
    $cookieString = '';

    $cookieString = 'Set-Cookie: ' . rawurlencode($cookie->name) . '=' . rawurlencode($cookie->value);

    if ($cookie->expire > 0) {
      $cookieString .= '; Expires=' . date('D, d-M-Y H:i:s T', $cookie->expire);
    }

    if (trim($cookie->path) != '') {
      $cookieString .= '; Path=' . $cookie->path;
    }

    if (trim($cookie->domain) != '') {
      $cookieString .= '; Domain=' . $cookie->domain;
    }

    if ($cookie->secure == TRUE) {
      $cookieString .= '; Secure';
    }

    // xxx need to check browser support for this
    if ($cookie->httpOnly == TRUE) {
      $cookieString .= '; HttpOnly';
    }

    header($cookieString, TRUE);
    //setcookie($cookie->name(), $cookie->value(), $cookie->expire(), $cookie->path(), $cookie->domain(), $cookie->secure(), $cookie->httpOnly());
  }

  public function delete (Cookie $cookie) {
    $cookieString = '';

    $cookieString = 'Set-Cookie: ' . rawurlencode($cookie->name) . '=' . rawurlencode('deleted');

    $cookieString .= '; Expires=' . date('D, d-M-Y H:i:s T', time() - 3600);

    if (trim($cookie->path) != '') {
      $cookieString .= '; Path=' . $cookie->path;
    }

    if (trim($cookie->domain) != '') {
      $cookieString .= '; Domain=' . $cookie->domain;
    }

    if ($cookie->secure == TRUE) {
      $cookieString .= '; Secure';
    }

    // xxx need to check browser support for this
    if ($cookie->httpOnly == TRUE) {
      $cookieString .= '; HttpOnly';
    }

    header($cookieString, TRUE);
    //setcookie($cookie->name(), '', time() - 3600, $cookie->path(), $cookie->domain(), $cookie->secure(), $cookie->httpOnly());
  }
}

?>