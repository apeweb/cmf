<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

// Cmf_User does not deal with anything to do with sessions, it is more of a model than anything
// Cmf_User_Session deals with setting the active user, impersonating users and so on
// Cmf_User_Email_Verification deals with verifying the e-mail address supplied by the user exists

class Cmf_User {
  private $_userName = '';
  private $_password = ''; // this will always be encrypted so no passwords will be revealed
  private $_salt = '';
  private $_authenticated = FALSE;
  private $_userId = 0;

  const Prepared_Statement_Library = 'cmf_user_prepared_statement_library';

  static public function install () {
    Config::setValue(CMF_REGISTRY, 'site', 'security', 'salt', self::_getSalt());
  }

  static private function _getSalt () {
    return Hash::id(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'));
  }

  static public function addUser ($username, $password) {
    $user = new Cmf_User;
    $user->setUserName($username);
    $user->setPassword($password);
    $user->save();
    return $user;
  }

  static public function getByUserId ($id) {
    Assert::isInteger($id);

    $query = Cmf_Database::call('cmf_user_get_by_id', self::Prepared_Statement_Library);
    $query->bindValue(':u_id', $id);
    $query->bindValue(':u_active', 1);
    $query->bindValue(':u_deleted', 0);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();
    $row = $query->fetch(PDO::FETCH_ASSOC);

    if ($row == FALSE) {
      throw new Argument_Exception("User does not exist with id '{$id}'");
    }

    $user = new Cmf_User;
    $user->_userId = $id;
    $user->_userName = $row['u_username'];
    $user->_password = base64_decode($row['u_password']);
    $user->_salt = $row['u_salt'];

    return $user;
  }

  static public function getByUserName ($userName) {
    Assert::isString($userName);

    $query = Cmf_Database::call('cmf_user_get_by_username', self::Prepared_Statement_Library);
    $query->bindValue(':u_username', $userName);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->bindValue(':u_active', 1);
    $query->bindValue(':u_deleted', 0);
    $query->execute();
    $row = $query->fetch(PDO::FETCH_ASSOC);

    if ($row == FALSE) {
      throw new Argument_Exception("User does not exist with username '{$userName}'");
    }

    $user = new Cmf_User;
    $user->_userId = $row['u_id'];
    $user->_userName = $row['u_username'];
    $user->_password = base64_decode($row['u_password']);
    $user->_salt = $row['u_salt'];

    return $user;
  }

  // Returns a machine comparable user name as user names are not case sensitive
  static public function normaliseUserName ($userName) {
    return strtolower($userName);
  }

  // Will be 0 if the user doesn't have an ID yet (ie those that haven't been saved in the db yet)
  public function getUserId () {
    return (int) $this->_userId;
  }

  public function setUserName ($userName) {
    Assert::isString($userName);

    $error = Cmf_User_Library::validateUserName($userName);
    if ($error != '') {
      throw new InvalidArgumentException($error);
    }

    $this->_userName = $userName;
  }

  public function getUserName () {
    return $this->_userName;
  }

  public function setPassword ($password) {
    Assert::isString($password);

    $error = Cmf_User_Library::validatePassword($password);
    if ($error != '') {
      throw new InvalidArgumentException($error);
    }

    $this->_salt = self::_getSalt();
    $this->_password = $this->_saltedPassword($password);
  }

  // For security it is a good practise to be able to get anything that is set, but be aware that
  // the password will be in an encrypted form
  public function getPassword () {
    return $this->_password;
  }

  public function authenticate ($password) {
    Assert::isString($password);

    if ($this->_password == $this->_saltedPassword($password)) {
      $this->_authenticated = TRUE;
    }
    else {
      $this->_authenticated = FALSE;
    }

    return $this->_authenticated;
  }

  private function _saltedPassword ($password) {
    Assert::isString($password);
    return Hash::password($password . $this->_salt, Config::getValue('site', 'security', 'salt'), 100);
  }

  public function hasAuthenticated () {
    return $this->_authenticated;
  }

  public function getAllGroupMemberships () {
    $groups = array();

    $query = Cmf_Database::call('cmf_user_get_all_group_memberships', self::Prepared_Statement_Library);
    $query->bindValue(':u_id', $this->_userId);
    $query->bindValue(':u_active', 1);
    $query->bindValue(':u_deleted', 0);

    $query->bindValue(':s_id', Config::getValue('site', 'id'));

    $query->bindValue(':gu_active', 1);
    $query->bindValue(':gu_deleted', 0);

    $query->bindValue(':g_active', 1);
    $query->bindValue(':g_deleted', 0);
    $query->execute();

    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
      $groups[] = $row['g_name'];
    }

    return $groups;
  }

  public function getAllPermittedRoles () {
    $roles = array();

    $query = Cmf_Database::call('cmf_user_get_all_permitted_roles', self::Prepared_Statement_Library);
    $query->bindValue(':u_id', $this->_userId);
    $query->bindValue(':u_active', 1);
    $query->bindValue(':u_deleted', 0);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->bindValue(':gu_active', 1);
    $query->bindValue(':gu_deleted', 0);
    $query->bindValue(':g_active', 1);
    $query->bindValue(':g_deleted', 0);
    $query->bindValue(':grl_active', 1);
    $query->bindValue(':grl_deleted', 0);
    $query->bindValue(':rl_active', 1);
    $query->bindValue(':rl_deleted', 0);
    $query->execute();

    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
      $roles[] = $row['rl_name'];
    }

    return $roles;
  }

  public function flagAsInactive () {
    // xxx finish
  }

  public function flagAsActive () {
    // xxx finish
  }

  public function flagAsDeleted () {
    // There is no "flag as not deleted" as a special module deals with undoing deletes
    // xxx finish
  }

  // When a user is modified, use the save method to add a new user to the database or
  public function save () {
    if ($this->_userId > 0) {
      $query = Cmf_Database::call('cmf_user_update', self::Prepared_Statement_Library);
      // set
      $query->bindValue(':u_username', $this->_userName);
      $query->bindValue(':u_password', base64_encode($this->_password));
      $query->bindValue(':u_salt', $this->_salt);
      // where
      $query->bindValue(':u_id', $this->_userId);
      $query->bindValue(':s_id', Config::getValue('site', 'id'));
      $query->execute();
    }
    else {
      $query = Cmf_Database::call('cmf_user_add', self::Prepared_Statement_Library);
      $query->bindValue(':u_username', $this->_userName);
      $query->bindValue(':u_password', base64_encode($this->_password));
      $query->bindValue(':u_salt', $this->_salt);
      $query->bindValue(':s_id', Config::getValue('site', 'id'));
      $query->execute();
      
      $query = Cmf_Database::call('cmf_user_get_by_username', self::Prepared_Statement_Library);
      $query->bindValue(':u_username', $this->_userName);
      $query->bindValue(':s_id', Config::getValue('site', 'id'));
      $query->execute();
      $row = $query->fetch(PDO::FETCH_ASSOC);

      if ($row == FALSE) {
        throw new Argument_Exception("User '{$this->_userName}' failed to save");
      }

      $this->_userId = $row['u_id'];
    }
  }
}

?>