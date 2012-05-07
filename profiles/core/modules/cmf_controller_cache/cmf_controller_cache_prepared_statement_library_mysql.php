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

class Cmf_Controller_Cache_Prepared_Statement_Library_Mysql {
  const CMF_CONTROLLER_CACHE_TRUNCATE = "
    TRUNCATE TABLE cmf_controllers_cache
  ";

  const CMF_CONTROLLER_CACHE_ADD = "
    INSERT INTO cmf_controllers_cache (
      cc_name,
      cc_path,
      s_id
    )
    VALUES (
      :cc_name, :cc_path, :s_id
    );
  ";

  const CMF_CONTROLLER_CACHE_UPDATE = "
    UPDATE cmf_controllers_cache
      SET cc_path = :cc_path
      WHERE cc_name = :cc_name
      AND s_id = :s_id
      LIMIT 1
  ";

  const CMF_CONTROLLER_CACHE_REMOVE = "
    DELETE FROM cmf_controllers_cache
      WHERE cc_name = :cc_name
      AND s_id = :s_id
      LIMIT 1
  ";

  const CMF_CONTROLLER_CACHE_GET_ALL = "
    SELECT cc_name, cc_path
      FROM cmf_controllers_cache
      WHERE s_id = :s_id
  ";

  const CMF_CONTROLLER_CACHE_GET_PATH = "
    SELECT cc_path
      FROM cmf_controllers_cache
      WHERE cc_name = :cc_name
      AND s_id = :s_id
      LIMIT 1
  ";
}

?>
