<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

/**
 * The CMF database is a helper that makes connecting to databases using settings from
 * the settings file easy, it does not extend the Database class as it really has nothing to do
 * with the Database class
 */
class Cmf_Database {
  static private $_isConnected = FALSE;

  static public function isConnected () {
    return self::$_isConnected;
  }

  static public function connect ($connectionName = 'default') {
    Assert::isString($connectionName);

    $connectionInfo = Config::getValue('databases', $connectionName);

    if ($connectionInfo == NULL) {
      throw new Database_Exception('Database connection information could not be found');
    }

    self::_normaliseConnectionInfo($connectionInfo);

    Database::connect($connectionName, $connectionInfo['dsn'], $connectionInfo['username'], $connectionInfo['password'], $connectionInfo['options']);

    self::$_isConnected = TRUE;
  }

  /**
   * Make sure we have values set to make it easier to create an instance of PDO
   * @param $connectionInfo An array containing the values for the connection
   */
  static protected function _normaliseConnectionInfo (&$connectionInfo) {
    Assert::isArray($connectionInfo);

    if (isset($connectionInfo['dsn']) == FALSE || empty($connectionInfo['dsn']) == TRUE) {
      throw new Database_Exception('DSN must be set');
    }

    if (isset($connectionInfo['username']) == FALSE) {
      $connectionInfo['username'] = NULL;
    }

    if (isset($connectionInfo['password']) == FALSE) {
      $connectionInfo['password'] = NULL;
    }

    if (isset($connectionInfo['options']) == FALSE) {
      $connectionInfo['options'] = array();
    }
  }

  /**
   * Gets a prepared statement for the database specified
   * @param $statement
   * @param string $library
   * @param string $connectionName
   * @return PDOStatement Returns a PDOStatement object
   */
  static public function call ($statement, $library = 'Cmf_Database_Prepared_Statement_Library', $connectionName = 'default') {
    Assert::isString($statement);
    Assert::isString($library);
    Assert::isString($connectionName);

    $driver = ucfirst(strtolower(Database::getConnectionDriver($connectionName)));
    $statement = strtoupper($statement);

    if (defined("{$library}_{$driver}::$statement") == FALSE) {
      throw new RuntimeException("The prepared statement '{$statement}' in the '{$library}' library does not exist for the '" . lcfirst($driver) . "' driver used by the '{$connectionName}' connection");
    }

    $sql = constant("{$library}_{$driver}::$statement");

    return Database::prepare($sql, $connectionName);
  }
}

?>