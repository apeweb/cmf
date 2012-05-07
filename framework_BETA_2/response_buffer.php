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

// Note: flushing will automatically happen at the end of the script

class Response_Buffer {
  static private $_headers = array();
  static private $_headersSent = FALSE;
  static private $_httpStatusCode = 200;

  static private $_isBuffering = FALSE;
  static private $_isFlushing = FALSE;

  static public function start ($callback = NULL) {
    Assert::isString($callback, TRUE);

    if (Environment::isCommandLine() == FALSE) {
      ob_start($callback);
    }

    self::$_isBuffering = TRUE;

    Event_Dispatcher::notifyObservers(Response_Buffer_Event::start);

    if (Environment::isCommandLine() == FALSE) {
      register_shutdown_function('Response_Buffer::flush');
    }
  }

  static public function setStatusCode ($httpStatusCode) {
    Assert::isInteger($httpStatusCode);

    if (self::$_isBuffering == FALSE) {
      self::start();
    }

    self::$_httpStatusCode = $httpStatusCode;
  }

  static public function getStatusCode ($httpStatusCode) {
    return self::$_httpStatusCode;
  }

  /**
   * @static Sets a HTTP header field to be included as part of the response
   * @note You don't have to use this method but doing so prevents any errors from being printed if spaces or empty
   *       lines are output before header() is called.
   * @param string $fieldName the HTTP header field name
   * @param string $fieldValue the HTTP header field value
   * @param bool $replace replace all current fields with the same name
   * @return void
   */
  static public function setHeader ($fieldName, $fieldValue, $replace = FALSE) {
    Assert::isString($fieldName);
    Assert::isString($fieldValue);
    Assert::isBoolean($replace);

    // Field names are case-insensitive, but for maximum compatibility they should follow "common form" (see RFC 2617, section 4.2)
    $fieldName = strtolower($fieldName);

    if (self::$_isBuffering == FALSE) {
      self::start();
    }

    if ($replace == FALSE) {
      self::$_headers[$fieldName][] = $fieldValue;
    }
    else {
      unset(self::$_headers[$fieldName]);
      self::$_headers[$fieldName][] = $fieldValue;
    }
  }

  static public function deleteHeader ($fieldName, $fieldValueToDelete = NULL) {
    Assert::isString($fieldName);
    Assert::isString($fieldValueToDelete, TRUE);

    // Field names are case-insensitive, but for maximum compatibility they should follow "common form" (see RFC 2617, section 4.2)
    $fieldName = strtolower($fieldName);

    // Nothing to delete if we are not buffering
    if (self::$_isBuffering == FALSE) {
      return;
    }

    if (headers_sent($path, $line) == TRUE) {
      throw new RuntimeException("Headers already sent in '$path' on line '$line'");
    }

    // If the header wasn't set by the response buffer or the response buffer has already sent it's headers
    if (isset(self::$_headers[$fieldName]) == FALSE || self::$_headersSent == TRUE) {
      // This removes all headers with the $fieldName specified
      header_remove($fieldName);
      return;
    }

    if ($fieldValueToDelete === NULL) {
      unset(self::$_headers[$fieldName]);
      return;
    }
    
    foreach (self::$_headers[$fieldName] as $i => $fieldValue) {
      if ($fieldValue == $fieldValueToDelete) {
        unset(self::$_headers[$fieldName][$i]);
        return;
      }
    }

    throw new RuntimeException("Header field '$fieldName' value '$fieldValueToDelete' not set");
  }

  static public function getHeaders ($fieldName = NULL) {
    Assert::isString($fieldName, TRUE);

    if ($fieldName === NULL) {
      return self::$_headers;
    }

    $fieldName = strtolower($fieldName);

    if (isset(self::$_headers[$fieldName]) == FALSE) {
      throw new RuntimeException("No headers with the field name '{$fieldName}' could be found");
    }

    return self::$_headers[$fieldName];
  }

  /**
   * @static Send HTTP headers to the client
   * @note In the CMF, don't send the headers prematurely, doing so will cause an exception in the terminate phase
   * @throws RuntimeException
   * @return NULL Uses return for quick escaping
   */
  static public function sendHeaders () {
    if (self::$_headersSent == TRUE) {
      throw new RuntimeException("Headers already sent");
    }

    self::$_headersSent = TRUE;

    if (Environment::isCommandLine() == TRUE) {
      return;
    }

    if (headers_sent($path, $line) == TRUE) {
      throw new RuntimeException("Headers already sent in '$path' on line '$line'");
    }

    header(Request::protocol() . ' ' . self::$_httpStatusCode . ' ' . Http_Status_Code::getStatusLine(self::$_httpStatusCode), TRUE);

		foreach (self::$_headers as $fieldName => $fieldValues) {
			foreach ($fieldValues as $fieldValue) {
        $field = $fieldName . ': ' . $fieldValue;
        // If headers were set prior to the framework headers then we shouldn't be trying to override them
				header($field, FALSE);
			}
		}
  }

  static public function headersSent () {
    return (headers_sent() || self::$_headersSent);
  }

  static public function addContent ($content, $replace = FALSE) {
    Assert::isString($content);
    Assert::isBoolean($replace);

    if (self::$_isBuffering == FALSE) {
      self::start();
    }
    elseif ($replace == TRUE) {
      ob_clean();
    }

    echo $content;
  }

  static public function getContent () {
    if (self::$_isBuffering == FALSE) {
      return '';
    }

    return ob_get_contents();
  }

  static public function clearContent () {
    if (self::$_isBuffering == FALSE) {
      return FALSE;
    }

    return ob_clean();
  }

  static public function flush () {
    self::$_isFlushing = TRUE;

    while (ob_get_level()) {
      ob_end_flush();
    }

    flush();
  }

  static public function isBuffering () {
    return self::$_isBuffering;
  }

  static public function isFlushing () {
    return self::$_isFlushing;
  }
}

?>
