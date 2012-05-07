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

class Cmf_Route_Table_Prepared_Statement_Library_Mysql {
  const CMF_ROUTE_TABLE_GET_ALL = "
    SELECT rt_id, rt_name, rt_url
      FROM cmf_route_table
      WHERE rt_active = :rt_active
      AND rt_deleted = :rt_deleted
      AND s_id = :s_id
      ORDER BY rt_weight
  ";

  const CMF_ROUTE_TABLE_GET_ROUTE_ARGUMENTS = "
    SELECT rtarg_name, rtarg_mask, rtarg_default_value
      FROM cmf_route_arguments
      WHERE rtarg_active = :rtarg_active
      AND rtarg_deleted = :rtarg_deleted
      AND s_id = :s_id
      AND rt_id = :rt_id
  ";

  const CMF_ROUTE_TABLE_GET_ALL_ROUTE_ARGUMENTS = "
    SELECT rt_id, rtarg_name, rtarg_mask, rtarg_default_value
      FROM cmf_route_arguments
      WHERE rtarg_active = :rtarg_active
      AND rtarg_deleted = :rtarg_deleted
      AND s_id = :s_id
  ";
}

?>