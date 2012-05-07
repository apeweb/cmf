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

class Mail_Address {
  private $_address = '';
  private $_displayName = '';
  private $_host = '';
  private $_userName = '';

  public function __construct ($address, $displayName = '') {
    if (empty($address) == TRUE) {
      throw new LengthException ('The e-mail address cannot be empty.');
    }

    $this->_displayName = trim($displayName);

    $this->_parseAddress($address);
  }

  private function _parseAddress ($address) {
    if (preg_match( "/^(([^<>()[\]\\\\.,;:\s@\"]+(\.[^<>()[\]\\\\.,;:\s@\"]+)*)|(\"([^\"\\\\\r]|(\\\\[\w\W]))*\"))@((\[([0-9]{1,3}\.){3}[0-9]{1,3}\])|(([a-z\-0-9????????????????]+\.)+[a-z]{2,}))$/i", $address)) {
      $this->_address = $address;
      $this->_getParts();
    }
    else {
      throw new UnexpectedValueException ('E-mail address is not valid.');
    }
  }

  private function _getParts () {
    $index = strstr($this->_address, '@');
    $this->_userName = substr($this->_address, 0, $index);
    $this->_host = substr($this->_address, $index + 1);
  }

  public function displayName () {
    return $this->_displayName;
  }

  public function host () {
    return $this->_host;
  }

  public function smtpAddress () {
    return '<' . $this->_address . '>';
  }

  public function user () {
    return $this->_userName;
  }
}

//try {
//  $email_address = new Mail_Address('foo@bar.com');
//}
//catch (UnexpectedValueException $ex) {
//  // email address is wrongly formatted
//}
//catch (LengthException $ex) {
//  // email address is empty
//}

?>