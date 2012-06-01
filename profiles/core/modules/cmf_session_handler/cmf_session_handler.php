<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

// xxx using databases is considered to be slow when it comes to dealing with sessions, so we need to make a copy of this and rewrite it to support memcache
// xxx need some form of locking to prevent session corruption (such as the case with missing files and the session table filling up with new invalid sessions)
// xxx TABLE LOCKING TABLE LOCKING TABLE LOCKING!!!
class Cmf_Session_Handler implements iSession_Handler {
  // The name of the session
  static private $_sessionName = '';

  // Together the following make up the session ID
  static private $_token = NULL; // Non-guessable
  static private $_uuid = NULL; // Guessable

  // Contains all variables set for the current session
  static private $_store = NULL;

  // Should the session be kept alive when the browser closes?
  static private $_keepAlive = FALSE;

  // Used to keep the time in sync for cookies and the DB
  static private $_time = 0;

  // Determines whether the session can be saved or not
  static private $_sessionWritable = FALSE;

  // Cookie name settings
  static private $_securePrefix = 'secure_';
  static private $_tokenPostfix = '_token';
  static private $_uuidPostfix = '_uuid';

  static private $_cookiesFound = FALSE;

  const Prepared_Statement_Library = 'cmf_session_handler_prepared_statement_library';

  public static function install () {
    Config::setValue(CMF_REGISTRY, 'session', 'autostart', 1);
    Config::setValue(CMF_REGISTRY, 'session', 'name', 'session');
    Config::setValue(CMF_REGISTRY, 'session', 'expiration', 604800); // 1 week
    Config::setValue(CMF_REGISTRY, 'session', 'cookie_domain', ''); // No host (automatically work out)
  }

  public static function initialise () {
    Session::setHandler(__CLASS__);

    if (Config::getValue('session', 'autostart') == TRUE) {
      self::start();
    }
  }

  private static function _initialiseSettings () {
    static $initialised = FALSE;

    if ($initialised == TRUE) {
      return;
    }

    self::$_time = time();

    self::$_sessionName = Config::getValue('session', 'name');

    // For secure cookies use a different name
    if (Request::isSecure() == TRUE) {
      self::$_sessionName = self::$_securePrefix . self::$_sessionName;
    }

    // Prevent caching
    Response_Buffer::setHeader('Expires', 'Mon, 19 Nov 1981 08:52:00 GMT');
    Response_Buffer::setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    Response_Buffer::setHeader('Pragma', 'no-cache');

    $initialised = TRUE;
  }

