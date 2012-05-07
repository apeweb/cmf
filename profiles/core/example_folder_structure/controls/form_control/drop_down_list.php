<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

require_once(ROOT . 'framework/control/form_control/abstract.php');
require_once(ROOT . 'framework/network.php');
require_once(ROOT . 'framework/data/table.php');

define('Drop_Down_List', NULL);

class Drop_Down_List extends Form_Control {
  #Region "protected variables"
  protected $_name = '';
  protected $_disabled = FALSE;
  protected $_listSize = -1;
  protected $_multipleChoice = FALSE;

  // for using data tables to fill drop down list
  protected $_dataSource = NULL; // xxx add dataset option to allow grouping wuth $_group = TRUE;
  protected $_groupDataTables = FALSE; // xxx preparing for the future, each data table would become its own group
  protected $_dataTextField = '';
  protected $_dataValueField = '';

  // default and selected option
  protected $_selectedIndex = -1;
  protected $_selectedValue = NULL;
  protected $_selectedText = NULL;

  protected $_items = array();

  /**
   * Setting this to TRUE will prevent posted back data to be used as the
   * default value when re-rendered
   */
  protected $_defaultRenderedValue = FALSE;
  protected $_defaultSelectedIndex = -1;
  protected $_defaultSelectedValue = NULL;
  protected $_defaultSelectedText = NULL;
  #End Region

  #Region "private variables"
  private $_request = Request;
  #End Region

  #Region "magic methods"
  public function __construct () {
    $this->_request = new Request;
  }

  public function __get ($variableName) {
    switch ($variableName) {
      // basic attributes
      case 'cssClass':
      case 'id':
      case 'style':
      case 'toolTip':

      // i18n attributes
      case 'lang':
      case 'direction':

      // framework attributes
      case 'enabled':
      case 'defaultRenderedValue':

      // html attributes
      case 'name':
      case 'accessKey':
      case 'listSize':
      case 'multipleChoice':
      case 'tabIndex':
      case 'disabled':

      // data binding
      case 'dataSource':
      case 'dataTextField':
      case 'dataValueField':
        return $this->{'_' . $variableName};

      // default/selected value
      case 'selectedIndex':
        $this->_collectPostBackData();
        return $this->_selectedIndex();

      case 'selectedValue':
        $this->_collectPostBackData();
        return $this->_selectedValue();

      case 'selectedText':
        $this->_collectPostBackData();
        return $this->_selectedText();

      default:
        // legacy error reporting
        trigger_error("Undefined variable: '" . $variableName . "'", E_USER_NOTICE);
    }
  }

  // just the same as get but allows for method to be used too
  public function __call($methodName, $arguments) {
    switch ($methodName) {
      // basic attributes
      case 'cssClass':
      case 'id':
      case 'style':
      case 'toolTip':

      // i18n attributes
      case 'lang':
      case 'direction':

      // framework attributes
      case 'enabled':
      case 'defaultRenderedValue':

      // html attributes
      case 'name':
      case 'accessKey':
      case 'listSize':
      case 'multipleChoice':
      case 'tabIndex':
      case 'disabled':

      // data binding
      case 'dataSource':
      case 'dataTextField':
      case 'dataValueField':
        if (count($arguments) > 0) {
          throw new Exception ("Call must assign a value to '" . $methodName . "' or use its value.");
        }

        return $this->{'_' . $methodName};

      // default/selected value
      case 'selectedIndex':
        if (count($arguments) > 0) {
          throw new Exception ("Call must assign a value to '" . $methodName . "' or use its value.");
        }
        return $this->_selectedIndex();

      case 'selectedValue':
        if (count($arguments) > 0) {
          throw new Exception ("Call must assign a value to '" . $methodName . "' or use its value.");
        }
        return $this->_selectedValue();

      case 'selectedText':
        if (count($arguments) > 0) {
          throw new Exception ("Call must assign a value to '" . $methodName . "' or use its value.");
        }
        return $this->_selectedText();

      default:
        // legacy error reporting
        trigger_error("Call to undefined function '" . $methodName . "()'", E_USER_ERROR);
    }
  }

  // set stuff
  public function __set ($variableName, $value) {
    switch ($variableName) {
      // basic attributes
      case 'cssClass':
      case 'id':
      case 'style':
      case 'toolTip':

      // i18n attributes
      case 'lang':
      case 'direction':

      // html attributes
      case 'name':
      case 'accessKey':

      case 'dataTextField':
      case 'dataValueField':
        if (is_object($value) == FALSE && is_array($value) == FALSE) {
          $this->{'_' . $variableName} = $value;
          return;
        }
        else {
          throw new Exception ("Conversion from '" . gettype($value) . "' to type 'string' in '" . $variableName . "' is not valid.");
        }

      case 'listSize':
      case 'tabIndex':
        if (is_numeric($value) == TRUE && is_string($value) == FALSE) {
          $this->{'_' . $variableName} = $value;
          return;
        }
        else {
          throw new Exception ("Conversion from '" . gettype($value) . "' to type 'integer' in '" . $variableName . "' is not valid.");
        }

      // framework attributes
      case 'enabled':
      case 'multipleChoice':
      case 'defaultRenderedValue':
      case 'disabled':
        if (is_bool($value) == TRUE || (is_numeric($value) == TRUE && is_string($value) == FALSE)) {
          $this->{'_' . $variableName} = $value;
          return;
        }
        else {
          throw new Exception ("Conversion from '" . gettype($value) . "' to type 'boolean' in '" . $variableName . "' is not valid.");
        }

      case 'dataSource':
        $this->_checkDataSource($value);
        return;

      case 'selectedIndex':
        $this->_selectedIndex($value);
        return;

      case 'selectedValue':
        $this->_selectedValue($value);
        return;

      case 'selectedText':
        $this->_selectedText($value);
        return;

      default:
        throw new Exception ("'" . $variableName . "' is not a member of '" . __CLASS__ . "'");
    }
  }

