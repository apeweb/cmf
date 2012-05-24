<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Pr_Campaigns_Module extends Cmf_Module {
  const Prepared_Statement_Library = 'pr_campaigns_prepared_statement_library';

  public static function install () {
    // xxx add the routes so the controller can be found
    // xxx install the required settings into the registry
  }

  public static function uninstall () {
    // xxx remove the stored routes
    // xxx remove any settings from the registry
  }

  public static function initialise () {
    Event_Dispatcher::attachObserver(Cmf_Template_Engine_Event::buildContent, __CLASS__ . '::buildContent');
  }

  // Add controls to the View_Data
  public static function buildContent () {
    // xxx do a switch statement on the URL to work out what we need to add, if anything
  }
}

?>