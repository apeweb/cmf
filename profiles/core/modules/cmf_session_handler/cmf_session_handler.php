<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

// xxx add support for s_id

class Cmf_Session_Handler implements iSession_Handler {
  static private $_sessionName = '';
  static private $_token = NULL;
  static private $_store = array();
  static private $_keepAlive = FALSE;
  static private $_time = 0;
  static private $_sessionWritable = FALSE;

  static private $_initialised = FALSE;

  const Prepared_Statement_Library = 'cmf_session_handler_prepared_statement_library';

  public static function install () {
    Config::setValue(CMF_REGISTRY, 'session', 'autostart', 1);
    Config::setValue(CMF_REGISTRY, 'session', 'name', 'sid' . '_' . php_uname('n'));
    Config::setValue(CMF_REGISTRY, 'session', 'token_regenerate_hits', 5); // Every 5 hits
    Config::setValue(CMF_REGISTRY, 'session', 'token_regenerate_time', 60*5); // Every 5 minutes
    Config::setValue(CMF_REGISTRY, 'session', 'expiration', 60*60*24*7); // 1 week
    Config::setValue(CMF_REGISTRY, 'session', 'cookie_domain', ''); // no host (automatically work out)
  }

  public static function initialise () {
    Session::setHandler(__CLASS__);

    if (self::$_initialised == FALSE && Config::getValue('session', 'autostart') == TRUE) {
      self::start();
    }
  }

  private static function _initialiseSettings () {
    // keep all times set in cookies and the DB in sync
    self::$_time = time();

    self::$_sessionName = Config::getValue('session', 'name');

    // For secure cookies use a different name
    if (Request::isSecure() == TRUE) {
      self::$_sessionName .= '_s';
    }
  }

  public static function start () {
    // if the session has already started return
    if (self::$_initialised == TRUE && self::exists() == TRUE) {
      return;
    }

    // We don't initialise extra resources until the session actually starts
    self::_initialiseSettings();

    // Purge any old sessions from the database
    self::purge();

    // make sure we don't cache the page
    self::_setCacheControlHeaders();

    // everything we require to consider sessions initialised has now happened
    self::$_initialised = TRUE;

    // allow the session to be saved at the end of the request
    self::enableWrite();

    // get the token from the cookie if one exists
    $token = self::_getCookieToken();

    // see if we can get the session data based on the cookie token
    self::$_store = self::getSessionData($token);

    // if the session exists it will have values set by the session handler
    if (count(self::$_store) > 0) {
      // if a session exists make sure we set the token for internal use
      self::$_token = $token;

      // check to see if the session has been hijacked or not
      self::_checkForHijackedSession();

      self::_incrementCounter();

      // Work out whether we should be regenerating the token or not
      if (self::$_store['token_regeneration'] < self::$_time || self::$_store['total_requests'] % Config::getValue('session', 'token_regenerate_hits') == 0) {
        // xxx bug with not being able to lock sessions means we can't regenerate tokens
        // the bug:
        // the browser makes 3 requests at the same time
        // the first request regenerates the token from A1 to B2
        // the second request looks for A1 which doesn't exist anymore as it is now B2, and since it can't find A1 it creates a new session C3
        // the third comes along, and does the same as the second
        //self::regenerateToken();
      }

      // should the session be remembered?
      self::$_keepAlive = self::$_store['keep_alive'];
    }
    // a session does not exist create one
    else {
      self::_create();
    }
  }

  private static function _getExpires () {
    static $expires;

    if (isset($expires) == TRUE) {
      return $expires;
    }

    $expires = self::$_time + Config::getValue('session', 'expiration');

    return $expires;
  }

  private static function _setCacheControlHeaders () {
    Response_Buffer::setHeader('Expires', 'Mon, 19 Nov 1981 08:52:00 GMT');
    Response_Buffer::setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    Response_Buffer::setHeader('Pragma', 'no-cache');
  }

  private static function _getCookieToken () {
    $token = '';

    if (Request::cookieExists(self::$_sessionName) == TRUE) {
      $cookie = Request::getCookie(self::$_sessionName);
      $token = strval($cookie->getValue());
    }

    Debug::logMessage('Existing Session Token:', $token);

    return $token;
  }

