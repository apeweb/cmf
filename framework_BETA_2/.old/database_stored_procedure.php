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

define('Database_Stored_Procedure', 'Database_Stored_Procedure');
class Database_Stored_Procedure {
  private $_name = '';
  private $_options = array();
  private $_params = array();
  private $_db = NULL;
  private $_driver = '';
  private $_sql = '';

  public function __construct ($name, $options = array()) {
    $this->_name = $name;
    $this->_options = $options;
  }

  /**
   * We auto-detect the data type of the value so there is no need to pass it
   */
  public function bindParameter ($name, $value) {
    $this->_params[$name] = $value;
  }

  /**
   * Execute the stored procedure, all parameters passed should match the PDO::query
   * method with the exception of the first parameter which should be omitted like so:
   * $this->execute(PDO::FETCH_INTO, new Foo);
   */
  public function query () {  
    $this->_getConnection();
    $this->_buildQuery();
    
    $args = func_get_args();
    array_unshift($args, $this->_sql);

    // xxx should be Database::log which then does Debug::log
    Debug::log('Last stored procedure executed', $this->_sql);

    return call_user_func_array(array($this->_db, 'query'), $args);
  }

  public function execute () {  
    $this->_getConnection();
    $this->_buildQuery();

    // xxx should be Database::log which then does Debug::log
    Debug::log('Last stored procedure executed', $this->_sql);

    return $this->_db->exec($this->_sql);
  }

  /**
   * Get the database connection that was intended to run the query
   */
  private function _getConnection () {
    if (isset($this->_options['database'])) {
      $this->_db = Database::getConnection($this->_options['database']);
      $this->_driver = Database::getConnectionDriver($this->_options['database']);
    }
    else {
      $this->_db = Database::getActiveConnection();
      $this->_driver = Database::getActiveConnectionDriver();
    }
  }

  /**
   * This is an automatic query builder, which puts together the query to call the stored
   * procedure
   */
  private function _buildQuery () {  
    $params = '';

    // add support for more database drivers
    switch ($this->_driver) {
      case 'mysql':
        /** I don't like forcing parameters on stored procedures but MySQL and PDO don't work nicely when you don't do so **/
        $sql = 'CALL `%s` (%s)';
        $param = '@%s := %s, ';
      break;

      // xxx need to test
      case 'mssql':
      default:
        $sql = 'EXECUTE [%s] %s';
        $param = '@%s = %s, ';

      case 'sqlite':
      case 'sqlite2':
        throw new Invalid_Operation_Exception('SQLite does not support stored procedures');
    }

    foreach ($this->_params as $name => $value) {
      if ($value === NULL || $value == 'NULL') {
        $params .= sprintf($param, $name, 'NULL');
      }
      if (is_numeric($value)) {
        $params .= sprintf($param, $name, $this->_db->quote($value, PDO::PARAM_INT));
      }
      else {
        $params .= sprintf($param, $name, $this->_db->quote($value));
      }
    }

    $params = rtrim($params, ', ');

    $this->_sql = sprintf($sql, $this->_getCommand(), $params);
  }

  /**
   * Escapes the command and returns it
   */
  private function _getCommand () {
    return substr($this->_db->quote($this->_name), 1, strlen($this->_name));
  }
}

?>