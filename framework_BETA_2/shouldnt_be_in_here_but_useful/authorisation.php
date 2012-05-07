<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

require_once(ROOT . 'framework/data/sql_client.php');
require_once(ROOT . 'framework/session.php');
require_once(ROOT . 'framework/mail.php');
require_once(ROOT . 'framework/network.php');
require_once(ROOT . 'framework/server.php');
require_once(ROOT . 'framework/security/access/user.php');

define('Authorisation', NULL);

class Authorisation {
  private $_db = Database;
  private $_session = Session;
  private $_user = User;
  private $_request = Request;
  private $_response = Response;
  private $_server = Server;

  private $_allowedAccessLevels = array();

  // xxx allow override by settings
  private $_signInPageUrl = '/account/sign-in/';
  private $_restrictedAccessPage = 'account/restricted/index.do';
  private $_accountDisabledUrl = '/account/disabled/';

  public function __construct () {
    $this->_db = new Database;
    $this->_session = new Session;
    $this->_request = new Request;
    $this->_response = new Response;
    $this->_server = new Server;
    $this->_user = new User;

    if ($this->_session->exists == FALSE) {
      $this->_session->start();
    }

    if ($this->_request->queryString('sign-out') !== NULL) {
      $this->signOut();
    }
  }

  public function __get ($variableName) {
    switch ($variableName) {
      case 'signedIn':
        return $this->signedIn();

      default:
        // legacy error reporting
        trigger_error("Undefined variable: '" . $variableName . "()'", E_USER_NOTICE);
    }
  }

  /**
   * Set allowed group access levels
   * @return (void)
   */
  public function grantGroupAccessTo ($accessTo) {
    $this->_allowedAccessLevels[] = $accessTo;
  }

  /**
   * Set allowed group access levels
   * @return (void)
   */
  public function grantUserAccessTo ($username, $password) {
    $this->_allowedAccessLevels[] = array($username, $password);
  }

  public function protect () {
    $signedIn = FALSE;
    $privileged = FALSE;
    $allowedUserName = '';
    $allowedPassword = '';

    $signedIn = $this->signedIn();

    if ($signedIn == FALSE) {
      $this->showSignInForm();
    }
    else {
      // loop through access levels
      for ($i = 0; $i < count($this->_allowedAccessLevels); ++$i) {
        if (is_array($this->_allowedAccessLevels[$i]) == TRUE) {
          $allowedUserName = issetor($this->_allowedAccessLevels[$i][0], '');
          $allowedPassword = issetor($this->_allowedAccessLevels[$i][1], '');

          if (str_match($this->_session->userName, $allowedUserName) == TRUE && str_match($this->_session->password, $allowedPassword, TRUE) == TRUE) {
            $privileged = TRUE;
            break;
          }
        }
        elseif ($this->_user->hasPrivilege($this->_session->userName, $this->_session->password, $this->_allowedAccessLevels[$i])) {
          $privileged = TRUE;
          break;
        }
      }

      if ($privileged == FALSE) {
        $this->showForbiddenForm();
      }
    }
  }

  /**
   * Check to see if the user is signed in or not
   * @return (boolean) Indicates whether the user is signed in or not
   */
  public function signedIn () {
    if ($this->_session->userName == NULL || trim($this->_session->userName) == '') {
      return FALSE;
    }
    else {
      if ($this->_user->exists($this->_session->userName, $this->_session->password)) {
        if ($this->_user->isEnabled($this->_session->userName, $this->_session->password) == FALSE) {
          $this->signOut();
        }
        else {
          return TRUE;
        }
      }
      else {
        return FALSE;
      }
    }
  }

  public function signIn ($userName, $password) {
    $userId = 0;
    $dump = array();

    if ($this->_user->exists($userName, $password) == TRUE) {
      if ($this->_user->isEnabled($userName, $password) == TRUE) {
        $dump['Username (Supplied)'] = $userName;

        /**
         * here we get the username again as the username is not case
         * sensitive so to avoid seeing matt Matt and MATT, etc all over
         * the place
         */
        $userName = $this->_user->normaliseUserName($userName, $password);

        /**
         * Sometimes we need the user id opposed to the username and
         * encrypted password
         */
        $userId = $this->_user->userId($userName, $password);

        $dump['Username (Normalised)'] = $userName;
        $dump['User id'] = $userId;
        $dump['Session token'] = $this->_session->token;

        // xxx change to new log
        Event_Log::add(Event_Log_Type::Security, 'Sign In', 'User signed in', $dump);

        $this->_session->userName = $userName;
        $this->_session->password = $password;
        $this->_session->userId = $userId;

        return TRUE;
      }
      else {
        $userId = $this->_user->userId($userName, $password);
        $dump['Username'] = $userName;
        $dump['User id'] = $userId;
        $dump['Session token'] = $this->_session->token;

        // xxx change to new log
        Event_Log::add(Event_Log_Type::Security, 'Sign In', 'Banned user tried signing in', $dump, Event_Log_Level::Warning);

        $this->_response->redirect($this->_accountDisabledUrl, 303);

        return FALSE;
      }
    }
    else {
      $userId = $this->_user->userId($userName, $password);
      $dump['Username'] = $userName;
      $dump['Session token'] = $this->_session->token;

      // xxx change to new log
      Event_Log::add(Event_Log_Type::Security, 'Sign In', 'Sign in attempt failed', $dump, Event_Log_Level::Warning);

      return FALSE;
    }
  }

  public function signOut () {
    $this->_session->destroy();
    $this->_response->redirect($this->_signInPageUrl);
  }

  public function showSignInForm () {
    $this->_response->redirect($this->_signInPageUrl . '?referrer=' . $this->_server->urlEncode($this->_request->path()));
  }

  public function showForbiddenForm () {
    require_once(ROOT . $this->_restrictedAccessPage);
  }
}

?>