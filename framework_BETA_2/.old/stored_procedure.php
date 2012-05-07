<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

define('Stored_Procedure', NULL);
class Stored_Procedure extends PDOStatement {
  public function bindValue ($parameter,  $value, $dataType = PDO::PARAM_STR) {
    // Fixes the annoying ":foo" and ":bar" usage, allowing us to just use "foo"
    // and "bar"
    $parameter = ':'. $parameter;

    if ($value === NULL) {
      $value = 'NULL';
    }

    return parent::bindValue($parameter,  $value, $dataType);
  }

  public function execute ($inputParameters = array()) {
    $inputParameters = array_values($inputParameters);
    return parent::execute($inputParameters);
  }
}

?>