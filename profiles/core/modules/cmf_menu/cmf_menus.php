<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Cmf_Menus extends Model {
  const Prepared_Statement_Library = 'cmf_menu_prepared_statement_library';

  public static function getAllMenus ($active = TRUE) {
    Assert::isBoolean($active);

    $menus = array();

    $query = Cmf_Database::call('cmf_menu_get_all', self::Prepared_Statement_Library);
    $query->bindValue(':mn_active', $active);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();

    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
      $menus[] = new Cmf_Menu($row);
    }

    return $menus;
  }
}

?>