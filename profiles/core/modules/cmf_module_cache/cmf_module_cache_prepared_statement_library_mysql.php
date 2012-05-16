<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

define('Cmf_Controller_Cache_Prepared_Statement_Library_Mysql', 'Cmf_Controller_Cache_Prepared_Statement_Library_Mysql');
class Cmf_Module_Cache_Prepared_Statement_Library_Mysql {
  const CMF_MODULE_CACHE_TRUNCATE = "
    TRUNCATE TABLE cmf_modules
  ";

  const CMF_MODULE_CACHE_ADD = "
    INSERT INTO cmf_modules (
      m_path,
      m_name,
      m_initialise_function,
      m_weight,
      m_ini,
      s_id
    )
    VALUES (
      :m_path, :m_name, :m_initialise_function, :m_weight, :m_ini, :s_id
    );
  ";

  const CMF_MODULE_CACHE_UPDATE = "
    UPDATE cmf_modules
      SET m_path = :m_path
      WHERE m_name = :m_name
      AND s_id = :s_id
      LIMIT 1
  ";

  const CMF_MODULE_CACHE_REMOVE = "
    DELETE FROM cmf_modules
      WHERE m_name = :m_name
      AND s_id = :s_id
      LIMIT 1
  ";

  const CMF_MODULE_CACHE_GET_ALL = "
    SELECT m_name, m_path
      FROM cmf_modules
      WHERE s_id = :s_id
  ";

  const CMF_MODULE_CACHE_GET_PATH = "
    SELECT m_path
      FROM cmf_modules
      WHERE m_name = :m_name
      AND s_id = :s_id
      LIMIT 1
  ";
}

?>
