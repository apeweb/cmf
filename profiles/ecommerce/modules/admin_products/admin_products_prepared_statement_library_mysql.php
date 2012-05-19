<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

define('Admin_Products_Prepared_Statement_Library_Mysql', 'Admin_Products_Prepared_Statement_Library_Mysql');
class Admin_Products_Prepared_Statement_Library_Mysql {
  const ADMIN_PRODUCTS_GET_ALL_TYPES = "
    SELECT *
      FROM shop_product_types
      WHERE prdt_active = :prdt_active
      AND prdt_deleted = :prdt_deleted
      AND s_id = :s_id
      LIMIT 1
  ";
}

?>
