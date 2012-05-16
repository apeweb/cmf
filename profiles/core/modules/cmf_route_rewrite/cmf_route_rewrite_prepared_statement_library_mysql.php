<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Cmf_Route_Rewrite_Prepared_Statement_Library_Mysql {
  const CMF_ROUTE_REWRITE_GET_ARGUMENT_ALIASES = "
    SELECT rtarg_name, rta_old_value, rta_new_value
      FROM cmf_route_table rt
      INNER JOIN cmf_route_alias rta ON rt.rt_id = rta.rt_id
        AND rta_active = :rta_active
        AND rta_deleted = :rta_deleted
        AND rta.s_id = :s_id
      INNER JOIN cmf_route_arguments rtarg ON rta.rtarg_id = rtarg.rtarg_id
        AND rtarg_active = :rtarg_active
        AND rtarg_deleted = :rtarg_deleted
        AND rta.s_id = :s_id
      WHERE rt_active = :rt_active
        AND rt_deleted = :rt_deleted
        AND rt.s_id = :s_id
        AND rt_name = :rt_name
  ";
}

?>