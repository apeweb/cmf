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

// xxx rename to Response_Buffer_Helper
class Response_Buffer_Event_Helper {
  /**
   * This whole function serves two purposes:
   * 1. To try and push the content from the Response_Buffer to the client; and
   * 2. To allow events to manipulate the data if an error hasn't occurred.
   *
   * If the method fails to push the content to the buffer the system can potentially end up in a loop with itself so it
   * is important the checks, in particular the error checks, added are not fiddled with.
   *
   * If you get a blank white page, step through this function, watch the error returned by the error_get_last()
   * function, as it won't always be the same as the error in the buffer.
   *
   * @param $buffer The data in the output buffer
   * @return string The data in the output buffer to flush
   */
  static public function notifyFlushEventObservers ($buffer) {
    static $flushing = FALSE;

    Assert::isString($buffer);

    if (Response_Buffer::isBuffering() == FALSE) {
      return '';
    }

    // The response is ending, most likely due to an error being handled, return the buffer in it's unmodified state
    // If we haven't got to this stage, and we encounter an error, we need to check to see if the Response class has
    // even loaded as if not we will trigger yet another exception "Class declarations may not be nested"
    if (class_exists('Response', FALSE) && Response::isEnding() == TRUE) {
      // Send no headers, as they should have already been sent
      return $buffer;
    }

    // Avoid a blank white page by detecting if we hit a warning while flushing
    if ($flushing == TRUE) {
      // Send some headers to attempt a gracefully fallback...
      if (Response_Buffer::headersSent() == FALSE) {
        header('content-encoding:', TRUE);
        header('content-type: text/html', TRUE);
      }
      return $buffer;
    }

    // Avoid a blank white page by detecting if we hit an error while flushing
    $lastError = @error_get_last();
    if (@is_array($lastError) == TRUE
      && isset($lastError['type']) == TRUE
      && ($lastError['type'] & E_ERROR | E_USER_ERROR)) {
      return $buffer;
    }

    $flushing = TRUE;

    $eventData = new Event_Data;
    $eventData->buffer = $buffer;

    // Allows modules to modify content into the output buffer
    Event_Dispatcher::notifyObservers(Response_Buffer_Event_Helper_Event::preprocess, $eventData);
    // Allows output to be converted into different data formats such as gzip compressed data
    Event_Dispatcher::notifyObservers(Response_Buffer_Event_Helper_Event::flush, $eventData);

    /**
     * Sending headers here ensures that any errors which occur prior to flushing while attempting to flush, don't cause
     * the response to either corrupt or return nothing
     */
    Response_Buffer::sendHeaders();

    // If something has unset the response buffer, make sure it doesn't cause an error
    if (isset($eventData->buffer) == FALSE) {
      return '';
    }

    return $eventData->buffer;
  }
}

?>