<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Cmf_Session_Handler implements iSession_Handler {
  // The name of the session
  static private $_sessionName = '';

  // Together the following make up the session ID
  static private $_token = NULL; // Non-guessable
  static private $_uuid = NULL; // Guessable

  // Contains all variables set for the current session
  static private $_store = array();

  // Should the session be kept alive when the browser closes?
  static private $_keepAlive = FALSE;

  // Used to keep the time in sync for cookies and the DB
  static private $_time = 0;

  // Determines whether the session can be saved or not
  static private $_sessionWritable = FALSE;

  // A flag to indicate if the session has initialised (nothing to do with whether the session has started or not)
  static private $_initialised = FALSE;

  // Cookie name settings
  static private $_securePrefix = 'secure_';
  static private $_tokenPostfix = '_token';
  static private $_uuidPostfix = '_uuid';

  const Prepared_Statement_Library = 'cmf_session_handler_prepared_statement_library';

  public static function install () {
    Config::setValue(CMF_REGISTRY, 'session', 'autostart', 1);
    Config::setValue(CMF_REGISTRY, 'session', 'name', 'session');
    Config::setValue(CMF_REGISTRY, 'session', 'expiration', 604800); // 1 week
    Config::setValue(CMF_REGISTRY, 'session', 'cookie_domain', ''); // No host (automatically work out)
  }

  public static function initialise () {
    Session::setHandler(__CLASS__);

    if (self::$_initialised == FALSE && Config::getValue('session', 'autostart') == TRUE) {
      self::start();
    }
  }

  private static function _initialiseSettings () {
    self::$_time = time();

    self::$_sessionName = Config::getValue('session', 'name');

    // For secure cookies use a different name
    if (Request::isSecure() == TRUE) {
      self::$_sessionName = self::$_securePrefix . self::$_sessionName;
    }
  }

  public static function start () {
    // If the session has already started to prevent the store from corrupting we quick escape
    if (self::$_initialised == TRUE && self::exists() == TRUE) {
      return;
    }

    self::_initialiseSettings();

    self::purge();

    // Makes sure the page isn't cached
    self::_setCacheControlHeaders();

    // Everything we require to consider sessions initialised has now happened
    self::$_initialised = TRUE;

    // Allow the session to be saved at the end of the request
    self::enableWrite();

    // Get the token and UUID from the cookies if they exist
    $cookies = self::_getCookies();

    // See if we can get the session data based on the cookie data provided by the visitor
    self::$_store = self::getSessionData($cookies['token'], $cookies['uuid']);

    // If the session exists it will have values set by the session handler, otherwise the self::$_store variable value will be an empty array
    if (count(self::$_store) > 0) {
      // If a session exists make sure we set the token and UUID for internal use
      self::$_token = $cookies['token'];
      self::$_uuid = $cookies['uuid'];

      self::_checkForHijackedSession();

      // xxx bug with not being able to lock sessions means we can't regenerate the token
      /**
       * the bug:
       * the browser makes 3 requests at the same time
       * the first request regenerates the token from A1 to B2
       * the second request looks for A1 which doesn't exist anymore as it is now B2, and since it can't find A1 it creates a new session C3
       * the third comes along, and does the same as the second
       */
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

  private static function _setCacheControlHeaders () {
    Response_Buffer::setHeader('Expires', 'Mon, 19 Nov 1981 08:52:00 GMT');
    Response_Buffer::setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    Response_Buffer::setHeader('Pragma', 'no-cache');
  }

  private static function _getCookies () {
    $cookies = array(
      'token' => '',
      'uuid' => ''
    );

    if (Request::cookieExists(self::$_sessionName . self::$_tokenPostfix) == TRUE) {
      $cookie = Request::getCookie(self::$_sessionName . self::$_tokenPostfix);
      $cookies['token'] = strval($cookie->getValue());
    }

    if (Request::cookieExists(self::$_sessionName . self::$_uuidPostfix) == TRUE) {
      $cookie = Request::getCookie(self::$_sessionName . self::$_uuidPostfix);
      $cookies['uuid'] = strval($cookie->getValue());
    }

    return $cookies;
  }

  // Get the session data for a user based on the session token, because session data is read only to outside classes
  // (to avoid session session poisoning) there is no setSessionData method
  public static function getSessionData ($token, $uuid) {
    Assert::isString($token);
    Assert::isString($uuid);

    $query = Cmf_Database::call('cmf_session_get_data', self::Prepared_Statement_Library);
    $query->bindValue(':session_token', $token);
    $query->bindValue(':session_uuid', $uuid);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();

    $sessionData = strval($query->fetchColumn());

    if ($sessionData != '') {
      return (array) unserialize(base64_decode($sessionData));
    }

    return array();
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

    // Make sure nothing has tampered with the store
    self::$_store = array();

    // Set the default store values
    self::$_store['user_agent'] = Request::userAgent();
    self::$_store['keep_alive'] = FALSE; // By default the session ends when the browser closes
  }

  private static function _setCookies () {
    self::_setCookie(self::$_sessionName . self::$_tokenPostfix, self::$_token);
    self::_setCookie(self::$_sessionName . self::$_uuidPostfix, self::$_uuid);
  }

  private static function _setCookie ($name, $value) {
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
        $cookie->setDomain(Config::getValue('session', Request::host()));
      }
    }

    if (self::$_keepAlive == TRUE) {
      $cookie->setExpire(self::_getExpires());
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
    $i = 0;

    // Get a uuid
    $query = Cmf_Database::call('cmf_session_get_uuid', self::Prepared_Statement_Library);
    $query->execute();
    $uuid = $query->fetchColumn();

    // Get a token
    do {
      // Prevent an infinite loop from using all of the resources
      if (++$i == 50) {
        throw new RuntimeException("Recursion loop detected");
      }

      $token = Hash::id(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'));

      // In order to generate a session ID, we must create an entry in the table to reserve the token
      $query = Cmf_Database::call('cmf_session_insert', self::Prepared_Statement_Library);
      $query->bindValue(':session_expires', self::_getExpires());
      $query->bindValue(':session_token', $token);
      $query->bindValue(':session_uuid', $uuid);
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
    if (self::$_initialised == FALSE) {
      throw new RuntimeException('Session is not initialised');
    }

    if (self::exists() == FALSE) {
      throw new RuntimeException('Session does not exist');
    }

    $id = self::_generateSessionId();
    $old_token = self::$_token;
    $new_token = $id['token'];

    $query = Cmf_Database::call('cmf_session_update_token', self::Prepared_Statement_Library);
    $query->bindValue(':old_session_token', $old_token);
    $query->bindValue(':new_session_token', $new_token);
    $query->bindValue(':session_uuid', self::$_uuid);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));

    if ($query->execute() == TRUE) {
      self::$_token = $new_token;
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
    $query->bindValue(':session_uuid', self::$_uuid);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
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

    // Delete all associated cookies
    self::_removeCookies();

    // Reset variables
    self::$_token = NULL;
    self::$_uuid = NULL;
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

  // See comments for self::disableWrite()
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

    self::_setCookies();

    $query = Cmf_Database::call('cmf_session_update', self::Prepared_Statement_Library);
    $query->bindValue(':session_token', self::$_token);
    $query->bindValue(':session_uuid', self::$_uuid);
    $query->bindValue(':session_expires', self::_getExpires());
    $query->bindValue(':session_data', base64_encode(serialize(self::$_store)));
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();
  }

  public static function purge () {
    $query = Cmf_Database::call('cmf_session_purge', self::Prepared_Statement_Library);
    $query->bindValue(':now', self::$_time);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
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