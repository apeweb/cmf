<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

require_once(ROOT . 'framework/data/sql_client.php');
require_once(ROOT . 'framework/security/encryption.php');

// xxx username should be userName
/**
 * This class used to handle the sanitisation and requirements of passwords
 * but there were a few problems with this including passing unhashed
 * passwords about. Therefore, it is the requirement of the page that
 * signs in the user to check for any requirements that page wishes to
 * impose.
 */

define('User', NULL);

class User {
  private $_db = Database;

  /**
   * Holds the last error message for the last error that occured
   */
  protected $_lastErrorMessage = '';

  /**
   * @return (void)
   */
  public function __construct () {
    $this->_db = new Database;
  }

  public function __get ($variableName) {
    switch ($variableName) {
      case 'error':
        return $this->_lastErrorMessage;

      default:
        // legacy error reporting
        trigger_error("Undefined variable: '" . $variableName . "()'", E_USER_NOTICE);
    }
  }

  /**
   * @return (string) last error message
   */
  public function error () {
    return $this->_lastErrorMessage;
  }

  /**
   * Checks to see if the user exists.
   *
   * @return (boolean) whether the user exists or not
   */
  public function exists ($username, $password) {
    $dataSet = Data_Set;
    $exists = FALSE;

    $this->_cleanUsername($username);

    if (empty($username) == TRUE) {
      $this->_lastErrorMessage = 'The username supplied is not valid, usernames must contain one or more alphanumeric characters only.';
      return FALSE;
    }
    elseif (strlen($username) > 30) {
      $this->_lastErrorMessage = 'The username supplied is too long, the maximum length for a username is 20 characters long.';
      return FALSE;
    }

    $sp = new Stored_Procedure('user_exists');
    $sp->addParameter('@username', $username, $this->_db->type['nvarchar']);
    $sp->addParameter('@password', $password, $this->_db->type['nvarchar']);

    $dataSet = $this->_db->execute($sp);

    if ($dataSet->tables->count() > 0 && $dataSet->table(0)->rows->count() > 0) {
      $exists = (bool) $dataSet->table(0)->row(0)->exists;
    }

    return $exists;
  }

  /**
   * Returns the normalised username for display
   *
   * @return (string) the username
   */
  public function normaliseUserName ($username, $password) {
    $dataSet = Data_Set;

    $this->_cleanUsername($username);

    if (empty($username) == TRUE) {
      $this->_lastErrorMessage = 'The username supplied is not valid, usernames must contain one or more alphanumeric characters only.';
      return '';
    }
    elseif (strlen($username) > 30) {
      $this->_lastErrorMessage = 'The username supplied is too long, the maximum length for a username is 20 characters long.';
      return '';
    }

    $sp = new Stored_Procedure('user_get_username');
    $sp->addParameter('@username', $username, $this->_db->type['nvarchar']);
    $sp->addParameter('@password', $password, $this->_db->type['nvarchar']);

    $dataSet = $this->_db->execute($sp);

    if ($dataSet->tables->count() > 0 && $dataSet->table(0)->rows->count() > 0) {
      return $dataSet->table(0)->row(0)->username;
    }

    return '';
  }

  /**
   * Returns the user id of the user
   *
   * @return (int) the user id
   */
  public function userId ($username, $password) {
    $dataSet = Data_Set;

    $this->_cleanUsername($username);

    if (empty($username) == TRUE) {
      $this->_lastErrorMessage = 'The username supplied is not valid, usernames must contain one or more alphanumeric characters only.';
      return 0;
    }
    elseif (strlen($username) > 30) {
      $this->_lastErrorMessage = 'The username supplied is too long, the maximum length for a username is 20 characters long.';
      return 0;
    }

    $sp = new Stored_Procedure('user_get_user_id');
    $sp->addParameter('@username', $username, $this->_db->type['nvarchar']);
    $sp->addParameter('@password', $password, $this->_db->type['nvarchar']);

    $dataSet = $this->_db->execute($sp);

    if ($dataSet->tables->count() > 0 && $dataSet->table(0)->rows->count() > 0) {
      return (int) $dataSet->table(0)->row(0)->user_id;
    }

    return 0;
  }

  /**
   * Checks to see whether the user account is enabled or not.
   *
   * @return (boolean) whether the user is enabled or not
   */
  public function isEnabled ($username, $password) {
    $dataSet = Data_Set;
    $enabled = FALSE;

    $this->_cleanUsername($username);

    if (empty($username) == TRUE) {
      $this->_lastErrorMessage = 'The username supplied is not valid, usernames must contain one or more alphanumeric characters only.';
      return FALSE;
    }
    elseif (strlen($username) > 30) {
      $this->_lastErrorMessage = 'The username supplied is too long, the maximum length for a username is 20 characters long.';
      return FALSE;
    }

    $cmd = new Stored_Procedure('user_enabled');
    $cmd->addParameter('@username', $username, $this->_db->type['nvarchar']);
    $cmd->addParameter('@password', $password, $this->_db->type['nvarchar']);

    $dataSet = $this->_db->execute($cmd);

    if ($dataSet->tables->count() > 0 && $dataSet->table(0)->rows->count() > 0) {
      $enabled = (bool) $dataSet->table(0)->row(0)->enabled;
    }

    return $enabled;
  }

