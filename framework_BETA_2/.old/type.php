<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

require_once(ROOT . 'framework/system/enum.php');

define('Data_Type', NULL);

/**
 * Deals with scalar variables only
 */
final class Data_Type extends Enum {
  const Integer = 'integer';
  const Int = 'integer';
  const Long = 'integer';

  const Boolean = 'boolean';
  const Bool = 'boolean';

  const String = 'string';
  const Str = 'string';

  const Float = 'float';
  const Double = 'float';
  const Real = 'float';

  #Region "integer"
  static public function isInteger ($variable) {
    if (is_numeric($variable) == TRUE && is_string($variable) == FALSE) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  static function isInt ($variable) {
    return self::isInteger($variable);
  }

  static function isLong ($variable) {
    return self::isInteger($variable);
  }
  #End Region

  #Region "boolean"
  static public function isBoolean ($variable) {
    if (is_bool($variable) == TRUE || (is_numeric($variable) == TRUE && is_string($variable) == FALSE)) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  static function isBool ($variable) {
    return self::isBoolean($variable);
  }
  #End Region

  #Region "string"
  static public function isString ($variable) {
    if (is_object($variable) == FALSE && is_array($variable) == FALSE) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  static function isStr ($variable) {
    return self::isString($variable);
  }
  #End Region

  #Region "float"
  static public function isFloat ($variable) {
    throw new Exception('unsupported feature');
  }

  static function isDouble ($variable) {
    return self::isFloat($variable);
  }

  static function isReal ($variable) {
    return self::isFloat($variable);
  }
  #End Region
}

?>