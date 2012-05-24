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
    $content->templatePath = dirname(__DIR__) . '/views/admin_products' . PHP_EXT;
    View_Data::setValue('content', $content);
  }

  // Start a new campaign
  public static function add ($arguments = array()) {
    Admin_Controller::shared();
  }

  // Edit a new campaign
  public static function edit ($arguments = array()) {
    Admin_Controller::shared();
  }

  // Delete a campaign
  public static function delete ($arguments = array()) {
    Admin_Controller::shared();
  }
}

?>