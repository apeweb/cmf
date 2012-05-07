<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

#Region "Required files"
require_once(FRAMEWORK_PATH . 'data/row_collection' . EXT);
#End Region

define('Data_Table', 'Data_Table');

class Data_Table {
  /**
   * Holds the Data_Set name for reference.
   * @var (string) Data_Set name
   */
  public $name = '';

  private $_dataRowCollection;

  public function __construct ($name = '') {
    $this->name = $name;
    $this->_dataRowCollection = new Data_Row_Collection;
  }

  public function __get ($variableName) {
    switch ($variableName) {
      case 'rows':
      case 'row':
        return $this->_dataRowCollection;

      default:
        // legacy error reporting
        trigger_error('Undefined variable: ' . $variableName . '()', E_USER_NOTICE);
    }
  }

  public function __call ($methodName, $arguments) {
    switch ($methodName) {
      case 'rows':
      case 'row':
        if (isset($arguments[0])) {
          if (!is_numeric($arguments[0]) || $arguments[0] < 0) {
            throw new OutOfRangeException ('Index was outside the bounds of the array');
          }
          else {
            try {
              return $this->_dataRowCollection->row($arguments[0]);
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
        trigger_error('Call to undefined method ' . __CLASS__ . '::' . $methodName . '()', E_USER_ERROR);
    }
  }

  public function name ($name) {
    $this->name = $name;
  }

  public function load (Data_Row_Collection $dataRowCollection, $overwrite = FALSE) {
    if ($overwrite == TRUE) {
      $this->_dataRowCollection = $dataRowCollection;
    }
    else {
      foreach ($dataRowCollection as $dataRow) {
        $this->_dataRowCollection->add($dataRow);
      }
    }
  }
}

?>