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

class Cmf_Route_Path_Access {
  const Prepared_Statement_Library = 'cmf_route_path_access_prepared_statement_library';

  public static function initialise () {
    Event_Dispatcher::attachObserver(Cmf_Route_Table_Event::rewriteActiveRoute, __CLASS__ . '::processRoute');
  }

  public static function processRoute (Cmf_Route $route) {
    $query = Cmf_Database::call('cmf_route_path_access_get_route_access', self::Prepared_Statement_Library);
    $query->bindValue(':rtpa_active', 1);
    $query->bindValue(':rtpa_deleted', 0);
    $query->bindValue(':rt_active', 1);
    $query->bindValue(':rt_deleted', 0);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->bindValue(':rt_name', $route->getName());
    $query->execute();

    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
      if ($row['rtpa_access'] == TRUE) {
        $route->setPathAccessAllowed($row['rtpa_path']);
      }
      else {
        $route->setPathAccessDenied($row['rtpa_path']);
      }
    }
  }

  // xxx add methods for managing route access to paths
}

?>