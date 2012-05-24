<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Admin_Menu_Manager_Prepared_Statement_Library_Mysql {
  const ADMIN_MENU_UPDATE_LINK = "
    UPDATE cmf_menu_link
      SET mnl_weight = :mnl_weight,
          mnl_parent_id = :mnl_parent_id,
          mnl_active = :mnl_active
      WHERE mnl_id = :mnl_id
      AND s_id = :s_id
      LIMIT 1
  ";
}

?>
