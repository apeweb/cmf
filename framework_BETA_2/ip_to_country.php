<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Ip_To_Country {
  private $_db = Database;
  private $_request = Request;

  private $_ipNumber = 0;
  private $_ipAddress = '';
  private $_countryIsoCode = '';
  private $_countryName = '';

  public function __construct () {
    $this->_db = new Database;
    $this->_request = new Request;

    $this->ipAddress($this->_request->visitorHostAddress());
  }

  public function ipAddress ($ipAddress) {
    $this->_ipAddress = $ipAddress;
    $this->_ipNumber = sprintf('%u', ip2long($this->_ipAddress));
    $this->_reset();
  }

  private function _reset () {
    $this->_countryIsoCode = '';
    $this->_countryName = '';
  }

  public function getCountryIsoCode () {
    if (trim($this->_ipAddress) == '') {
      return '';
    }

    try {
      $sp = new Stored_Procedure('ip_select_country');
      $sp->addParameter('@ip_number', $this->_ipNumber, $this->_db->type['int']);
      $ds = $this->_db->execute($sp);
    }
    catch (Exception $ex) {
      // xxx log error
    }

    try {
      if ($ds->tables->count() > 0 && $ds->table(1)->rows->count() > 0) {
        $this->_countryName = $ds->table(1)->row(0)->country_name;
        $this->_countryIsoCode = $ds->table(1)->row(0)->country_code;
      }
    }
    catch (Exception $ex) {
      // xxx log error
    }

    return $this->_countryIsoCode;
  }

  public function getCountryName () {
    $this->getCountryIsoCode();
    return $this->_countryName;
  }
}

?>