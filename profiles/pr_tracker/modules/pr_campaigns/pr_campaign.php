<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Pr_Campaign implements iCmf_List, iCmf_List_Item {
  protected $_id = NULL;
  protected $_name = NULL;
  protected $_dateStarted = NULL;
  protected $_status = NULL;
  protected $_active = FALSE;
  protected $_keyMessages = NULL;

  const Prepared_Statement_Library = 'pr_campaigns_prepared_statement_library';

  // Create a new campaign (also used by the model to create Pr_Campaign objects for existing campaigns)
  public function __construct ($prCampaignProperties = array()) {
    Assert::isArray($prCampaignProperties);

    if (isset($prCampaignProperties['id']) == TRUE) {
      $this->setId(intval($prCampaignProperties['id']));
    }

    if (isset($prCampaignProperties['name']) == TRUE) {
      $this->setName($prCampaignProperties['name']);
    }

    if (isset($prCampaignProperties['date_started']) == TRUE) {
      $this->setDateStarted($prCampaignProperties['date_started']);
    }

    if (isset($prCampaignProperties['status']) == TRUE) {
      $this->setStatus($prCampaignProperties['status']);
    }

    if (isset($prCampaignProperties['active']) == TRUE) {
      if ($prCampaignProperties['active'] == TRUE) {
        $this->activate();
      }
      else {
        $this->deactivate();
      }
    }
  }

  // Get an existing campaign from the DB
  static public function getPrCampaign ($id, $active = TRUE) {
    Assert::isInteger($id);
    Assert::isBoolean($active);

    $query = Cmf_Database::call('pr_campaigns_get_campaign', self::Prepared_Statement_Library);
    $query->bindValue(':prtc_id', $id);
    $query->bindValue(':prtc_active', $active);
    $query->bindValue(':prtcs_active', $active);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();

    $row = $query->fetch();

    if ($row == FALSE) {
      throw new RuntimeException("Campaign '{$id} does not exist or cannot be found");
    }

    return new Pr_Campaign($row);
  }

  // Check to see if a campaign exists
  static public function exists ($id, $active = TRUE) {
    Assert::isInteger($id);
    Assert::isBoolean($active);

    $query = Cmf_Database::call('pr_campaigns_campaign_exists', self::Prepared_Statement_Library);
    $query->bindValue(':prtc_id', $id);
    $query->bindValue(':prtc_active', $active);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();

    return (bool) $query->fetchColumn();
  }

  public function setId ($id) {
    Assert::isInteger($id);
    $this->_id = $id;
    return $this;
  }

  public function getId () {
    return $this->_id;
  }

  public function setName ($name) {
    Assert::isString($name);
    $this->_name = $name;
    return $this;
  }

  public function getName () {
    return $this->_name;
  }

  public function setDateStarted ($dateStarted) {
    Assert::isString($dateStarted);
    $this->_dateStarted = $dateStarted;
    return $this;
  }

  public function getDateStarted () {
    return $this->_dateStarted;
  }

  public function setStatus ($status) {
    Assert::isString($status);
    $this->_status = $status;
    return $this;
  }

  public function getStatus () {
    return $this->_status;
  }

  public function addKeyMessage (Pr_Campaign_Key_Message $keyMessage) {
    if ($keyMessage->getId() == NULL) {
      $keyMessage->save();
    }

    if ($this->_isKeyMessageInUse($keyMessage->getId() == TRUE)) {
      throw new RuntimeException("Key message '{$keyMessage->getId()}' is in use by another campaign");
    }

    $this->_keyMessages[$keyMessage->getId()] = $keyMessage;

    return $this;
  }

  private function _isKeyMessageInUse ($id) {
    $inUse = TRUE;

    // xxx check to see if key message is in use on another campaign

    return $inUse;
  }

  public function getKeyMessages () {
    if ($this->_keyMessages === NULL && $this->_id !== NULL) {
      // xxx load key messages
    }

    return $this->_keyMessages;
  }

  // Removes the key message from this campaign, but does not delete the key message
  /**
   * $keyMessage = Pr_Campaign_Key_Message::getKeyMessage(2);
   * $prCampaign = Pr_Campaign::getPrCampaign();
   * $prCampaign->removeKeyMessage($keyMessage);
   * $keyMessage->delete(); // Not doing so won't break anything but will orphan the key message and cause the db to fill up with junk
   */
  public function removeKeyMessage (Pr_Campaign_Key_Message $keyMessage) {
    unset($this->_keyMessages[$keyMessage->getId()]);
    return $this;
  }

  public function activate () {
    $this->_active = TRUE;
    return $this;
  }

  public function deactivate () {
    $this->_active = FALSE;
    return $this;
  }

  public function isActive () {
    return $this->_active;
  }

  // Saves the new campaign, or changes to an existing campaign
  public function save () {
    if ($this->_id === NULL) {
      $query = Cmf_Database::call('pr_campaigns_add_campaign', self::Prepared_Statement_Library);
      $query->bindValue(':prtc_id', $this->_id);
      $query->bindValue(':prtc_name', $this->_name);
      $query->bindValue(':prtc_active', $this->_active);
      $query->bindValue(':s_id', Config::getValue('site', 'id'));
      $query->execute();
    }
    else {
      $query = Cmf_Database::call('pr_campaigns_update_campaign', self::Prepared_Statement_Library);
      $query->bindValue(':prtc_id', $this->_id);
      $query->bindValue(':prtc_name', $this->_name);
      $query->bindValue(':prtc_active', $this->_active);
      $query->bindValue(':s_id', Config::getValue('site', 'id'));
      $query->execute();
    }

    // xxx update the status table

    if ($this->_keyMessages !== NULL) {
      // xxx update key messages
    }

    return $this;
  }

  // Delete the campaign
  public function delete ($recursive = TRUE) {
    $query = Cmf_Database::call('pr_campaigns_delete_campaign', self::Prepared_Statement_Library);
    $query->bindValue(':prtc_id', $this->_id);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();

    if ($recursive == TRUE) {
      foreach ($this->getKeyMessages() as $keyMessage) {
        $keyMessage->delete();
      }
    }
  }
}

?>