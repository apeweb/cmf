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

// xxx need to support g_active and g_deleted

class Cmf_Group {
  private $_groupName = '';
  private $_groupId = 0;

  const Prepared_Statement_Library = 'cmf_group_prepared_statement_library';

  public static function getByGroupId ($id) {
    Assert::isInteger($id);

    $query = Cmf_Database::call('cmf_group_get_by_id', self::Prepared_Statement_Library);
    $query->bindValue(':g_id', $id);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();
    $row = $query->fetch(PDO::FETCH_ASSOC);

    if ($row == FALSE) {
      throw new Argument_Exception("Group does not exist with id '{$id}'");
    }

    $group = new Cmf_Group;
    $group->_groupId = $id;
    $group->_groupName = $row['g_name'];

    return $group;
  }

  public static function getByGroupName ($groupName) {
    Assert::isString($groupName);

    $query = Cmf_Database::call('cmf_group_get_by_name', self::Prepared_Statement_Library);
    $query->bindValue(':g_name', $groupName);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();
    $row = $query->fetch(PDO::FETCH_ASSOC);

    if ($row == FALSE) {
      throw new Argument_Exception("Group '{$groupName}' does not exist");
    }

    $group = new Cmf_Group;
    $group->_groupId = $row['g_id'];
    $group->_groupName = $row['g_name'];

    return $group;
  }

  // Returns a machine comparable group name as they are not case sensitive
  static public function normaliseGroupName ($groupName) {
    return strtolower($groupName);
  }

  public function setGroupName ($groupName) {
    $this->_groupName = $groupName;
  }
  
  public function getGroupName () {
    return $this->_groupName;
  }

  public function getGroupId () {
    return $this->_groupId;
  }

  public function addMember ($userName) {
    // xxx add member
  }

  public function removeMember ($userName) {
    // xxx mark member as deleted
  }

  public function hasMember ($userName) {
    $user = Cmf_User::getByUserName($userName);
    $userId = $user->getUserId();

    $query = Cmf_Database::call('cmf_group_has_member', self::Prepared_Statement_Library);
    $query->bindValue(':g_id', $this->_groupId);
    $query->bindValue(':u_id', $userId);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();

    return (bool) $query->fetchColumn();
  }

  public function getAllMembers () {
    // xxx returns an array of user's names as strings
  }

  // xxx add role

  // xxx remove role
  
  public function getAllPermittedRoles () {
    // xxx returns an array of user's names as strings
  }

  public function save () {
    if ($this->_groupId > 0) {
      $query = Cmf_Database::call('cmf_group_update', self::Prepared_Statement_Library);
      // set
      $query->bindValue(':g_name', $this->_groupName);
      // where
      $query->bindValue(':g_id', $this->_groupId);
      $query->bindValue(':s_id', Config::getValue('site', 'id'));
      $query->execute();
    }
    else {
      $query = Cmf_Database::call('cmf_group_add', self::Prepared_Statement_Library);
      $query->bindValue(':g_name', $this->_groupName);
      $query->bindValue(':s_id', Config::getValue('site', 'id'));
      $query->execute();

      $query = Cmf_Database::call('cmf_group_get_by_name', self::Prepared_Statement_Library);
      $query->bindValue(':g_name', $this->_groupName);
      $query->bindValue(':s_id', Config::getValue('site', 'id'));
      $query->execute();
      $row = $query->fetch(PDO::FETCH_ASSOC);

      if ($row == FALSE) {
        throw new Argument_Exception("Group '{$this->_groupName}' failed to save");
      }

      $this->_groupId = $row['g_id'];
    }
  }
}

?>