  public function __toString () {
    $this->_build();
    return $this->_control;
  }
  #End Region

  #Region "public methods"
  // takes data table, and uses columns datatextfield and datavaluefield to create an array of names and values ($array['name'] = $value;)
  public function dataBind () {
    $i = 0;

    if (trim($this->_dataValueField) == '') {
      $this->_dataValueField = $this->_dataTextField;
    }

    if ($this->_dataSource->tables->count() > 1) {
      // xxx not supported yet
      throw new Exception ('Framework does not support multiple table binds on Drop_Down_List.');
    }
    elseif ($this->_dataSource->tables->count() > 0) {
      if ($this->_dataSource->table(0)->rows->count() > 0) {
        foreach ($this->_dataSource->table(0)->rows as $row) {
          try {
            $this->_items[$i]['value'] = htmlspecialchars($row->{$this->_dataValueField});
            $this->_items[$i]['text'] = htmlspecialchars($row->{$this->_dataTextField});
            ++$i;
          }
          catch (Exception $ex) {
            throw new Exception ('Data could not be bound. ' . $ex->getMessage());
          }
        }
      }
      else {
        throw new Exception ('Data could not be bound because there are no data rows in the data source.');
      }
    }
    else {
      throw new Exception ('Data could not be bound because there are no data tables in the data source.');
    }
  }

  public function insertItem ($index, $text, $value = NULL) {
    $itemData = array();

    if (is_numeric($index) == FALSE || is_string($index) == TRUE) {
      throw new Exception ("Conversion from '" . gettype($index) . "' to type 'integer' is not valid.");
    }

    if (is_object($text) == TRUE || is_array($text) == TRUE) {
      throw new Exception ("Conversion from '" . gettype($text) . "' to type 'string' is not valid.");
    }

    if (is_object($value) == TRUE || is_array($value) == TRUE) {
      throw new Exception ("Conversion from '" . gettype($value) . "' to type 'string' is not valid.");
    }

    $itemData['text'] = $text;

    if ($value !== NULL) {
      $itemData['value'] = $value;
    }

    if ($index > count($this->_items)) {
      $index = count($this->_items);
    }

    array_insert($this->_items, $itemData, $index);
  }
  #End Region

  #Region "protected methods"
  /**
   * Gets or sets the selected text
   * @param string $text
   * @return mixed
   */
  protected function _selectedText ($text = NULL) {
    // get
    if ($text === NULL) {
      // if the select is <option value="x">selectedText</option>
      if (trim($this->_selectedText) != '') {
        return $this->_selectedText;
      }

      // if the select is just <option>selectedValue</option>
      return $this->_selectedValue;
    }
    // set
    else {
      if (is_object($text) == FALSE && is_array($text) == FALSE) {
        $this->_selectedText = $this->_defaultSelectedText = $text;
      }
      else {
        throw new Exception ("Conversion from '" . gettype($text) . "' to type 'string' is not valid.");
      }
    }
  }

  /**
   * Gets or sets the selected value
   * @param string $value
   * @return mixed
   */
  protected function _selectedValue ($value = NULL) {
    // get
    if ($value === NULL) {
      return $this->_selectedValue;
    }
    // set
    else {
      if (is_object($value) == FALSE && is_array($value) == FALSE) {
        $this->_selectedValue = $this->_defaultSelectedValue = $value;
      }
      else {
        throw new Exception ("Conversion from '" . gettype($value) . "' to type 'string' is not valid.");
      }
    }
  }

  /**
   * Gets or sets the selected index
   * @param integer $index
   * @return mixed
   */
  protected function _selectedIndex ($index = NULL) {
    // get
    if ($index === NULL) {
      return $this->_selectedIndex;
    }
    // set
    else {
      if (is_numeric($index) == TRUE && is_string($index) == FALSE) {
        $this->_selectedIndex = $this->_defaultSelectedIndex = $index;
      }
      else {
        throw new Exception ("Conversion from '" . gettype($index) . "' to type 'integer' is not valid.");
      }
    }
  }

  protected function _build () {
    if ($this->_enabled == TRUE) {
      $this->_control = '<select';

      // core attributes
      if (trim($this->_cssClass) != '') {
        $this->_control .= ' class="' . $this->_cssClass . '"';
      }

      if (trim($this->_id) != '') {
        $this->_control .= ' id="' . $this->_id . '"';
      }

      if (trim($this->_style) != '') {
        $this->_control .= ' style="' . $this->_style . '"';
      }

      if (trim($this->_toolTip) != '') {
        $this->_control .= ' title="' . $this->_toolTip . '"';
      }

      // i18n attributes
      if (trim($this->_lang) != '') {
        $this->_control .= ' lang="' . $this->_lang . '" xml:lang="' . $this->_lang . '"';
      }

      if (trim($this->_direction) != '') {
        $this->_control .= ' dir="' . $this->_direction . '"';
      }

      // all others
      if (trim