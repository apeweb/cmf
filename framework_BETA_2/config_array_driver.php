<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Config_Array_Driver extends Config_Driver {
  protected $_array = array();

  public function setValue ($key, $value) {
    if (isset($this->_options['read_only']) == TRUE && $this->_options['read_only'] == TRUE) {
      throw new Config_Write_Exception('Data source is set to read-only');
    }

    // xxx finish
    // xxx when the value is set, must update Config's cache
  }
  
  public function deleteValue ($key) {
    if (isset($this->_options['read_only']) == TRUE && $this->_options['read_only'] == TRUE) {
      throw new Config_Write_Exception('Data source is set to read-only');
    }

    // xxx finish
    // xxx when the value is deleted, must update Config's cache
  }

  public function getValue ($key) {
    $args = func_get_args();

    if (array_key_exists($args[0], $this->_array) == FALSE) {
      return NULL;
    }
    elseif (count($args) == 1) {
      return $this->_array[$args[0]];
    }
    else {
      $temp = $this->_array[$args[0]];
      unset($args[0]);
    }

    foreach ($args as $key) {
      if (array_key_exists($key, $temp) == FALSE) {
        return NULL;
      }
      else {
        $temp = $temp[$key];
      }
    }
    
    return $temp;
  }

  public function valueExists ($key) {
    $args = func_get_args();

    // If first branch does not exist, quick escape
    if (array_key_exists($args[0], $this->_array) == FALSE) {
      return FALSE;
    }
    // If first branch exists and there is only 1 branch then quick escape TRUE as we know it exists
    elseif (count($args) == 1) {
      return TRUE;
    }

    // See if we can find the branch...
    $temp = $this->_array[$args[0]];
    unset($args[0]);

    foreach ($args as $key) {
      if (array_key_exists($key, $temp) == FALSE) {
        return FALSE;
      }
      else {
        $temp = $temp[$key];
      }
    }
    
    return TRUE;
  }

  public function load ($options) {
    if (!isset($options['path'])) {
      throw new InvalidArgumentException('Path must be set');
    }

    $this->_array = include($options['path']);

    $this->_options = $options;
  }

  public function save () {
    // xxx support File_Access as $this->_options['file_access'];

    // xxx need to implement
  }
}

?>