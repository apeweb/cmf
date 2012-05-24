<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Cmf_Menu_Link_Prepared_Statement_Library_Mysql {
  const CMF_MENU_LINK_GET = "
    SELECT mn_id, mnl_parent_id, mnl_name, mnl_url, mnl_weight, mnl_css_class, mnl_tooltip, mnl_active
      FROM cmf_menu_link
      WHERE mnl_id = :mnl_id
      AND mnl_deleted = '0'
      AND s_id = :s_id
      LIMIT 1
  ";

  const CMF_MENU_LINK_EXISTS = "
    SELECT count(*)
      FROM cmf_menu_link
      WHERE mnl_id = :mnl_id
      AND mnl_deleted = '0'
      AND s_id = :s_id
      LIMIT 1
  ";

  const CMF_MENU_LINK_UPDATE = "
    UPDATE cmf_menu_link
      SET mnl_parent_id = :mnl_parent_id
          mnl_name = :mnl_name
          mnl_url = :mnl_url
          mnl_weight = :mnl_weight
          mnl_css_class = :mnl_css_class
          mnl_tooltip = :mnl_tooltip
          mnl_active = :mnl_active
      WHERE mnl_id = :mnl_id
        AND s_id = :s_id
      LIMIT 1
  ";

  const CMF_MENU_LINK_DELETE = "
    DELETE FROM cmf_menu_link
      WHERE mnl_id = :mnl_id
        AND s_id = :s_id
      LIMIT 1
  ";
}

?>
