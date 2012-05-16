<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Cmf_Route_Rewrite {
  const Prepared_Statement_Library = 'cmf_route_rewrite_prepared_statement_library';

  public static function initialise () {
    Event_Dispatcher::attachObserver(Cmf_Route_Table_Event::rewriteActiveRoute, __CLASS__ . '::processRoute');
  }

  public static function processRoute (Cmf_Route $route) {
    $s_id = Config::getValue('site', 'id');

    $query = Cmf_Database::call('cmf_route_rewrite_get_argument_aliases', self::Prepared_Statement_Library);
    $query->bindValue(':rta_active', 1);
    $query->bindValue(':rta_deleted', 0);
    $query->bindValue(':rtarg_active', 1);
    $query->bindValue(':rtarg_deleted', 0);
    $query->bindValue(':rt_active', 1);
    $query->bindValue(':rt_deleted', 0);
    $query->bindValue(':s_id', $s_id);
    $query->bindValue(':rt_name', $route->getName());
    $query->execute();

    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
      try {
        $argument = $route->getArgumentValue($row['rtarg_name']);
      }
      catch (Argument_Exception $ex) {
        continue;
      }

      if ($argument == $row['rta_old_value']) {
        $route->setNewArgumentValue($row['rtarg_name'], $row['rta_new_value']);
      }
    }

    // xxx add functionality to force redirect route to the alias URL
  }

  // xxx add

  // xxx remove

  // xxx etc
}

?>