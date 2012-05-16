<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Cmf_Database_Statement_Log extends PDOStatement {
  static private $_log = array();

  private $_connection = NULL;
  private $_args = array();
  private $_time = 0;

  protected function __construct ($connectionName) {
    $this->_connection = Database::getConnection($connectionName);
  }

  static public function initialise () {
    static $initialised = FALSE;

    if ($initialised == TRUE) {
      return;
    }

    $connections = Database::getConnectionList();

    foreach ($connections as $connectionName) {
      $db = Database::getConnection($connectionName);
      $db->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('Cmf_Database_Statement_Log', array($connectionName)));
    }

    $initialised = TRUE;
  }

  // xxx need to test the $length and $driverOptions
  public function bindParam ($parameter, &$value, $dataType = PDO::PARAM_STR, $length = NULL, $driverOptions = NULL) {
    $this->_args[$parameter] = $value;
    parent::bindParam($parameter, $value, $dataType, $length, $driverOptions);
  }

  public function bindValue ($parameter, $value, $dataType = PDO::PARAM_STR) {
    $this->_args[$parameter] = $value;
    parent::bindValue($parameter, $value, $dataType);
  }

  public function execute ($args = NULL) {
    $queryId = uniqid();

    if (func_num_args() < 1) {
      Timer::start('Database Statement ' . $queryId);
      $result = parent::execute();
      $time = Timer::stop('Database Statement ' . $queryId);

      self::_log($this->queryString, $this->_args, $time);

      return $result;
    }
    elseif (is_array($args) == TRUE) {
      Timer::start('Database Statement ' . $queryId);
      $result = parent::execute($args);
      $time = Timer::stop('Database Statement ' . $queryId);

      self::_log($this->queryString, $args, $time);

      return $result;
    }
    else {
      $args = func_get_args();

      Timer::start('Database Statement ' . $queryId);
      $result = eval('return parent::execute($args);');
      $time = Timer::stop('Database Statement ' . $queryId);

      self::_log($this->queryString, $args, $time);

      return $result;
    }
  }

  static private function _log ($queryString, $args, $time) {
    $groupId = md5($queryString);
    self::$_log[$groupId][] = array(
      'query' => $queryString,
      'parameters' => $args,
      'time' => $time
    );
  }

  static public function getLog () {
    return self::$_log;
  }
}

?>