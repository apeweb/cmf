<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Pr_Campaign extends Cmf_List_Item {
  protected $_id = NULL;
  protected $_name = NULL;
  protected $_active = FALSE;

  // Create a new campaign
  public function __construct ($prCampaignProperties = array()) {
    Assert::isArray($prCampaignProperties);

    if (isset($menuLinkProperties['name']) == TRUE) {
      Assert::isString($menuLinkProperties['name']);
      $this->_name = $menuLinkProperties['name'];
    }

    if (isset($menuLinkProperties['active']) == TRUE) {
      Assert::isBoolean($menuLinkProperties['active']);
      $this->_active = $menuLinkProperties['active'];
    }
  }

  static public function getPrCampaign ($id) {

  }

  static public function exists ($id) {

  }

  public function getId () {
    return $this->_id;
  }

  public function setName () {

  }

  public function getName () {

  }

  public function activate () {

  }

  public function deactivate () {

  }

  public function isActive () {

  }

  // Saves the new campaign, or changes to an existing campaign
  public function save () {

  }

  // Delete campaign
  public function delete () {

  }
}

?>