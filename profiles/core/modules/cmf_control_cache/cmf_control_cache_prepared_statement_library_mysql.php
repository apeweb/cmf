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

class Cmf_Control_Cache_Prepared_Statement_Library_Mysql {
  const CMF_CONTROL_CACHE_TRUNCATE = "
    TRUNCATE TABLE cmf_controls_cache
  ";

  const CMF_CONTROL_CACHE_ADD = "
    INSERT INTO cmf_controls_cache (
      ct_name,
      ct_path,
      s_id
    )
    VALUES (
      :ct_name, :ct_path, :s_id
    );
  ";

  const CMF_CONTROL_CACHE_UPDATE = "
    UPDATE cmf_controls_cache
      SET ct_path = :ct_path
      WHERE ct_name = :ct_name
      AND s_id = :s_id
      LIMIT 1
  ";

  const CMF_CONTROL_CACHE_REMOVE = "
    DELETE FROM cmf_controls_cache
      WHERE ct_name = :ct_name
      AND s_id = :s_id
      LIMIT 1
  ";

  const CMF_CONTROL_CACHE_GET_ALL = "
    SELECT ct_name, ct_path
      FROM cmf_controls_cache
      WHERE s_id = :s_id
  ";

  const CMF_CONTROL_CACHE_GET_PATH = "
    SELECT ct_path
      FROM cmf_controls_cache
      WHERE ct_name = :ct_name
      AND s_id = :s_id
      LIMIT 1
  ";
}

?>
