<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

/**
 * The Database class serves as a factory for database connections, the underlying
 * technology is PDO
 */
class Database {
  static protected $_activeConnection = '';
  static protected $_connections = array();

  /**
   * Connect to a database using PDO
   * @param $connectionName
   * @param $dsn
   * @param null $username
   * @param null $password
   * @param array $options
   * @return
   */
  static public function connect ($connectionName, $dsn, $username = NULL, $password = NULL, $options = array()) {
    Assert::isString($connectionName);

    self::$_connections[$connectionName]['connection'] = new PDO($dsn, $username, $password, $options);
    self::$_connections[$connectionName]['connection_options'] = $options;

    if (count(self::$_connections) == 1) {
      self::$_activeConnection = $connectionName;
    }

    return self::$_connections[$connectionName]['connection'];
  }
  
  static public function isConnected () {
    if (count(self::$_connections) > 0) {
      return TRUE;
    }
    
    return FALSE;
  }

  static public function disconnect ($connectionName) {
    Assert::isString($connectionName);
    unset(self::$_connections[$connectionName]);
  }

  /**
   * Get a database connection
   * @param $connectionName leave blank to get default connection
   */
  static public function getConnection ($connectionName = NULL) {
    Assert::isString($connectionName, TRUE);
  
    if ($connectionName === NULL) {
      $connectionName = self::$_activeConnection;
    }

    if (isset(self::$_connections[$connectionName])) {
      return self::$_connections[$connectionName]['connection'];
    }

    throw new Database_Exception('Database connection could not be found');
  }
  
  static public function getConnectionOptions ($connectionName = NULL) {
    Assert::isString($connectionName, TRUE);
  
    if ($connectionName === NULL) {
      $connectionName = self::$_activeConnection;
    }

    if (isset(self::$_connections[$connectionName])) {
      return self::$_connections[$connectionName]['connection_options'];
    }

    throw new Database_Exception('Database connection could not be found');
  }
  
  static public function getConnectionList () {
    return array_keys(self::$_connections);
  }

  static public function getConnectionDriver ($connectionName = NULL) {
    Assert::isString($connectionName, TRUE);
  
    if ($connectionName === NULL) {
      $connectionName = self::$_activeConnection;
    }

    if (isset(self::$_connections[$connectionName])) {
      return self::$_connections[$connectionName]['connection']->getAttribute(PDO::ATTR_DRIVER_NAME);
    }

    throw new Database_Exception('Database connection could not be found');
  }

  /**
   * Set the active database connection
   * @param $connectionName
   */
  static public function setActiveConnection ($connectionName) {
    Assert::isString($connectionName);
  
    if (isset(self::$_connections[$connectionName])) {
      self::$_activeConnection = $connectionName;
    }

    throw new Database_Exception('Database connection could not be found');
  }

  /**
   * Get the active database connection
   */
  static public function getActiveConnection () {
    if (isset(self::$_connections[self::$_activeConnection])) {
      return self::$_connections[self::$_activeConnection]['connection'];
    }

    throw new Database_Exception('Active database connection could not be found');
  }

  static public function getActiveConnectionOptions () {
    if (isset(self::$_connections[self::$_activeConnection])) {
      return self::$_connections[self::$_activeConnection]['connection_options'];
    }

    throw new Database_Exception('Active database connection could not be found');
  }

  static public function getActiveConnectionDriver () {
    if (isset(self::$_connections[self::$_activeConnection])) {
      return self::$_connections[self::$_activeConnection]['connection']->getAttribute(PDO::ATTR_DRIVER_NAME);
    }

    throw new Database_Exception('Active database connection could not be found');
  }

  /**
   * Prepare a query
   * @param $statement
   * @param null $connectionName
   * @return
   */
  static public function prepare ($statement, $connectionName = NULL) {
    Assert::isString($statement);
    Assert::isString($connectionName, TRUE);
  
    if ($connectionName === NULL) {
      $connectionName = self::$_activeConnection;
    }
    
    return self::$_connections[$connectionName]['connection']->prepare($statement);
  }
  
  // xxx add query and execute here too
}

?>