  /**
   * Returns the relative url of the home for the user
   * @param string $username
   * @param string $password
   * @return string the relative url of the home for the user
   */
  public function homePath ($username, $password) {
    $dataSet = Data_Set;
    $homePath = '';

    $this->_cleanUsername($username);

    if (empty($username) == TRUE) {
      $this->_lastErrorMessage = 'The username supplied is not valid, usernames must contain one or more alphanumeric characters only.';
      return FALSE;
    }
    elseif (strlen($username) > 30) {
      $this->_lastErrorMessage = 'The username supplied is too long, the maximum length for a username is 20 characters long.';
      return FALSE;
    }

    $cmd = new Stored_Procedure('user_select_home_path');
    $cmd->addParameter('@username', $username, $this->_db->type['nvarchar']);
    $cmd->addParameter('@password', $password, $this->_db->type['nvarchar']);

    $dataSet = $this->_db->execute($cmd);

    if ($dataSet->tables->count() > 0 && $dataSet->table(0)->rows->count() > 0) {
      $homePath = $dataSet->table(0)->row(0)->home_path;
    }

    return $homePath;
  }

  /**
   * This function allows us to get a customer id for a user account,
   * whether the account be for a customer, client or agent.
   *
   * @return (integer) the customer (agent, client etc.) id
   */
  public function getCustomerId ($username, $password) {
    $dataSet = Data_Set;
    $customerId = 0;

    $this->_cleanUsername($username);

    if (empty($username) == TRUE) {
      $this->_lastErrorMessage = 'The username supplied is not valid, usernames must contain one or more alphanumeric characters only.';
      return FALSE;
    }
    elseif (strlen($username) > 30) {
      $this->_lastErrorMessage = 'The username supplied is too long, the maximum length for a username is 20 characters long.';
      return FALSE;
    }

    $cmd = new Stored_Procedure('user_get_customer_id');
    $cmd->addParameter('@username', $username, $this->_db->type['nvarchar']);
    $cmd->addParameter('@password', $password, $this->_db->type['nvarchar']);

    $dataSet = $this->_db->execute($cmd);

    if ($dataSet->tables->count() > 0 && $dataSet->tables(0)->rows->count() > 0) {
      $customerId = (int) $dataSet->tables(0)->rows(0)->customer_id;
    }

    return $customerId;
  }

  /**
   * This function adds users to the system, with the option of defining
   * the user's relationship to a customer and if they are disabled for any
   * reason.
   *
   * @return (integer) the user's new id
   */
  public function add ($username, $password, $customerId = 0, $disabled = FALSE) {
    $dataSet = Data_Set;
    $userId = 0;

    $this->_cleanUsername($username);

    if (empty($username) == TRUE) {
      $this->_lastErrorMessage = 'The username supplied is not valid, usernames must contain one or more alphanumeric characters only.';
      return 0;
    }
    elseif (strlen($username) > 30) {
      $this->_lastErrorMessage = 'The username supplied is too long, the maximum length for a username is 20 characters long.';
      return 0;
    }

    $cmd = new Stored_Procedure('user_add');
    $cmd->addParameter('@username', $username, $this->_db->type['nvarchar']);
    $cmd->addParameter('@password', $password, $this->_db->type['nvarchar']);
    $cmd->addParameter('@customer_id', $customerId, $this->_db->type['int']);
    $cmd->addParameter('@disabled', $disabled, $this->_db->type['int']);

    $dataSet = $this->_db->execute($cmd);

    // xxx need to test
    if ($dataSet->tables->count() > 0 && $dataSet->tables(0)->rows->count() > 0) {
      $userId = (int) $dataSet->tables(0)->rows(0)->user_id;
    }

    return $userId;
  }

