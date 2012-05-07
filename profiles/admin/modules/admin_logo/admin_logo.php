<?php

/**
 * Copyright (c) 2011 Ape Web Ltd.  All rights reserved.
 *
 * The use and distribution terms for this software are contained in the file
 * named license.txt, which can be found in the root of this distribution.
 * By using this software in any fashion, you are agreeing to be bound by the
 * terms of this license.
 *
 * You must not remove this notice, or any other, from this software.
 */

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Admin_Logo {
  public static function install () {
    // Config settings
    //Config::setValue(CMF_REGISTRY, 'admin', 'logo', 'setting_name', 'setting_value');
  }

  public static function initialise () {
    Event_Dispatcher::attachObserver(Cmf_Template_Engine_Event::modifyContent, __CLASS__ . '::setLogo');
  }

  public static function setLogo () {
    // xxx make specific to user logged in
    View_Data::setValue('logo', '<img src="/themes/admin/images/logo.jpg" height="50" alt="Company Name" />');
  }
}

?>