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

// xxx add support for compression

define('Response_Buffer_X', 'Response_Buffer_X');
class Response_Buffer_X {
  static protected $_content = '';
  static private $_shutdownFunctionRegistered = FALSE;

  static public function start () {
    ob_start();

    // Make sure we don't register the shutdown function more than once
    if (self::$_shutdownFunctionRegistered == FALSE) {
      register_shutdown_function(array(__CLASS__, 'flush'));
      self::$_shutdownFunctionRegistered = TRUE;
    }
  }

  static public function stop () {
    ob_end_flush();
  }

  // Appends the buffered content
  static public function addContent ($content, $replace = FALSE) {
    // xxx assert string

    $eventData = new Event_Data;
    $eventData->content = $content;
    $eventData->replace = $replace;

    Event_Dispatcher::notifyObservers(Response_Buffer_Event::addContent, $eventData);

    if ($replace == FALSE) {
      self::$_content .= $eventData->content;
    }
    else {
      self::$_content = $eventData->content;
    }
  }

  // Access buffered content
  static public function getContent () {
    $eventData = new Event_Data;
    $eventData->content = self::$_content;

    Event_Dispatcher::notifyObservers(Response_Buffer_Event::getContent, $eventData);
  
    return $eventData->content;
  }

  /**
   * Erases buffered content but not response headers. You can use this method to handle
   * error cases.
   */
  static public function clearContent () {
    self::$_content = '';
  }

  static public function clear () {
    // xxx clear content and headers
    //header_remove("X-Foo");
    //Headers::removeAll();
    self::$_content = '';
  }
  
  static public function sendHeaders () {
    if (Event_Dispatcher::eventTriggered(Response_Buffer_Event::sendHeaders) == FALSE) {
		  Event_Dispatcher::notifyObservers(Response_Buffer_Event::sendHeaders);
		}
  }

  /**
   * Forces the content to be output
   */
  static public function flush () {
    self::sendHeaders();
    
    $eventData = new Event_Data;
    $eventData->content = self::$_content;

    if (Event_Dispatcher::eventTriggered(Response_Buffer_Event::flush) == FALSE) {
		  Event_Dispatcher::notifyObservers(Response_Buffer_Event::flush, $eventData);
		}

    echo $eventData->content;
    self::$_content = '';
  }
}

?>