  public static function start () {
    // If the session has already started to prevent the store from corrupting we quick escape
    if (self::exists() == TRUE) {
      return;
    }

    self::_initialiseSettings();

    self::purge();

    // Allow the session to be saved at the end of the request
    self::enableWrite();

    // Get the token and UUID from the cookies if they exist
    $cookies = self::_getCookies();

    // See if we can get the session data based on the cookie data provided by the visitor
    self::$_store = self::_getSessionData($cookies['token'], $cookies['uuid']);

    // If the session exists it will have values set by the session handler, otherwise the self::$_store variable value will be an empty array
    if (count(self::$_store) > 0) {
      // If a session exists make sure we set the token and UUID for internal use
      self::$_token = $cookies['token'];
      self::$_uuid = $cookies['uuid'];

      self::_checkForHijackedSession();

      // xxx not working properly
      //self::regenerateToken();

      // Should the session be remembered?
      self::$_keepAlive = self::$_store['keep_alive'];
    }
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

  private static function _getCookies () {
    static $cookies = array();

    if (count($cookies) > 0) {
      return $cookies;
    }

    $cookies = array(
      'token' => '',
      'uuid' => ''
    );

    if (Request::cookieExists(self::$_sessionName . self::$_tokenPostfix) == TRUE) {
      $cookie = Request::getCookie(self::$_sessionName . self::$_tokenPostfix);
      $cookies['token'] = strval($cookie->getValue());
      self::$_cookiesFound = TRUE;
    }

    if (Request::cookieExists(self::$_sessionName . self::$_uuidPostfix) == TRUE) {
      $cookie = Request::getCookie(self::$_sessionName . self::$_uuidPostfix);
      $cookies['uuid'] = strval($cookie->getValue());
      self::$_cookiesFound = TRUE;
    }

    return $cookies;
  }

  private static function _getSessionData ($token, $uuid) {
    Assert::isString($token);
    Assert::isString($uuid);

    $store = NULL;

    // Get the data
    $query = Cmf_Database::call('cmf_session_get_data', self::Prepared_Statement_Library);
    $query->bindValue(':session_token', $token);
    $query->bindValue(':session_uuid', $uuid);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();

    $sessionData = strval($query->fetchColumn());

    if ($sessionData != '') {
      $store = self::_decodeData($sessionData);
    }

    // Check for a redirect
    while ($store instanceof Store && count($store) == 1 && array_key_exists('new_token', $store) == TRUE) {
      $store = self::_getSessionData($store['new_token'], $uuid);
    }

    // Either the store failed to unserialize, the store could not be found, the session is invalid or has expired
    if (($store instanceof Store) == FALSE) {
      if (self::$_cookiesFound == TRUE) {
        // xxx trigger an event so that we can show a session invalid/expired page
        // xxx also make cookies last twice as long as session so we can show a session expired page
      }
      $store = new Store;
    }

    return $store;
  }

  // A basic user agent header check to see if the session has been hijacked or not
  private static function _checkForHijackedSession () {
    if (self::$_store['user_agent'] != Request::userAgent()) {
      self::_create();
    }
  }

  private static function _create () {
    $id = self::_generateSessionId();
    self::$_token = $id['token'];
    self::$_uuid = $id['uuid'];

    // Create a new session store
    self::$_store = new Store;

    // Set the default store values
    self::$_store['user_agent'] = Request::userAgent();
    self::$_store['keep_alive'] = FALSE; // By default the session ends when the browser closes
  }

  private static function _setCookies () {
    self::_setCookie(self::$_sessionName . self::$_tokenPostfix, self::$_token);
    self::_setCookie(self::$_sessionName . self::$_uuidPostfix, self::$_uuid);
  }

  private static function _setCookie ($name, $value) {
    Assert::isString($name);
    Assert::isString($value);

    $cookie = new Cookie;

    $cookie->setName($name);
    $cookie->setValue($value);
    $cookie->setHttpOnly(TRUE);

    /**
     * To make the cookie domain automatically generate, the config setting 'session' -> 'cookie_domain' must be set and
     * set to blank, if the value is not set at all then no domain will be passed in the cookie header
     */
    if (Config::hasValue('session', 'cookie_domain')) {
      if (Config::getValue('session', 'cookie_domain') != '') {
        $cookie->setDomain(Config::getValue('session', 'cookie_domain'));
      }
      else {
        $cookie->setDomain(Request::host());
      }
    }

    if (self::$_keepAlive == TRUE) {
      // We set the cookies to remain stored twice as long as the session so that we can inform the user that the session has expired
      $expires = ((self::_getExpires() - self::$_time) * 2) + self::$_time;
      $cookie->setExpire($expires);
    }
    else {
      $cookie->setExpire(0);
    }

    Response::setCookie($cookie);
  }

  private static function _removeCookies () {
    try {
      Response::removeCookie(self::$_sessionName . self::$_tokenPostfix, TRUE);
      Response::removeCookie(self::$_sessionName . self::$_uuidPostfix, TRUE);
    }
    catch (Exception $ex) {}
  }

  private static function _generateSessionId () {
    if (self::$_uuid !== NULL) {
      $uuid = self::$_uuid;
    }
    else {
      // Get a uuid
      $query = Cmf_Database::call('cmf_session_get_uuid', self::Prepared_Statement_Library);
      $query->execute();
      $uuid = $query->fetchColumn();
    }

    // xxx temp to see why we keep spawning lots of duplicates of the same session
    $store = new Store;
    if (self::$_cookiesFound == TRUE) {
      $cookies = self::_getCookies();
      $store->setValue('_old_cookie_token', $cookies['token']);
      $store->setValue('_old_cookie_uuid', $cookies['uuid']);
      self::$_store['_old_cookie_token'] = $cookies['token'];
      self::$_store['_old_cookie_uuid'] = $cookies['uuid'];
    }

    // Get a token
    do {
      $token = Hash::id(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'));

      // In order to generate a session ID, we must create an entry in the table to reserve the token
      $query = Cmf_Database::call('cmf_session_insert', self::Prepared_Statement_Library);
      $query->bindValue(':session_expires', self::_getExpires());
      $query->bindValue(':session_token', $token);
      $query->bindValue(':session_uuid', $uuid);
      //$query->bindValue(':session_data', self::_encodeData(new Store));
      $query->bindValue(':session_data', self::_encodeData($store));
      $query->bindValue(':s_id', Config::getValue('site', 'id'));
      $sessionCreated = $query->execute();
    }
    while ($sessionCreated == FALSE);

    return array('token' => $token, 'uuid' => $uuid);
  }

  public static function exists () {
    return (self::$_token !== NULL);
  }

  public static function regenerateToken () {
    if (self::exists() == FALSE) {
      throw new RuntimeException('Session does not exist');
    }

    // Generate a new token and reserve it
    $id = self::_generateSessionId();

    try {
      // Update the new reserved record
      $query = Cmf_Database::call('cmf_session_update', self::Prepared_Statement_Library);
      $query->bindValue(':session_token', $id['token']);
      $query->bindValue(':session_uuid', self::$_uuid);
      $query->bindValue(':session_expires', self::_getExpires());
      $query->bindValue(':session_data', self::_encodeData(self::$_store));
      $query->bindValue(':s_id', Config::getValue('site', 'id'));
      $query->execute();

      // Update the previous record to redirect to the new record for the duration set in the PHP ini file for max_execution_time
      $redirect = new Store;
      $redirect->setValue('new_token', $id['token']);

      $query = Cmf_Database::call('cmf_session_update', self::Prepared_Statement_Library);
      $query->bindValue(':session_token', self::$_token);
      $query->bindValue(':session_uuid', self::$_uuid);
      $query->bindValue(':session_expires', self::$_time + ini_get('max_execution_time'));
      $query->bindValue(':session_data', self::_encodeData($redirect));
      $query->bindValue(':s_id', Config::getValue('site', 'id'));
      $query->execute();

      self::$_token = $id['token'];
    }
    catch (Exception $ex) {
      self::close();
    }
  }

  public static function getToken () {
		if (self::exists() == FALSE) {
      throw new RuntimeException('Session does not exist');
    }

    return self::$_token;
  }
  
  public static function destroy () {
		if (self::exists() == FALSE) {
      throw new RuntimeException('Session does not exist');
    }

    $query = Cmf_Database::call('cmf_session_delete', self::Prepared_Statement_Library);
    $query->bindValue(':session_token', self::$_token);
    $query->bindValue(':session_uuid', self::$_uuid);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();

    self::close();
  }

  public static function close () {
		if (self::exists() == FALSE) {
      throw new RuntimeException('Session does not exist');
    }

    // Delete all associated cookies
    self::_removeCookies();

    // Reset variables
    self::$_token = NULL;
    self::$_uuid = NULL;
    self::$_store = NULL;

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
    self::$_sessionWritable = FALSE;
    Event_Dispatcher::detachObservers(Response_Buffer_Event_Helper_Event::preprocess, __CLASS__ . '::write');
  }

  // see comments for self::disableWrite()
  public static function enableWrite () {
    self::$_sessionWritable = TRUE;
    Event_Dispatcher::attachObserver(Response_Buffer_Event_Helper_Event::preprocess, __CLASS__ . '::write');
  }

  // See comments for self::disableWrite()
  public static function isWritable () {
    return self::$_sessionWritable;
  }

  public static function write () {
		if (self::exists() == FALSE) {
      throw new RuntimeException('Session does not exist');
    }

		if (self::isWritable() == FALSE) {
      throw new RuntimeException('Session is set to read-only');
    }

    self::_setCookies();

    $query = Cmf_Database::call('cmf_session_update', self::Prepared_Statement_Library);
    $query->bindValue(':session_token', self::$_token);
    $query->bindValue(':session_uuid', self::$_uuid);
    $query->bindValue(':session_expires', self::_getExpires());
    $query->bindValue(':session_data', self::_encodeData(self::$_store));
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();
  }

  public static function purge () {
    $query = Cmf_Database::call('cmf_session_purge', self::Prepared_Statement_Library);
    $query->bindValue(':now', self::$_time);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();
  }

  static public function setKeepAlive ($keepAlive) {
    Assert::isBoolean($keepAlive);
    self::$_keepAlive = $keepAlive;
  }

  static public function getKeepAlive () {
    return self::$_keepAlive;
  }

  static public function getStore () {
		if (self::exists() == FALSE) {
      throw new RuntimeException('Session does not exist');
    }

    return self::$_store;
  }

  static private function _encodeData ($data) {
    return base64_encode(serialize($data));
  }

  static private function _decodeData ($data) {
    return unserialize(base64_decode($data));
  }
}

?>