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

// xxx finish

class Native_Session_Driver implements iSession_Handler {
  protected static $_sessionName = NULL;
  protected static $_cacheLimiter = NULL;
  protected static $_cacheExpire = NULL;

  public static function load () {
    return (session_id() == '');
  }

  public static function start ($sessionName = NULL, $cacheLimiter = NULL, $cacheExpire = NULL) {
    if ($sessionName !== NULL) {
      Native_Session_Driver::$_sessionName = $sessionName;
      session_name($sessionName);
    }

    if ($cacheLimiter != NULL) {
      Native_Session_Driver::$_cacheLimiter = $cacheLimiter;
      session_cache_limiter($cacheLimiter);
    }

    if ($cacheExpire != NULL) {
      Native_Session_Driver::$_cacheExpire = $cacheExpire;
      session_cache_expire($cacheExpire);
    }

    session_start();
  }

  public static function exists () {
    return (session_id() != '');
  }

  public static function regenerate () {
    session_regenerate_id();
  }

  public static function destroy () {
    $_SESSION = array();

    if (ini_get('session.use_cookies')) {
      $params = session_get_cookie_params();
      setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }

    session_destroy();
  }

  public static function close () {
    session_write_close();
  }

  public static function write () {
    session_write_close();

    if (Native_Session_Driver::$_sessionName !== NULL) {
      session_name(Native_Session_Driver::$_sessionName);
    }

    if (Native_Session_Driver::$_cacheLimiter != NULL) {
      session_cache_limiter(Native_Session_Driver::$_cacheLimiter);
    }

    if (Native_Session_Driver::$_cacheExpire != NULL) {
      session_cache_expire(Native_Session_Driver::$_cacheExpire);
    }

    session_start();
  }

  public static function purge () {
    // PHP does this internally automatically depending on the configuration directives set
  }
}

?>