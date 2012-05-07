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

class Cmf_Exception_Handler {
  static public function catchUncaught ($exception, $message = NULL, $file = NULL, $line = NULL) {
    try {
      $code = $exception->getCode();
      $type = get_class($exception);
      $message = $exception->getMessage();
      $file = $exception->getFile();
      $line = $exception->getLine();

      try {
        Log::writeEntry("An uncaught exception '" . $type . "' has been handled", $message, Log_Type::runtime, Log_Level::error, func_get_args());
      }
      catch (RuntimeException $ex) {
        $message .= "\n\nIn addition to the previous error, the error could not be logged due to the following: " . $ex->getMessage();
      }

      // Send the headers
      if (Response_Buffer::headersSent() == FALSE && Response_Buffer::isFlushing() == FALSE) {
        if (method_exists($exception, 'sendHeaders') == TRUE) {
          $exception->sendHeaders();
        }
        else {
          header('HTTP/1.1 500 Internal Server Error');
        }
      }

      // Make sure the output is free from HTML as it could break the page
      $message = htmlspecialchars($message);
      $line = htmlspecialchars($line);
      $file = htmlspecialchars($file);
      $backtrace = Backtrace::steps();

      if (Environment::isCommandLine() == FALSE) {
        // require is specifically used here to force an error if the file could not be found
        require(Cmf_Application_Environment::getCoreProfilePath() . 'views' . DIRECTORY_SEPARATOR . 'basic_error' . PHP_EXT);
      }
      else {
        require(Cmf_Application_Environment::getCoreProfilePath() . 'views' . DIRECTORY_SEPARATOR . 'basic_error_cli' . PHP_EXT);
      }

  		// Turn off error reporting to prevent any further errors being output
    	error_reporting(0);
    	Response::end();
    }
    /**
     * Catch errors/exception within uncaught exceptions, this is the least graceful we can go
     * before outputting a wasteful error message
     */
    catch (Exception $ex) {
      if (headers_sent() == FALSE) {
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-type: text/html', TRUE);
      }

      echo $ex->getMessage() . '<br>';
      echo 'File: ' . $ex->getFile() . '<br>';
      echo 'Line: ' . $ex->getLine() . '<br>';
      echo '<pre>' . htmlspecialchars(print_r(debug_backtrace(), TRUE)) . '</pre>';
      exit;
    }
  }

  /**
   * This method sends E_USER_ERRORs and E_RECOVERABLE_ERRORs to the exception
   * handler for recovery while other errors are just logged to prevent the
   * script from exiting due to an uncaught exception
   */
  static public function throwErrorException ($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
  }
}

?>