  /**
   * This function allows users to change their usernames, this is only
   * really important if usernames are e-mail addresses but can be used for
   * other scenarios too.
   *
   * It is important that the password is required here, as a user could
   * belong to 2 different customers therefore having 2 accounts opposed to
   * just the 1 so the password identifies which they belong to.
   *
   * @return (boolean) whether the username changed successfully or not
   */
  public function changeUsername ($oldUsername, $password, $newUsername) {
    $this->_cleanUsername($oldUsername);
    $this->_cleanUsername($newUsername);

    if (empty($oldUsername) == TRUE) {
      $this->_lastErrorMessage = 'The current username supplied is not valid, usernames must contain one or more alphanumeric characters only.';
      return FALSE;
    }
    if (empty($newUsername) == TRUE) {
      $this->_lastErrorMessage = 'The new username supplied is not valid, usernames must contain one or more alphanumeric characters only.';
      return FALSE;
    }
    elseif (strlen($oldUsername) > 20) {
      $this->_lastErrorMessage = 'The current username supplied is too long, the maximum length for a username is 20 characters long.';
      return FALSE;
    }
    elseif (strlen($newUsername) > 20) {
      $this->_lastErrorMessage = 'The new username supplied is too long, the maximum length for a username is 20 characters long.';
      return FALSE;
    }

    $cmd = new Stored_Procedure('user_change_username');
    $cmd->addParameter('@old_username', $oldUsername, $this->_db->type['nvarchar']);
    $cmd->addParameter('@new_username', $newUsername, $this->_db->type['nvarchar']);
    $cmd->addParameter('@password', $password, $this->_db->type['nvarchar']);

    return $this->_db->executeNonQuery($cmd);
  }

  /**
   * Changes the user's password
   *
   * @return (boolean) whether the password changed successfully or not
   */
  public function changePassword ($username, $oldPassword, $newPassword) {
    $this->_cleanUsername($username);

    if (empty($username) == TRUE) {
      $this->_lastErrorMessage = 'The username supplied is not valid, usernames must contain one or more alphanumeric characters only.';
      return FALSE;
    }
    elseif (strlen($username) > 30) {
      $this->_lastErrorMessage = 'The username supplied is too long, the maximum length for a username is 20 characters long.';
      return FALSE;
    }

    $cmd = new Stored_Procedure('user_change_username');
    $cmd->addParameter('@username', $username, $this->_db->type['nvarchar']);
    $cmd->addParameter('@old_username', $oldPassword, $this->_db->type['nvarchar']);
    $cmd->addParameter('@new_password', $newPassword, $this->_db->type['nvarchar']);

    return $this->_db->executeNonQuery($cmd);
  }

  /**
   * Disables a user preventing them from signing in with a session
   *
   * @return (boolean) whether the user was disabled successfully or not
   */
  public function disable ($username, $password) {
    $this->_cleanUsername($username);

    if (empty($username) == TRUE) {
      $this->_lastErrorMessage = 'The username supplied is not valid, usernames must contain one or more alphanumeric characters only.';
      return FALSE;
    }
    elseif (strlen($username) > 30) {
      $this->_lastErrorMessage = 'The username supplied is too long, the maximum length for a username is 20 characters long.';
      return FALSE;
    }

    $cmd = new Stored_Procedure('user_disable');
    $cmd->addParameter('@username', $username, $this->_db->type['nvarchar']);
    $cmd->addParameter('@password', $password, $this->_db->type['nvarchar']);

    return $this->_db->executeNonQuery($cmd);
  }

  public function hasPrivilege ($username, $password, $privilege) {
    $privileged = FALSE;

    $privileges = $this->privileges($username, $password);

    foreach ($privileges->rows as $row) {
      if ($row->name == $privilege) {
        $privileged = TRUE;
      }
    }

    return $privileged;
  }

  public function privileges ($username, $password) {
    $dataSet = Data_Set;

    $this->_cleanUsername($username);

    if (empty($username) == TRUE) {
      $this->_lastErrorMessage = 'The username supplied is not valid, usernames must contain one or more alphanumeric characters only.';
      return 0;
    }
    elseif (strlen($username) > 30) {
      $this->_lastErrorMessage = 'The username supplied is too long, the maximum length for a username is 20 characters long.';
      return 0;
    }

    $cmd = new Stored_Procedure('user_get_privileges');
    $cmd->addParameter('@username', $username, $this->_db->type['nvarchar']);
    $cmd->addParameter('@password', $password, $this->_db->type['nvarchar']);

    $dataSet = $this->_db->execute($cmd);

    return $dataSet->tables(0);
  }

  private function _cleanUsername (&$username) {
    $username = trim($username);

    // supports e-mail addresses only
    if (defined('USERNAME_EMAIL_ADDRESS') && USERNAME_EMAIL_ADDRESS == TRUE) {
      $username = preg_replace('/[^a-z0-9@\.\+\-_]/i', '', $username);
    }
    // supports both e-mail addresses and usernames
    elseif (defined('USERNAME_ALLOW_EMAIL_ADDRESS') && USERNAME_ALLOW_EMAIL_ADDRESS == TRUE) {
      $username = preg_replace('/[^a-z0-9\s@\.\+\-_]/i', '', $username);
    }
    // allows usernames only
    else {
      $username = preg_replace('/[^a-z0-9\s\-_]/i', '', $username);
    }
  }
}

?>