<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

// xxx see also http://msdn.microsoft.com/en-us/library/ms379625%28v=vs.80%29.aspx

/**
 * Evaluates an expression and, when the result is FALSE, prints a diagnostic message and
 * terminates execution
 */
class Assert {
  protected static $_enabled = FALSE;

  /**
   * Turn assertion on or off
   * @link http://intranet/docs/framework/v3/Assert/enabled/
   * @param $value assert enabled if TRUE and disabled if FALSE
   */
  public static function enabled ($value) {
    if ($value == TRUE) {
      assert_options(ASSERT_ACTIVE, TRUE);
      assert_options(ASSERT_WARNING, FALSE);
      assert_options(ASSERT_BAIL, TRUE);
      assert_options(ASSERT_QUIET_EVAL, TRUE);
      assert_options(ASSERT_CALLBACK, array('Assert', 'callback'));
      Assert::$_enabled = TRUE;
    }
    else {
      assert_options(ASSERT_ACTIVE, FALSE);
      Assert::$_enabled = FALSE;
    }
  }

  // xxx doesn't provide info on what failed :(
  public static function callback () {
    echo "<hr><strong>Assertion Failed</strong><br /><pre>Backtrace:<br />";
    echo htmlentities(preg_replace('#^Array\n\(\n|\n\)$#', '', print_r(array_slice(debug_backtrace(), 2), TRUE)));
  }

  /**
   * Assert whether the variable is of a string data type
   * @link http://intranet/docs/framework/v3/Assert/isString/
   * @param $variable the variable to assert
   * @param bool $allowNull whether the absence of value (NULL) is allowed
   */
  public static function isString ($variable, $allowNull = FALSE) {
    if (Assert::$_enabled == TRUE) {
      if ($allowNull == FALSE) {
        assert('is_string($variable)');
      }
      else {
        assert('is_string($variable) || is_null($variable)');
      }
    }
  }

  /**
   * Assert whether the variable is of a boolean data type
   * @link http://intranet/docs/framework/v3/Assert/isBoolean/
   * @param $variable the variable to assert
   * @param bool $allowNull whether the absence of value (NULL) is allowed
   */
  public static function isBoolean ($variable, $allowNull = FALSE) {
    if (Assert::$_enabled == TRUE) {
      if ($allowNull == FALSE) {
        assert('is_bool($variable)');
      }
      else {
        assert('is_bool($variable) || is_null($variable)');
      }
    }
  }

  /**
   * Assert whether the variable is of an integer data type
   * @link http://intranet/docs/framework/v3/Assert/isInteger/
   * @param $variable the variable to assert
   * @param bool $allowNull whether the absence of value (NULL) is allowed
   */
  public static function isInteger ($variable, $allowNull = FALSE) {
    if (Assert::$_enabled == TRUE) {
      if ($allowNull == FALSE) {
        assert('is_int($variable)');
      }
      else {
        assert('is_int($variable) || is_null($variable)');
      }
    }
  }

  /**
   * Assert whether the variable is of a float/double data type
   * @link http://intranet/docs/framework/v3/Assert/isFloat/
   * @param $variable the variable to assert
   * @param bool $allowNull whether the absence of value (NULL) is allowed
   */
  public static function isFloat ($variable, $allowNull = FALSE) {
    if (Assert::$_enabled == TRUE) {
      if ($allowNull == FALSE) {
        assert('is_float($variable)');
      }
      else {
        assert('is_float($variable) || is_null($variable)');
      }
    }
  }

  /**
   * Assert whether the variable is an object
   * @link http://intranet/docs/framework/v3/Assert/isObject/
   * @param $variable the variable to assert
   * @param bool $allowNull whether the absence of value (NULL) is allowed
   */
  public static function isObject ($variable, $allowNull = FALSE) {
    if (Assert::$_enabled == TRUE) {
      if ($allowNull == FALSE) {
        assert('is_object($variable)');
      }
      else {
        assert('is_object($variable) || is_null($variable)');
      }
    }
  }

  /**
   * Assert whether the variable is an array
   * @link http://intranet/docs/framework/v3/Assert/isArray/
   * @param $variable the variable to assert
   * @param bool $allowNull whether the absence of value (NULL) is allowed
   */
  public static function isArray ($variable, $allowNull = FALSE) {
    if (Assert::$_enabled == TRUE) {
      if ($allowNull == FALSE) {
        assert('is_array($variable)');
      }
      else {
        assert('is_array($variable) || is_null($variable)');
      }
    }
  }

  /**
   * Assert whether the variable is NULL
   * @link http://intranet/docs/framework/v3/Assert/isNull/
   * @param $variable the variable to assert
   */
  public static function isNull ($variable) {
    if (Assert::$_enabled == TRUE) {
      assert('$variable === NULL');
    }
  }

  /**
   * Assert whether the variable is empty
   * @link http://intranet/docs/framework/v3/Assert/isEmpty/
   * @param $variable the variable to assert
   */
  public static function isEmpty ($variable) {
    if (Assert::$_enabled == TRUE) {
      assert('empty($variable)');
    }
  }
}

?>