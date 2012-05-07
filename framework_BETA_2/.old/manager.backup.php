<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

// xxx to do, should be using guid's for session tokens
// to do this use need to use mysql's UUID() function

// require_once(ROOT . 'framework/mvc/application.php');
require_once(ROOT . 'framework/settings.php');
require_once(ROOT . 'framework/data.php');
require_once(ROOT . 'framework/data/sql_client.php');
require_once(ROOT . 'framework/network.php');
require_once(ROOT . 'framework/security/encryption.php');

define('Session', NULL);

class Session {
  static protected $_request = Request;
  static protected $_db = Database;
  static protected $_hash = Hash_Encryption;
  static protected $_response = Response;

  static protected $_token = '';
  static protected $_store = array();
  static protected $_expires = 0;
  static protected $_keepAlive = 0;

  static protected $_initialised = FALSE;

  /**
   * @return void
   */
  static public function load () {
    if (self::$_initialised == TRUE) {
      return;
    }

    self::$_request = new Request;
    self::$_db = new Database;
    self::$_hash = new Hash_Encryption;
    self::$_response = new Response;

    self::purgeExpired();

    if (self::_exists() == FALSE && Application::config('session/autostart') == TRUE) {
      self::start();
    }

    Application::attachObserver(Application_Event::terminate, 'Session::commit');

    self::$_initialised = TRUE;
  }

  // get store vars
  public function __get ($variableName) {
    switch ($variableName) {
      case 'token':
        return self::$_token;
      break;

      case 'exists':
        return self::_exists();
      break;
    }

    if (isset(self::$_store[$variableName])) {
      return self::$_store[$variableName];
    }
    else {
      $trace = debug_backtrace();

      $dump = array();
      $dump['file'] = $trace[0]['file'];
      $dump['line'] = $trace[0]['line'];

      Event_Log::Add(Event_Log_Type::Runtime, 'Session', $variableName . ' is not a member of the session store', $dump, Event_Log_Level::Warning);

      return NULL;
    }
  }

  // set store vars
  public function __set ($variableName, $value) {
    switch ($variableName) {
      case 'token':
      case 'exists':
        throw new Exception ("Property '" . $variableName . "' is read only.");
        return;
      break;
    }

    self::$_store[$variableName] = $value;
  }

  public function __isset ($variableName) {
    switch ($variableName) {
      case 'token':
      case 'exists':
        return TRUE;
      break;
    }

    return isset(self::$_store[$variableName]);
  }

  public function __unset ($variableName) {
    switch ($variableName) {
      case 'token':
      case 'started':
        throw new Exception ("Property '" . $variableName . "' is read only.");
        return;
       break;
    }

    unset(self::$_store[$variableName]);
  }

  /**
   * Restricts cloning of the object to prevent multiple sessions
   */
  protected function __clone() {}

