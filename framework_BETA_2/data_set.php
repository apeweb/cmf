<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

/**
 * Represents an in-memory cache of data
 */

class Data_Set {
  /**
   * Holds the Data_Set name for reference.
   * @var (string) Data_Set name
   */
  public $name = '';

  /**
   * Holds the Data_Set name for reference.
   * @var (object) Data_Table_Collection
   */
  private $_dataTableCollection;

  /**
   * Whether the Data_Set is initialised or not.
   * @var (boolean) initialised
   */
  private $_initialised = FALSE;

  /**
   * Sets up the initial Data_Set.
   *
   * @return (void)
   */
  public function __construct ($name = '') {
    $this->name = $name;
    $this->_dataTableCollection = new Data_Table_Collection;
  }

  /**
   * Provides a reference to the Data_Table_Collection object.
   *
   * @return (object) Mixed (Data_Table_Collection, (void))
   */
  public function __get ($variableName) {
    switch ($variableName) {
      case 'tables':
      case 'table':
        return $this->_dataTableCollection;

      default:
        // legacy error reporting
        trigger_error('Undefined variable: ' . $variableName . '()', E_USER_NOTICE);
    }
  }

  /**
   * Passes by reference arguments to methods in the Data_Table_Collection.
   *
   * @return (object) Mixed (Data_Table, Data_Table_Collection, (void))
   */
  public function __call ($methodName, $arguments) {
    switch ($methodName) {
      case 'tables':
      case 'table':
        if (isset($arguments[0])) {
          if (is_numeric($arguments[0]) && $arguments[0] < 0) {
            throw new OutOfRangeException ('Index was outside the bounds of the array');
          }
          else {
            try {
              return $this->_dataTableCollection->table($arguments[0]);
            }
            // catch and re-throw the OutOfRangeException here
            catch (OutOfRangeException $ex) {
              throw new OutOfRangeException ('Cannot find table ' . $arguments[0]);
            }
          }
        }
        else {
          // legacy error reporting
          trigger_error('Wrong parameter count for ' . $methodName . '()', E_USER_WARNING);
        }
        break;

      default:
        // legacy error reporting
        trigger_error('Call to undefined method Data_Set::' . $methodName . '()', E_USER_ERROR);
    }
  }

  /**
   * Clears the Data_Set of any data by removing all rows and tables.
   *
   * @return (void)
   */
  public function clear () {
    $this->_dataTableCollection = new Data_Table_Collection;
  }

  /**
   * Releases all resources used up by the Data_Set.
   *
   * @return (void)
   */
  public function dispose () {
    for ($i = 0; $i < $this->_dataTableCollection->count(); ++$i) {
      $this->_dataTableCollection->removeAt($i);
    }
  }

  /**
   * Returns a value that indicates whether the Data_Set is initialised or
   * not.
   *
   * @return (boolean) initialised
   */
  public function initialised () {
    return $this->_initialised;
  }

  /**
   * Fills a Data_Set with values from a data source.
   *
   * @remarks xxx Will eventually support different types, such as XML etc.
   * using some sort of data reader such as an SQL data adapter.
   *
   * @return (void)
   */
  public function load ($dataSource, $overwrite = FALSE) {
    $validReader = FALSE;

    $this->_initialised = TRUE;

    if ($overwrite == TRUE) {
      $this->clear();
    }

    // work out which reader to use
    switch (TRUE) {
      case $dataSource instanceof mysqli:
        $reader = new Mysql_Data_Reader;
        $validReader = TRUE;
        break;

      default:
        //var_dump($dataSource);
        throw new RuntimeException ('Unsupported data reader');
    }

    if ($validReader == TRUE) {
      do {
        $this->_dataTableCollection->add($reader->read());
      }
      while ($reader->nextResult());
    }
  }

  /**
   * Reads XML data into the Data_Set using an XML reader. This is
   * specialised to reading string data into a Data_Set.
   *
   * @remarks xxx Might use XML-RPC xmlrpc_decode(). See data_set.xml file
   * for an example of how to store the data by default.
   *
   * @return (void)
   */
  public function readXml ($dataSource, $overwrite = FALSE) {
    $errorEncountered = FALSE;

    // xxx do something

    if ($errorEncountered == FALSE) {
      $this->_initialised = TRUE;
    }
  }
}

// --------------------------------------------------------- //
// EXAMPLE OF USAGE                                          //
// --------------------------------------------------------- //

//$db = Mysql_Adapter::instance();
//$ds = Data_Set;

//$result = $db->execute('SELECT * FROM `test`');
//$ds->load($result);

//try {
  // adds a new data table
  //$ds->tables->add(new Data_Table);
  // should catch but doesnt?
  //$ds->tables->add('string');
  //echo $ds->tables->count();
//}
//catch (Exception $ex) {
//  echo $ex->getMessage();
//}

//$adapter = new Mysql_Data_Adapter;
//$ds->load($adapter);

// both will return an empty Data_Table
//var_dump($ds->table(0));
//var_dump($ds->table[0]);

// when the Data_Table does not exist...
// throws an exception if not found
//var_dump($ds->table(1));

// returns NULL if not found
//var_dump($ds->table[1]);

//$ds->dispose();

//echo $ds->tables->count();

//foreach ($ds->tables as $table) {
//  print_r($table);
//}

//echo $ds->tables->count();

// how you should correctly check if rows are available
//if ($ds->tables->count() < 1) {
//  echo 'no tables exist';
//}
//elseif ($ds->table[2] == NULL) {
//  echo 'table is not set';
//}
//elseif ($ds->table[2]->rows->count() < 1) {
//  echo 'table has no rows';
//}
//else {
//  echo 'table has rows';
//}

?>