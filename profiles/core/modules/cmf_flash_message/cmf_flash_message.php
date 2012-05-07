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

class Cmf_Flash_Message {
  public static function setMessage ($message, $type = '', $repeat = TRUE) {
    try {
      $messages = Session::getValue('flash_message');
    }
    catch (Exception $ex) {
      $messages = array();
    }

    if (isset($messages[$type]) == FALSE) {
      $messages[$type] = array();
    }

    if ($repeat == TRUE || in_array($message, $messages[$type]) == FALSE) {
      $messages[$type][] = $message;
    }

    Session::setValue('flash_message', $messages);

    // xxx mark this page as being uncacheable.
    //drupal_page_is_cacheable(FALSE);

    return $messages;
  }

  public static function getMessages ($type = NULL, $clearQueue = TRUE) {
    $flashMessages = array();

    try {
      $messages = Session::getValue('flash_message');
    }
    catch (Exception $ex) {
      $messages = array();
    }

    if (count($messages) > 0) {
      if ($type != NULL) {
        if (isset($messages[$type]) == TRUE) {
          $flashMessages = $messages[$type];
        }

        if ($clearQueue == TRUE) {
          unset($messages[$type]);
        }

        if (count($messages) > 0) {
          Session::setValue('flash_message', $messages);
        }
        else {
          Session::deleteValue('flash_message');
        }
      }
      else {
        $flashMessages = $messages;

        if ($clearQueue == TRUE) {
          Session::deleteValue('flash_message');
        }
      }
    }

    return $flashMessages;
  }
}

?>