  // Get the session data for a user based on the session token, because session data is read only to outside classes
  // (to avoid session session poisoning) there is no setSessionData method
  public static function getSessionData ($token) {
    Assert::isString($token);

    $query = Cmf_Database::call('cmf_session_get_data', self::Prepared_Statement_Library);
    $query->bindValue(':session_token', $token);
    $query->execute();

    $sessionData = strval($query->fetchColumn());

    if ($sessionData != '') {
      return (array) unserialize(base64_decode($sessionData));
    }

    return array();
  }

  private static function _checkForHijackedSession () {
    // check user agent for hijack
    if (self::$_store['user_agent'] != Request::userAgent()) {
      self::_create();
    }
  }
  
  private static function _incrementCounter () {
    // count how many pages have been viewed so far, used later for regenerating the session
    ++self::$_store['total_requests'];

    // make sure we don't cause an integer overflow
    if (self::$_store['total_requests'] == PHP_INT_MAX) {
      self::$_store['total_requests'] = 0;
    }
  }

  private static function _create () {
    self::$_token = self::_generateToken();
    self::$_store = array(); // Make sure nothing has tampered with the store

    // Set the default store values
    self::$_store['total_requests'] = 1; // counts the number of requests made
    self::$_store['user_agent'] = Request::userAgent();
    self::$_store['token_regeneration'] = self::$_time + Config::getValue('session', 'token_regenerate_time');
    self::$_store['keep_alive'] = FALSE; // By default the session ends when the browser closes

    // create the session
    $query = Cmf_Database::call('cmf_session_insert', self::Prepared_Statement_Library);
    $query->bindValue(':session_expires', self::_getExpires());
    $query->bindValue(':session_token', self::$_token);
    $sessionCreated = $query->execute();

    if ($sessionCreated == FALSE) {
      self::close();
    }
  }

  private static function _setCookie () {
    $cookie = new Cookie;

    $cookie->setName(self::$_sessionName);
    $cookie->setValue(self::$_token);
    $cookie->setHttpOnly(TRUE);

    if (Config::hasValue('session', 'cookie_domain') == TRUE && Config::getValue('session', 'cookie_domain') != '') {
      $cookie->setDomain(Config::getValue('session', 'cookie_domain'));
    }
    else {
      $cookie->setDomain(Config::getValue('session', Request::host()));
    }

    if (self::$_keepAlive == TRUE) {
      $cookie->setExpire(self::_getExpires());
    }
    else {
      $cookie->setExpire(0);
    }

    Response::setCookie($cookie);
  }

  private static function _removeCookie () {
    if (Request::cookieExists(self::$_sessionName) == TRUE) {
      Response::removeCookie(self::$_sessionName, TRUE);
    }
  }

