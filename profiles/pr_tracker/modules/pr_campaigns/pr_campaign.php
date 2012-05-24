<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Pr_Campaign implements Cmf_List_Item {
  // Create a new campaign
  public function __construct ($prCampaignProperties = array()) {
    Assert::isArray($prCampaignProperties);
  }
}

?>