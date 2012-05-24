<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Cmf_Database_Prepared_Statement_Library_Mysql {
  #Region "Registry"
  const CMF_REGISTRY_GET_ALL = "
    SELECT r_name, r_value
      FROM cmf_registry
      WHERE r_active = :r_active
      AND r_deleted = :r_deleted
      AND s_id = :s_id
  ";

  const CMF_REGISTRY_SITE_ID_CHECK = "
    SELECT COUNT(*) AS installed
      FROM cmf_registry
      WHERE s_id = :s_id
      LIMIT 1
  ";

  const CMF_REGISTRY_KEY_GET_ID = "
    SELECT r_id
      FROM cmf_registry
      WHERE r_name = :r_name
      AND r_deleted = '0'
      AND s_id = :s_id
      LIMIT 1
  ";

  const CMF_REGISTRY_KEY_GET_VALUE = "
    SELECT r_value
      FROM cmf_registry
      WHERE r_id = :r_id
      AND s_id = :s_id
      LIMIT 1
  ";

  const CMF_REGISTRY_KEY_ADD = "
    INSERT INTO cmf_registry
      (r_name, r_value, s_id)
      VALUES (:r_name, :r_value, :s_id)
  ";

  const CMF_REGISTRY_KEY_UPDATE = "
    UPDATE cmf_registry
      SET r_value = :r_value
      WHERE r_id = :r_id
      AND s_id = :s_id
      LIMIT 1
  ";

  const CMF_REGISTRY_KEY_ENABLE = "
    UPDATE cmf_registry
      SET r_active = '1',
      WHERE r_id = :r_id
      AND s_id = :s_id
      LIMIT 1
  ";

  const CMF_REGISTRY_KEY_DISABLE = "
    UPDATE cmf_registry
      SET r_active = '0',
      WHERE r_id = :r_id
      AND s_id = :s_id
      LIMIT 1
  ";

  const CMF_REGISTRY_KEY_DELETE = "
    UPDATE cmf_registry
      SET
      r_active = '0',
      r_deleted = '1'
      WHERE r_id = :r_id
      AND s_id = :s_id
      LIMIT 1
  ";
  #End Region
  
  #Region "Module Manager"
  const CMF_MODULES_GET_ALL = "
    SELECT m_path, m_name, m_initialise_function
      FROM cmf_modules
      WHERE m_installed = :m_installed
      AND m_active = :m_active
      AND m_deleted = :m_deleted
      AND s_id = :s_id
      ORDER BY m_weight ASC, m_initialise_function ASC
  ";
  #End Region
}

?>