  private static function _generateToken () {
    do {
      $token = Hash::id(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'));

      $query = Cmf_Database::call('cmf_session_get_token_count', self::Prepared_Statement_Library);
      $query->bindValue(':session_token', $token);
      $query->execute();
    }
    while ($query->fetchColumn(0) > 0);

    // $query->fetchColumn(0) has a session token which may not be unique across a server farm but is not guessable
    // xxx make use of $query->fetchColumn(1) which is the unique (guessable) id

    Debug::logMessage('New Session Token:', $token);

    return $token;
  }

  public static function exists () {
    return (self::$_token !== NULL);
  }

  public static function regenerateToken () {
    if (self::$_initialised == FALSE) {
      throw new RuntimeException('Session is not initialised');
    }

    if (self::exists() == FALSE) {
      throw new RuntimeException('Session does not exist');
    }

    $old_token = self::$_token;
    $new_token = self::_generateToken();

    $query = Cmf_Database::call('cmf_session_update_token', self::Prepared_Statement_Library);
    $query->bindValue(':old_session_token', $old_token);
    $query->bindValue(':new_session_token', $new_token);

    if ($query->execute() == TRUE) {
      self::$_token = $new_token;
      self::$_store['token_regeneration'] = self::$_time + Config::getValue('session', 'token_regenerate_time');
    }
    else {
      self::close();
    }
  }

  public static function getToken () {
    return self::$_token;
  }
  
  public static function destroy () {
    if (self::$_initialised == FALSE) {
      throw new RuntimeException('Session is not initialised');
    }

		if (self::exists() == FALSE) {
      throw new RuntimeException('Session does not exist');
    }

    $query = Cmf_Database::call('cmf_session_delete', self::Prepared_Statement_Library);
    $query->bindValue(':session_token', self::$_token);
    $query->execute();

    self::close();
  }

  public static function close () {
    if (self::$_initialised == FALSE) {
      throw new RuntimeException('Session is not initialised');
    }

		if (self::exists() == FALSE) {
      throw new RuntimeException('Session does not exist');
    }

    // delete all associated cookies
    self::_removeCookie();

    // Reset any variables
    self::$_token = NULL;
    self::$_store = array();

    self::disableWrite();
  }

  /**
   * Disable session writing to allow potentially unsafe operations to continue, see:
   * http://drupal.org/node/218104
   *
   * $sessionWasWritable = Cmf_Session_Handler::isWritable();
   * Cmf_Session_Handler::disableWrite();
   *
   * // Safely impersonating another user
   * $previouslyActiveUser = Cmf_User_Session::getActiveUser();
   * Cmf_User_Session::setActiveUser($impersonateUser);
   *
   * // Go back to previous user session
   * Cmf_User_Session::setActiveUser($previouslyActiveUser);
   *
   * if ($sessionWasWritable == TRUE) {
   *   Cmf_Session_Handler::enableWrite();
   * }
   */
  public static function disableWrite () {
    if (self::$_initialised == FALSE) {
      throw new RuntimeException('Session is not initialised');
    }

    self::$_sessionWritable = FALSE;
    Event_Dispatcher::detachObservers(Response_Buffer_Event_Helper_Event::preprocess, __CLASS__ . '::write');
  }

  // see comments for self::disableWrite()
  public static function enableWrite () {
    if (self::$_initialised == FALSE) {
      throw new RuntimeException('Session is not initialised');
    }

    self::$_sessionWritable = TRUE;

    Event_Dispatcher::attachObserver(Response_Buffer_Event_Helper_Event::preprocess, __CLASS__ . '::write');
  }

  // see comments for self::disableWrite()
  public static function isWritable () {
    return self::$_sessionWritable;
  }

  public static function write () {
    if (self::$_initialised == FALSE) {
      throw new RuntimeException('Session is not initialised');
    }

		if (self::exists() == FALSE) {
      throw new RuntimeException('Session does not exist');
    }

		if (self::isWritable() == FALSE) {
      throw new RuntimeException('Session is set to read-only');
    }

    self::_setCookie();

    $query = Cmf_Database::call('cmf_session_update', self::Prepared_Statement_Library);
    $query->bindValue(':session_token', self::$_token);
    $query->bindValue(':session_expires', self::_getExpires());
    $query->bindValue(':session_data', base64_encode(serialize(self::$_store)));
    $query->execute();
  }

  public static function purge () {
    $query = Cmf_Database::call('cmf_session_purge', self::Prepared_Statement_Library);
    $query->bindValue(':now', self::$_time);
    $query->execute();
  }

  public static function setValue ($key, $value) {
    Assert::isString($key);

    if (self::$_initialised == FALSE) {
      throw new RuntimeException('Session is not initialised');
    }

    self::$_store[$key] = $value;
  }

  public static function getValue ($key) {
    Assert::isString($key);

    if (self::$_initialised == FALSE) {
      throw new RuntimeException('Session is not initialised');
    }

    if (self::valueExists($key) == FALSE) {
      throw new Missing_Value_Exception($key);
    }

    return self::$_store[$key];
  }

  public static function valueExists ($key) {
    Assert::isString($key);

    if (self::$_initialised == FALSE) {
      throw new RuntimeException('Session is not initialised');
    }

    return array_key_exists($key, self::$_store);
  }

  public static function deleteValue ($key) {
    Assert::isString($key);

    if (self::$_initialised == FALSE) {
      throw new RuntimeException('Session is not initialised');
    }

    if (self::valueExists($key) == FALSE) {
      throw new Missing_Value_Exception($key);
    }

    unset(self::$_store[$key]);
  }

  static public function keepAlive ($keepAlive) {
    Assert::isBoolean($keepAlive);

    if (self::$_initialised == FALSE) {
      throw new RuntimeException('Session is not initialised');
    }

		if (self::exists() == FALSE) {
      throw new RuntimeException('Session does not exist');
    }

    self::$_keepAlive = $keepAlive;
  }
}

?>