<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Cmf_Log implements iLog {
  static private $_loggingEnabled = FALSE;
  static private $_logs = array();

  const Prepared_Statement_Library = 'cmf_log_prepared_statement_library';

  static public function initialise () {
    if (Config::getValue('site', 'logging', 'enabled') == TRUE) {
      self::$_loggingEnabled = TRUE;

      Log::setLogWriter(new Cmf_Log);

      // Write the logs before the output buffer flushes
      Event_Dispatcher::attachObserver(Cmf_Application_Event::terminate, __CLASS__ . '::commit');

      // If the application didn't terminate successfully, attempt to write any new logs that may help diagnose why
      register_shutdown_function(__CLASS__ . '::commit');
    }
    else {
      Log::disableCurrentLogWriter();
    }
  }

  public function write ($title, $message, $type, $level, $dump) {
    self::$_logs[] = array($title, $message, $type, $level, $dump);
  }

  static public function commit () {
    if (count(self::$_logs) == 0) {
      return;
    }

    $query = Cmf_Database::call('cmf_log_add', self::Prepared_Statement_Library);
    $query->bindValue(':s_id', Config::getValue('site', 'id'), PDO::PARAM_INT);
    $query->bindParam(':log_title', $title, PDO::PARAM_STR);
    $query->bindParam(':log_message', $message, PDO::PARAM_STR);
    $query->bindParam(':log_type', $type, PDO::PARAM_STR);
    $query->bindParam(':log_level', $level, PDO::PARAM_STR);
    $query->bindParam(':log_dump', $dump); // blob

    foreach (self::$_logs as $log) {
      $title = $log[0];
      $message = $log[1];
      $type = $log[2];
      $level = $log[3];
      $dump = serialize($log[4]);
      $query->execute();
    }

    // Prevent the same logs from being written twice
    self::$_logs = array();
  }

  public function getLog ($limit = 9999) {
    $log = array();

    $query = Cmf_Database::call('cmf_log_get', self::Prepared_Statement_Library);
    $query->bindValue(':s_id', Config::getValue('site', 'id'), PDO::PARAM_INT);
    $query->bindValue(':limit', $limit, PDO::PARAM_INT);
    $query->execute();

    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
      $log[] = $row;
    }

    return $log;
  }
}

?>