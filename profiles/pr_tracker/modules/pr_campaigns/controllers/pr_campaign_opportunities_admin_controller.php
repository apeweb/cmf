<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Pr_Campaigns_Admin_Controller extends Cmf_Controller {
  // Manage campaigns
  public static function manage ($arguments = array()) {
    Admin_Controller::shared();

    $content = new Cmf_Template_Control;
    $content->templatePath = dirname(__DIR__) . '/views/pr_campaigns' . PHP_EXT;
    View_Data::setValue('content', $content);
  }

  // Start a new campaign
  public static function add () {
    Admin_Controller::shared();
    self::edit();
  }

  // Edit a new campaign
  public static function edit ($arguments = array('id' => 0)) {
    Admin_Controller::shared();

    if (isset($arguments['id']) == FALSE) {
      // xxx show the http_error controller bad_request view
    }

    if ($arguments['id'] > 0) {
      // edit
      $prCampaign = Pr_Campaign::getPrCampaign(intval($arguments['id']));

      View_Data::setValue('page', 'title', 'Edit &quot;' . $prCampaign->getName() . '&quot; Monitor');
    }
    else {
      // add
      
      View_Data::setValue('page', 'title', 'Add a New Monitor');
    }

    $content = new Cmf_Template_Control;
    $content->templatePath = dirname(__DIR__) . '/views/pr_campaign_edit' . PHP_EXT;
    View_Data::setValue('content', $content);
  }

  // View a running campaign
  public static function view ($arguments = array()) {
    Admin_Controller::shared();

    $content = new Cmf_Template_Control;
    $content->templatePath = dirname(__DIR__) . '/views/pr_campaign_report' . PHP_EXT;
    View_Data::setValue('content', $content);
  }

  // Delete a campaign
  public static function delete ($arguments = array()) {
    Admin_Controller::shared();
  }
}

?>