  static protected function _exists () {
    //echo 'Checking if session exists',BR;
    //echo 'Token currently set to: ', self::$_token, BR;

    if (trim(self::$_token) != '') {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Creates a new session
   */
  static protected function _create () {
    //echo 'Creating a new session',BR;
    $sp = Stored_Procedure;
    $queryExecuted = FALSE;

    self::$_token = self::_generateToken();
    self::$_expires = time() + Setting::SESSION_EXPIRATION;
    self::$_store = array();
    self::$_store['totalRequests'] = 1; // counts the number of requests made

    try {
      $sp = new Stored_Procedure('session_insert');
      $sp->addParameter('token', self::$_token);
      $sp->addParameter('expires', self::$_expires);
      $sp->addParameter('regeneration', time() + Setting::SESSION_REGENERATE_TIME);
      $sp->addParameter('user_agent', self::$_request->userAgent->id);
      $sp->addParameter('ip', self::$_request->visitorHostAddress());
      $queryExecuted = self::$_db->executeNonQuery($sp);
    }
    catch (Sql_Exception $ex) {
      $queryExecuted = FALSE;
      Sql_Error::log($sp->name, $ex->getMessage());
      break;
    }
    catch (Exception $ex) {
      $queryExecuted = FALSE;
    }

    if ($queryExecuted == TRUE) {
      //echo 'Created new session in DB',BR;
      self::_createCookie();
    }
    else {
      //echo 'Failed to create new session in DB',BR;
      self::_deleteCookie();
      self::$_token = '';
      self::$_store = array();
    }
  }

  /**
   * Starts the session and loads the store
   * @return bool whether the session was started or not
   */
  static public function start () {
    //echo 'Attempting to start session',BR;

    $cookie = Cookie;
    $ds = Data_Set;
    $sessionData = Data_Row;
    $token = ''; // use self::$_token
    $dump = array();

    // if the session has already started return FALSE
    if (self::_exists() == TRUE) {
      return FALSE;
    }

    // make sure we don't cache the page
    header('Expires: Thu, 19 Nov 1981 08:52:00 GMT');
    header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    header('Pragma: no-cache');

    //echo 'Session has not already started, starting session...',BR;

    // we don't want to filter it as if someone has messed with it then we dont
    // want to allow them to continue using the website
    $token = strval(self::$_request->cookie(Setting::SESSION_NAME));

    //echo 'Looking for session in DB using token: ', $token, BR;

    // query the database for the session
    try {
      $sp = new Stored_Procedure('session_select_all');
      $sp->addParameter('token', $token);
      $ds = self::$_db->execute($sp);
    }
    catch (Sql_Exception $ex) {
      Sql_Error::log($sp->name, $ex->getMessage());
      return FALSE;
    }
    catch (Exception $ex) {
      return FALSE;
    }

    // if the session exists
    if ($ds->tables->count() > 0 && $ds->table(0)->rows->count() > 0) {
      //echo 'Session found',BR;

      // get the current row
      $sessionData = $ds->table(0)->row(0);

      //echo 'Session data:',BR;
      //echo nl2br(print_r($sessionData, TRUE)),BR;

      // if a session exists make sure we set the token for internal use
      self::$_token = $sessionData->token;

      // load the store
      // decode and unserialise all of the data stored for the session
      self::$_store = unserialize(base64_decode($sessionData->store));

      // if we have lost the data, make sure the store is still set as an array
      if (self::$_store == FALSE) {
        self::$_store = array();
      }

      //echo 'Session store:',BR;
      //echo nl2br(print_r(self::$_store, TRUE)),BR;

      //echo 'Current total requests: ' . self::$_store['totalRequests'] . BR;

      // count how many pages have been viewed so far, used later for regenerating the session
      ++self::$_store['totalRequests'];

      //echo 'Updated total requests to: ' . self::$_store['totalRequests'] . BR;

      if (self::$_store['totalRequests'] > 1) {
        // check ip address
        //echo 'Verifying IP address',BR;
        if (str_match(self::$_request->visitorHostAddress, $sessionData->ip) == FALSE) {
          //echo 'IP address mismatch: ' . self::$_request->visitorHostAddress . ' (current) ~ ' . $sessionData->ip . ' (db value)',BR;
          //echo 'Restarting session...',BR;
          self::_create();
          $dump = array();
          $dump['Reason'] = 'Cookie exists but database session could not be found';
          $dump['Action'] = 'Created new session, leaving original session intact';
          Event_Log::add(Event_Log_Type::Security, 'Session', 'Possible hijack attempt', $dump, Event_Log_Level::Warning);
        }

        // check user agent
        //echo 'Verifying user agent',BR;
        if (str_match(self::$_request->userAgent->id, $sessionData->user_agent) == FALSE) {
          //echo 'User agent mismatch: ' . self::$_request->userAgent->id . ' (current) ~ ' . $sessionData->user_agent . ' (db value)',BR;
          //echo 'Restarting session...',BR;
          self::_create();
          $dump = array();
          $dump['Reason'] = "User agent in database did not match visitor's user agent";
          $dump['Action'] = 'Created new session, leaving original intact';
          Event_Log::add(Event_Log_Type::Security, 'Session', 'Possible hijack attempt', $dump, Event_Log_Level::Warning);
        }

        //echo 'Working out whether to regenerate the session or not...',BR;

        // Work out whether we should be regernating the class or not
        if ($sessionData->regeneration < time() || self::$_store['totalRequests'] % Setting::SESSION_REGENERATE_HITS == 0) {
          self::regenerate();
        }
        else {
          //echo 'Not regenerating session',BR;
        }

        // should the session be remembered?
        self::$_keepAlive = $sessionData->keep_alive;

        // update the cookie to stop it expiring
        self::_updateCookie();
        // need to update row to show new expires time
        //$sp->addParameter('@expires', time() + Setting::SESSION_EXPIRATION);
      }
    }
    // a session does not exist create one
    else {
      //echo 'No session found',BR;
      self::_create();
    }

    return TRUE;
  }

  /**
   * Generates a session token
   */
  static protected function _generateToken () {
    //echo 'Generating new session token',BR;

    $sp = Stored_Procedure;
    $ds = Data_Set;
    $token = '';

    // make sure the token is not in use
    do {
      $token = self::$_hash->sha512i(str_shuffle('./0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz+'));

      try {
        $sp = new Stored_Procedure('session_select_token');
        $sp->addParameter('@token', $token);
        $ds = self::$_db->execute($sp);
      }
      catch (Sql_Exception $ex) {
        Sql_Error::log($sp->name, $ex->getMessage());
        $token = '';
        break;
      }
      catch (Exception $ex) {
        $token = '';
        break;
      }
    }
    while ($ds->tables->count() > 0 && $ds->table(0)->rows->count() > 0);

    return $token;
  }

  static public function regenerate () {
    //echo 'Regenerating session token',BR;

    $old_token = '';
    $new_token = '';
    $sp = Stored_Procedure;
    $result = FALSE;

    if (self::_exists() == TRUE) {
      $old_token = self::$_token;
      $new_token = self::_generateToken();

      // could not regenerate at this time
      if (empty($new_token) == TRUE) {
        return $result;
      }

      try {
        $sp = new Stored_Procedure('session_update_token');
        $sp->addParameter('new_token', $new_token);
        $sp->addParameter('regeneration', time() + Setting::SESSION_REGENERATE_TIME);
        $sp->addParameter('old_token', $old_token);
        $result = self::$_db->executeNonQuery($sp);
      }
      catch (Sql_Exception $ex) {
        $result = FALSE;
        Sql_Error::log($sp->name, $ex->getMessage());
      }
      catch (Exception $ex) {
        $result = FALSE;
      }

      if ($result == TRUE) {
        self::$_token = $new_token;
        //echo 'New token: ' . self::$_token . BR;
      }
    }

    return $result;
  }

  static protected function _createCookie () {
    //echo 'Creating session cookie',BR;

    $cookie = Cookie;

    if (self::$_keepAlive == TRUE) {
      $cookie = new Cookie(Setting::SESSION_NAME, self::$_token, time() + Setting::SESSION_EXPIRATION);
    }
    else {
      $cookie = new Cookie(Setting::SESSION_NAME, self::$_token, 0);
    }

    self::$_response->cookies->add($cookie);
  }

  static protected function _updateCookie () {
    //echo 'Updating session cookie',BR;

    $cookie = Cookie;

    if (self::$_keepAlive == TRUE) {
      $cookie = new Cookie(Setting::SESSION_NAME, self::$_token, time() + Setting::SESSION_EXPIRATION);
    }
    else {
      $cookie = new Cookie(Setting::SESSION_NAME, self::$_token, 0);
    }

    self::$_response->cookies->update($cookie);
  }

  static protected function _deleteCookie () {
    //echo 'Deleting session cookie',BR;
    $cookie = new Cookie(Setting::SESSION_NAME);
    self::$_response->cookies->delete($cookie);
  }

  // deletes the session
  static public function destroy () {
    //echo 'Destroying session',BR;

		if (self::_exists() == TRUE) {
      // delete session from the db
      try {
        $sp = new Stored_Procedure('session_delete');
        $sp->addParameter('@token', self::$_token);
        self::$_db->executeNonQuery($sp);
      }
      catch (Sql_Exception $ex) {
        Sql_Error::log($sp->name, $ex->getMessage());
      }

      // delete all associated cookies
			self::_deleteCookie();

      // unset any vars that may have been used
      unset(self::$_data);
		}
  }

  static public function close () {
    //echo 'Closing session',BR;

    if (self::_exists() == TRUE) {
      // save changes to the db
      self::commit();

      // delete all associated cookies
			self::_deleteCookie();

      // unset any vars that may have been used
      unset(self::$_data);
    }
  }

  static public function commit () {
    //echo 'Committing session',BR;
    //echo nl2br(print_r(self::$_store, TRUE)),BR;

    if (self::_exists() == TRUE) {
      try {
        $sp = new Stored_Procedure('session_update_commit');
        $sp->addParameter('@token', self::$_token);
        $sp->addParameter('@store', base64_encode(serialize(self::$_store)));
        $sp->addParameter('@keep_alive', self::$_keepAlive);
        $sp->addParameter('@expires', self::$_expires);
        self::$_db->executeNonQuery($sp);
      }
      catch (Sql_Exception $ex) {
        Sql_Error::log($sp->name, $ex->getMessage());
      }
    }
  }

  /**
   * Removes all expired sessions out of the database
   */
  static public function purgeExpired () {
    //echo 'Deleting expired sessions',BR;
    $sp = new Stored_Procedure('session_delete_expired');
    /*
     * Passing a timestamp is more reliable than depending on the mysql time
     * as the mysql time could vary with the server the PHP is on (if different)
     */
    $sp->addParameter('@now', time(), self::$_db->type['int']);
    self::$_db->executeNonQuery($sp);
  }

  /**
   * Performs required functions to ensure session stays alive when the browser
   * closes if set to TRUE and forces session to die when the browser closes if
   * set to FALSE
   */
  static public function keepAlive ($keepAlive) {
    //echo 'Setting session to keep alive: ' . intval($keepAlive) . BR;

    if (is_bool($keepAlive) == TRUE || (is_numeric($keepAlive) == TRUE && is_string($keepAlive) == FALSE)) {
      if (self::_exists() == TRUE) {
        if (self::$_response->headersSent == FALSE) {
          self::$_keepAlive = $keepAlive;
          self::_updateCookie();
        }
        else {
          Event_Log::Add(Event_Log_Type::Runtime, 'Session', 'Trying to set self::$_keepAlive to ' . intval($keepAlive) . ' when headers have already sent, cookie cannot be updated.', array(), Event_Log_Level::Warning);
        }
      }
      else {
        Event_Log::Add(Event_Log_Type::Runtime, 'Session', 'Trying to set self::$_keepAlive to ' . intval($keepAlive) . ' when the session has not been started or no longer exists.', array(), Event_Log_Level::Warning);
      }
    }
    else {
      Event_Log::Add(Event_Log_Type::Runtime, 'Session', 'Trying to set self::$_keepAlive to ' . strval($keepAlive) . ' which is not a valid value.', array(), Event_Log_Level::Error);
    }
  }
}

?>