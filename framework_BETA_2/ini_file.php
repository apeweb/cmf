<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Ini_File {
  // For static functions

  // For member functions

  // Static methods
  public static function open ($path) {
    $handle = new Ini_File();
    // array parse_ini_file ( string $filename [, bool $process_sections = false [, int $scanner_mode = INI_SCANNER_NORMAL ]] )

  }


  // Member methods
  protected function __construct () {}

  public function __get ($variableName) {
    // return vals
  }

  public function __set ($variableName, $value) {
    // xxx check if file is read-only, etc.
    // write
  }
}

?>