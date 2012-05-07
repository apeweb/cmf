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


class Cmf_Devel {
  private static $_enbabled = TRUE;

  public static function initialise () {
    Event_Dispatcher::attachObserver(Cmf_Application_Event::terminate, __CLASS__ . '::dumpInfo');
  }

  public static function enabled($bool) {
    self::$_enbabled = $bool;
  }
  
  public static function dumpInfo () {
    if (!self::$_enbabled) return;

    echo '<br />';

    if (function_exists('xdebug_time_index') == TRUE) {
      echo 'Script execution time: ' . xdebug_time_index() . '<br />';
    }

    try {
      $requests = Session::getValue('total_requests');
      $ua = Session::getValue('user_agent');
    }
    catch (Exception $ex) {
      $requests = 0;
      $ua = '';
    }

    Debug::logMessage('Session requests', $requests);
    Debug::logMessage('Session user agent', $ua);

    echo '<h1>Debug Log</h1><ul>';
    foreach (Debug::getLog() as $log) {
      echo '<li><strong>' . $log['title'] . '</strong> ' . $log['message'] . '</li>';
    }
    echo '</ul>';

    echo '<h1>Event Log</h1><ul>';
    foreach (Log::getLogWriter()->getLog(5) as $log) {
      echo '<li><strong>' . $log['log_title'] . '</strong> ' . $log['log_message'] . '</li>';
    }
    echo '</ul>';

    echo '<h1>Database Log</h1><ul>';
    foreach (Cmf_Database_Statement_Log::getLog() as $queryGroupedId => $queries) {
      foreach ($queries as $query) {
        foreach ($query['parameters'] as $key => $value) {
          $query['query'] = str_replace($key, $value, $query['query']);
        }
        echo '<li><strong>' . $query['query'] . '</strong> (' . round($query['time'] * 100, 2) . ')</li>';
      }
    }
    echo '</ul>';

    echo '<h1>Modules Loaded</h1><ul>';
    foreach (Cmf_Module_Manager::getModulesLoaded() as $module) {
      echo '<li>' . $module . '</li>';
    }
    echo '</ul>';
  }
}

?>