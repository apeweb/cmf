<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

// Cmf_Authorisation will be used for all sorts so has to be generic
class Cmf_Authorisation {
  private $_usersDeniedAccess = array();
  private $_groupsDeniedAccess = array();
  private $_rolesDeniedAccess = array();

  private $_usersAllowedAccess = array();
  private $_groupsAllowedAccess = array();
  private $_rolesAllowedAccess = array();

  public function isUserAuthorised ($userName) {
    Assert::isString($userName);

    $user = Cmf_User::getByUserName($userName);

    if ($this->_isUserServiceAccount($user->getUserId()) == TRUE) {
      return TRUE;
    }

    $userName = Cmf_User::normaliseUserName($userName);
    $userGroups = $user->getAllGroupMemberships();
    $userRoles = $user->getAllPermittedRoles();

    if (count($this->_usersDeniedAccess) > 0 && in_array($userName, $this->_usersDeniedAccess) == TRUE) {
      return FALSE;
    }

    if (count($this->_groupsDeniedAccess) > 0 && count(array_intersect($userGroups, $this->_groupsDeniedAccess)) > 0) {
      return FALSE;
    }

    if (count($this->_rolesDeniedAccess) > 0 && count(array_intersect($userRoles, $this->_rolesDeniedAccess)) > 0) {
      return FALSE;
    }

    if (count($this->_usersAllowedAccess) > 0 && in_array($userName, $this->_usersAllowedAccess) == TRUE) {
      return TRUE;
    }

    if (count($this->_groupsAllowedAccess) > 0 && count(array_intersect($userGroups, $this->_groupsAllowedAccess)) > 0) {
      return TRUE;
    }

    if (count($this->_rolesAllowedAccess) > 0 && count(array_intersect($userRoles, $this->_rolesAllowedAccess)) > 0) {
      return TRUE;
    }

    return FALSE;
  }

  private function _isUserServiceAccount ($userId) {
    Assert::isInteger($userId);
    
    // This user has access regardless
    try {
      $serviceAccountUserId = Config::getValue('site', 'security', 'serviceAccountUserId');

      if ($serviceAccountUserId > 0 && $userId == $serviceAccountUserId) {
        return TRUE;
      }
    }
    catch (Exception $ex) {}
    
    return FALSE;
  }

  public function isGroupAuthorised ($groupName) {
    Assert::isString($groupName);

    // xxx finish

    return FALSE;
  }

  public function isRoleAuthorised ($roleName) {
    Assert::isString($roleName);

    // xxx finish

    return FALSE;
  }

  public function getRoleAccess () {
    // xxx get a list of all roles with access based on the groups that have access
    $rolesWithAccess = $this->_rolesAllowedAccess;

    // xxx remove any that have been denied access

    return $rolesWithAccess;
  }

  public function getGroupAccess () {
    return $this->_groupsAllowedAccess;
  }

  public function getUserAccess () {
    // xxx get a list of all users with access based on the groups that have access, minus any that have been denied access
    // xxx also include the sevice account with the list of users who have access
    return $this->_usersAllowedAccess;
  }

  public function grantRoleAccess ($roleName) {
    Assert::isString($roleName);

    // xxx check role exists

    $this->_rolesAllowedAccess[] = $roleName;
  }

  public function grantGroupAccess ($groupName) {
    Assert::isString($groupName);

    // xxx check group exists

    $this->_groupsAllowedAccess[] = $groupName;
  }

  public function grantUserAccess ($userName) {
    Assert::isString($userName);

    // xxx check user exists

    $this->_usersAllowedAccess[] = $userName;
  }

  public function denyRoleAccess ($roleName) {
    Assert::isString($roleName);

    // xxx check role exists

    $this->_rolesDeniedAccess[] = $roleName;
  }

  public function denyGroupAccess ($groupName) {
    Assert::isString($groupName);

    // xxx check group exists

    $this->_groupsDeniedAccess[] = $groupName;
  }

  public function denyUserAccess ($userName) {
    Assert::isString($userName);

    // xxx check user exists

    $this->_usersDeniedAccess[] = $userName;
  }
}

?>