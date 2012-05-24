<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Cmf_Menu_Prepared_Statement_Library_Mysql {
  const CMF_MENU_GET = "
    SELECT mn_name, mn_render_callback
      FROM cmf_menu
      WHERE mn_id = :mn_id
      AND mn_active = '1'
      AND mn_deleted = '0'
      AND s_id = :s_id
      LIMIT 1
  ";

  const CMF_MENU_GET_ACTIVE_LINKS = "
    SELECT mnl_id, mnl_parent_id, mnl_name, mnl_url, mnl_weight, mnl_css_class, mnl_tooltip
      FROM cmf_menu_link
      WHERE mn_id = :mn_id
      AND mnl_active = '1'
      AND mnl_deleted = '0'
      AND s_id = :s_id
      ORDER BY mnl_weight ASC
  ";

  const CMF_MENU_GET_ALL_LINKS = "
    SELECT mnl_id, mnl_parent_id, mnl_name, mnl_url, mnl_weight, mnl_css_class, mnl_tooltip, mnl_active
      FROM cmf_menu_link
      WHERE mn_id = :mn_id
      AND mnl_deleted = '0'
      AND s_id = :s_id
  ";
}

?>
