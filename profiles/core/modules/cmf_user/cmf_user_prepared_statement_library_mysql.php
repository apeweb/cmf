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

// xxx need to support u_active and u_deleted

class Cmf_User_Prepared_Statement_Library_Mysql {
  const CMF_USER_GET_BY_ID = "
    SELECT u_username, u_password, u_salt
      FROM cmf_user
      WHERE u_id = :u_id
      AND s_id = :s_id
      AND u_active = :u_active
      AND u_deleted = :u_deleted
      LIMIT 1
  ";

  const CMF_USER_GET_BY_USERNAME = "
    SELECT u_id, u_username, u_password, u_salt
      FROM cmf_user
      WHERE u_username = :u_username
      AND s_id = :s_id
      AND u_active = :u_active
      AND u_deleted = :u_deleted
      LIMIT 1
  ";

  const CMF_USER_ADD = "
    INSERT INTO cmf_user
      (u_username, u_password, u_salt, s_id)
      VALUES (:u_username, :u_password, :u_salt, :s_id)
  ";

  const CMF_USER_UPDATE = "
    UPDATE cmf_user
      SET u_username = :u_username,
          u_password = :u_password,
          u_salt = :u_salt
      WHERE u_id = :u_id
      AND s_id = :s_id
      LIMIT 1
  ";

  const CMF_USER_GET_ALL_GROUP_MEMBERSHIPS = "
    SELECT g.g_id, g.g_name
      FROM cmf_user u
      INNER JOIN cmf_group_users gu ON gu.u_id = u.u_id
        AND gu.s_id = :s_id
        AND gu.gu_active = :gu_active
        AND gu.gu_deleted = :gu_deleted
      INNER JOIN cmf_group g ON gu.g_id = g.g_id
        AND g.s_id = :s_id
        AND g.g_active = :g_active
        AND g.g_deleted = :g_deleted
      WHERE u.s_id = :s_id
      AND u.u_id = :u_id
      AND u.u_active = :u_active
      AND u.u_deleted = :u_deleted
  ";

  const CMF_USER_GET_ALL_PERMITTED_ROLES = "
    SELECT rl.rl_id, rl.rl_name
      FROM cmf_user u

      INNER JOIN cmf_group_users gu ON gu.u_id = u.u_id
        AND gu.s_id = :s_id
        AND gu.gu_active = :gu_active
        AND gu.gu_deleted = :gu_deleted

      INNER JOIN cmf_group g ON gu.g_id = g.g_id
        AND g.s_id = :s_id
        AND g.g_active = :g_active
        AND g.g_deleted = :g_deleted

      INNER JOIN cmf_group_roles grl ON grl.g_id = g.g_id
        AND grl.s_id = :s_id
        AND grl.grl_active = :grl_active
        AND grl.grl_deleted = :grl_deleted

      INNER JOIN cmf_role rl ON grl.rl_id = rl.rl_id
        AND rl.s_id = :s_id
        AND rl.rl_active = :rl_active
        AND rl.rl_deleted = :rl_deleted
        
      WHERE u.s_id = :s_id
        AND u.u_id = :u_id
        AND u.u_active = :u_active
        AND u.u_deleted = :u_deleted
  ";
}

?>
