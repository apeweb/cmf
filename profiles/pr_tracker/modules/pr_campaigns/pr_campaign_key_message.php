<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Pr_Campaign_Key_Message implements iCmf_List_Item {
  private $_id = NULL;
  private $_keyMessage = NULL;

  public function __construct ($prCampaignKeyMessageProperties = array()) {
    Assert::isArray($prCampaignKeyMessageProperties);

    if (isset($prCampaignKeyMessageProperties['id']) == TRUE) {
      Assert::isInteger($prCampaignKeyMessageProperties['id']);
      $this->_id = $prCampaignKeyMessageProperties['id'];
    }

    if (isset($prCampaignKeyMessageProperties['key_message']) == TRUE) {
      Assert::isString($prCampaignKeyMessageProperties['key_message']);
      $this->_keyMessage = $prCampaignKeyMessageProperties['key_message'];
    }
  }

  public static function getPrCampaignKeyMessage ($id) {

  }

  public function getId () {
    return $this->_id;
  }

  public function setKeyMessage () {

  }

  public function getKeyMessage () {

  }

  public function save () {

  }

  public function delete ($recursive = TRUE) {

  }
}

?>