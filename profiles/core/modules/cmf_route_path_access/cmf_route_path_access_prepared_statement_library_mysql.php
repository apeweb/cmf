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

class Cmf_Route_Path_Access_Prepared_Statement_Library_Mysql {
  const CMF_ROUTE_PATH_ACCESS_GET_ROUTE_ACCESS = "
    SELECT rtpa_path, rtpa_access
      FROM cmf_route_table rt
      INNER JOIN cmf_route_path_access rtpa ON rt.rt_id = rtpa.rt_id
        AND rtpa_active = :rtpa_active
        AND rtpa_deleted = :rtpa_deleted
        AND rtpa.s_id = :s_id
      WHERE rt_active = :rt_active
        AND rt_deleted = :rt_deleted
        AND rt.s_id = :s_id
        AND rt_name = :rt_name
  ";
}

?>