<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Admin_Price_Settings_Controller extends Controller {
  // manage price labels
  public static function priceLabels () {
    Admin_Controller::shared();
    $content = new Cmf_Template_Control;
    $content->templatePath = dirname(__DIR__) . '/views/admin_price_labels' . PHP_EXT;
    View_Data::setValue('content', $content);
  }

  // manage price validity
  public static function priceValidityConditions () {
    Admin_Controller::shared();
    $content = new Cmf_Template_Control;
    $content->templatePath = dirname(__DIR__) . '/views/admin_price_validity' . PHP_EXT;
    View_Data::setValue('content', $content);
  }
}

?>