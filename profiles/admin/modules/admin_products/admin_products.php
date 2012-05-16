<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Admin_Products {
  const Prepared_Statement_Library = 'admin_products_prepared_statement_library';

  public static function getAllProducts () {
    $products = array();
    return $products;
  }

  public static function getAllProductTypes ($active = 1, $deleted = 0) {
    $productTypes = array();

    $query = Cmf_Database::call('admin_products_get_all_types', self::Prepared_Statement_Library);
    $query->bindValue(':prdt_active', $active);
    $query->bindValue(':prdt_deleted', $deleted);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();

    $productTypes = $query->fetchAll();

    return $productTypes;
  }
}

?>