<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Cookie {
  private $_name = '';
  private $_value = '';
  private $_expire = 0;
  private $_path = '/';
  private $_domain = '';
  private $_secure = FALSE;
  private $_httpOnly = FALSE;

  public function __toString () {
    $cookieString = 'Set-Cookie: ' . rawurlencode($this->_name) . '=' . rawurlencode($this->_value);

    if ($this->_expire != 0) {
      $cookieString .= '; Expires=' . date('D, d-M-Y H:i:s T', $this->_expire);
    }

    if (trim($this->_path) != '') {
      $cookieString .= '; Path=' . $this->_path;
    }

    if (trim($this->_domain) != '' && count(explode('.', $this->_domain)) > 2 && !is_numeric(str_replace('.', '', $this->_domain))) {
      $cookieString .= '; Domain=' . $this->_domain;
    }

    if ($this->_secure == TRUE) {
      $cookieString .= '; Secure';
    }

    if ($this->_httpOnly == TRUE) {
      $cookieString .= '; HttpOnly';
    }

    return $cookieString;
  }

  public function setName ($name) {
    $this->_name = $name;
  }

  public function getName () {
    return $this->_name;
  }

  public function setValue ($value) {
    if ($value === FALSE) {
      $value = 0;
    }
    elseif ($value === TRUE) {
      $value = 1;
    }

    $this->_value = $value;
  }

  public function getValue () {
    return $this->_value;
  }

  public function setExpire ($dateTime) {
    $this->_expire = $dateTime;
  }

  public function getExpire () {
    return $this->_expire;
  }

  public function setPath ($path) {
    $this->_path = $path;
  }

  public function getPath () {
    return $this->_path;
  }

  public function setDomain ($domain) {
    $this->_domain = $domain;
  }

  public function getDomain () {
    return $this->_domain;
  }

  public function setSecure ($secure) {
    $this->_secure = $secure;
  }

  public function getSecure () {
    return $this->_secure;
  }

  public function setHttpOnly ($httpOnly) {
    $this->_httpOnly = $httpOnly;
  }

  public function getHttpOnly () {
    return $this->_httpOnly;
  }
}

?>