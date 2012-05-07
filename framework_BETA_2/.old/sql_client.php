<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

// xxx needs updating to allow framework to run
require_once(SYSTEM_PATH . 'data/stored_procedure.php');
require_once(SYSTEM_PATH . 'data/sql_client/exception.php');
require_once(SYSTEM_PATH . 'data/sql_client/error.php');
require_once(STORED_PROCEDURE_LIBRARY_PATH);

echo 'bp' . __LINE__ . ' ' . __FILE__;
exit;

define('Sql_Client', NULL);
class Sql_Client {
  static private $_instances = array();

  private $_connectionId = '';
  private $_driver = '';

  /**
  * Connects to the database specified based on the configuration file specified
  * @return void
  */
  public function __construct ($file = '') {
    $settings = array();

    if ($file == FALSE && defined('SQL_CLIENT_DEFAULT_CONFIG_FILE') == TRUE) {
      $file = SQL_CLIENT_DEFAULT_CONFIG_FILE;
    }

    $this->_connectionId = md5($file);

    // if the instance doesn't already exist, based on the config file used then
    // a new instance needs to be created
    if (isset(self::$_instances[$this->_connectionId]) == FALSE || self::$_instances[$this->_connectionId] == FALSE) {
      if (file_exists($file) == FALSE) {
        throw new File_Not_Found_Exception;
      }

      $settings = parse_ini_file($file);

      if ($settings == FALSE) {
        throw new Exception("Invalid database configuration file '" . $file . "'.");
      }

      if (isset($settings['driver']) == FALSE || isset($settings['host']) == FALSE || isset($settings['schema']) == FALSE) {
        throw new Exception("Missing information in database configuration file '" . $file . "'.");
      }

      if (isset($settings['options']) == FALSE) {
        $settings['options'] = NULL;
      }

      // This is important to know for later when returning insert ID's
      $this->_driver = $settings['driver'];

      // Build up the DNS for connecting to the database
      $dns = $settings['driver'];
      $dns .= ':host=' . $settings['host'];
      if (empty($settings['port']) == FALSE) {
        $dns .= ';port=' . $settings['port'];
      }
      $dns .= ';dbname=' . $settings['schema'];

      // Create a new instance
      try {
        self::$_instances[$this->_connectionId] = new PDO($dns, $settings['username'], $settings['password'], $settings['options']);

        // Allows mutiple queries to be ran at the same time
        if ($settings['driver'] == 'mysql') {
          self::$_instances[$this->_connectionId]->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, TRUE);
        }
      }
      catch (PDOException $e) {
        // xxx log
      }

      // Set the error mode
      if (SYSTEM_IN_SANDBOX == TRUE) {
        self::$_instances[$this->_connectionId]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      }

      // Just for precautions, not that it can be used anyway
      unset($settings['password']);
    }
  }

  final public function escape ($string) {
    return self::$_instances[$this->_connectionId]->quote($string);
  }

  /**
   * Pings the database to ensure the connection is still alive, if the
   * connection has dropped and $reconnect is set to TRUE then the connection
   * will attempt to reconnect itself
   */
  public function ping ($reconnect = TRUE) {

  }

  /**
   * Loads a stored procedure into the buffered SQL
   * @param string stored procedure name
   * @param string stored procedure library to use
   */
  public function prepare ($name, $library = 'Standard_Stored_Procedure_Library') {
    if (trim($name) == '') {
      throw new Sql_Exception("The stored procedure name is invalid");
    }
    /**
     * If the stored procedure exists, passes the SQL for the stored procedure
     * into a new instance of a Stored_Procedure and also passes the driver in
     * use to the Stored_Procedure for additional processing later
     */
    elseif (defined($library . '::' . $name)) {
      return self::$_instances[$this->_connectionId]->prepare(
        constant($library . '::' . $name),
        array(PDO::ATTR_STATEMENT_CLASS => array('Stored_Procedure'))
      );
    }
    else {
      throw new Sql_Exception("Stored procedure '" . $name . "' does not exist");
    }
  }

  public function beginTransaction () {
    self::$_instances[$this->_connectionId]->beginTransaction();
  }

  public function rollBack () {
    self::$_instances[$this->_connectionId]->rollBack();
  }

  public function commit () {
    self::$_instances[$this->_connectionId]->commit();
  }

  public function close () {
    self::$_instances[$this->_connectionId] = null;
  }
}

?>