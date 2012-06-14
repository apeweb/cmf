<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Pr_Campaign_Key_Message implements iCmf_List_Item {
  private $_id = NULL;
  private $_keyMessage = NULL;

  const Prepared_Statement_Library = 'pr_campaigns_prepared_statement_library';

  public function __construct ($prCampaignKeyMessageProperties = array()) {
    Assert::isArray($prCampaignKeyMessageProperties);

    if (isset($prCampaignKeyMessageProperties['id']) == TRUE) {
      $this->setId(intval($prCampaignKeyMessageProperties['id']));
    }

    if (isset($prCampaignKeyMessageProperties['key_message']) == TRUE) {
      $this->setKeyMessage($prCampaignKeyMessageProperties['key_message']);
    }
  }

  public static function getPrCampaignKeyMessage ($id, $active = TRUE) {
    Assert::isInteger($id);
    Assert::isBoolean($active);

    $query = Cmf_Database::call('pr_campaigns_get_campaign_key_message', self::Prepared_Statement_Library);
    // xxx finish
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();

    $row = $query->fetch();

    if ($row == FALSE) {
      throw new RuntimeException("Campaign key message '{$id} does not exist or cannot be found");
    }

    return new Pr_Campaign_Key_Message($row);
  }

  public function setId ($id) {
    Assert::isInteger($id);
    $this->_id = $id;
    return $this;
  }

  public function getId () {
    return $this->_id;
  }

  public function setKeyMessage ($keyMessage) {
    Assert::isString($keyMessage);
    $this->_keyMessage = $keyMessage;
    return $this;
  }

  public function getKeyMessage () {
    return $this->_keyMessage;
  }

  public function save () {
    // xxx finish
  }

  public function delete ($recursive = TRUE) {
    // xxx finish
  }
}

?>