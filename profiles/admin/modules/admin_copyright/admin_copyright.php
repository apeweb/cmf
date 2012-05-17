<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Admin_Copyright {
  public static function install () {
    Config::setValue(CMF_REGISTRY, 'admin', 'copyright', 'value', '');
  }

  public static function initialise () {
    Event_Dispatcher::attachObserver(Cmf_Template_Engine_Event::modifyContent, __CLASS__ . '::buildCopyrightNotice');
  }

  public static function buildCopyrightNotice () {
    try {
      $footer = View_Data::getValue('footer');
    }
    catch (Exception $ex) {
      $footer = '';
    }
    View_Data::setValue('footer', $footer . Config::getValue('admin', 'copyright', 'value'));
  }
}

?>