<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Cmf_Group_Prepared_Statement_Library_Mysql {
  const CMF_GROUP_GET_BY_ID = "
    SELECT g_name
      FROM cmf_group
      WHERE g_id = :g_id
      AND s_id = :s_id
      LIMIT 1
  ";

  const CMF_GROUP_GET_BY_NAME = "
    SELECT g_id, g_name
      FROM cmf_group
      WHERE g_name = :g_name
      AND s_id = :s_id
      LIMIT 1
  ";

  const CMF_GROUP_ADD = "
    INSERT INTO cmf_group
      (g_name, s_id)
      VALUES (:g_name, :s_id)
  ";

  const CMF_GROUP_UPDATE = "
    UPDATE cmf_group
      SET
      g_name = :g_name,
      WHERE g_id = :g_id
      AND s_id = :s_id
      LIMIT 1
  ";

  const CMF_GROUP_HAS_MEMBER = "
    SELECT count(*)
      FROM cmf_group_users
      WHERE g_id = :g_id
      AND u_id = :u_id
      AND s_id = :s_id
      LIMIT 1
  ";
}

?>
