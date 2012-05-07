<?php

/**
 * NOTE: If you work on this file please provide comments in the following
 *       section!
 *
 * NOTE: In the version please include your author identifiable tag!
 *
 * NOTE: For readability, comments should not be longer than 76 characters
 *       long on any line unless the comment includes a web page address or
 *       something similar that should not be split onto multiple lines.
 *
 * Data_Table_Collection
 * Version 1.0.0.1
 * Last edited by: Matthew Bonner
 *
 * Copyright (c) 2009 Ape Web Ltd & 2010-2011 Ape Web LLP
 *
 */

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

#Region "Required files"
require_once(FRAMEWORK_PATH . 'data/table' . EXT);
#End Region

define('Data_Table_Collection', 'Data_Table_Collection');

//class Data_Table_Collection extends ArrayObject implements Iterator {
class Data_Table_Collection extends ArrayObject {
  public function __get($key) {
    return $this[$key];
  }

  public function table ($key) {
    if (array_key_exists($key, $this)) {
      return $this[$key];
    }
    else {
      throw new OutOfRangeException ('Cannot find table ' . $key);
    }
  }

  public function rewind () {
    reset($this);
  }

  public function current () {
    return current($this);
  }

  public function key () {
    return key($this);
  }

  public function next () {
    return next($this);
  }

  public function valid () {
    return $this->current() !== FALSE;
  }

  public function removeAt ($key) {
    if (isset($this[$key])) {
      unset($this[$key]);
    }
    else {
      throw new OutOfRangeException ('Cannot find table ' . $key);
    }
  }

  public function add (Data_Table $dataTable) {
    if (trim($dataTable->name) != '' && array_key_exists($dataTable->name, $this) == FALSE) {
      $this[$dataTable->name] = $dataTable;
    }
    elseif (array_key_exists($this->count(), $this) == FALSE) {
      $this[$this->count()] = $dataTable;
    }
    else {
      throw new Exception ('Both tables ([' && $this->count() && '] and ' && $dataTable->name && ' already exist.');
    }
  }

  public function overwrite (Data_Table $dataTable) {
    if (empty($dataTable->name) == FALSE) {
      $this[$dataTable->name] = $dataTable;
    }
    else {
      $this[$this->count()] = $dataTable;
    }
  }
}

?>