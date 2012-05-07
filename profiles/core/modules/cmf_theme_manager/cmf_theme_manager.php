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

class Cmf_Theme_Manager {
  static private $_theme = NULL;

  static public function initialise () {
    Event_Dispatcher::attachObserver(Cmf_Application_Event::execute, __CLASS__ . '::initialiseTheme');
  }

  static public function initialiseTheme () {
    $eventData = new Event_Data;
    $eventData->theme = Config::getValue('page', 'theme');
    // Allows other modules to specify the theme that should be used
    Event_Dispatcher::notifyObservers(Cmf_Theme_Manager_Event::initialiseTheme, $eventData);

    if (isset($eventData->theme) == FALSE) {
      // xxx enable when module is finished
      Debug::logMessage(__CLASS__, 'Enable exception on line ' . (((int) __LINE__)+1));
      //throw new RuntimeException("Theme not set");
      return;
    }

    if (class_exists($eventData->theme) == FALSE) {
      throw new RuntimeException("Invalid theme '{$eventData->theme}' set");
    }

    // xxx check theme is using the correct interface
    // xxx following needs to be statically called
    self::$_theme = new $eventData->theme;

    foreach (self::$_theme->getCssFiles() as $path) {
      $css = new Css_File_Control;
      $css->href = $path;
      Cmf_Page::add('page', 'head', 'css', $css);
    }

    foreach (self::$_theme->getJsFiles() as $path) {
      $css = new Js_File_Control;
      $css->src = $path;
      Cmf_Page::add('page', 'head', 'js', $css);
    }

    // xxx trigger another event to allow modules to set what needs to go into each region?

    // xxx load the correct template engine
    Cmf_Module_Manager::loadModule(self::$_theme->templateEngine());
  }

  static public function setDefaultTheme ($defaultTheme) {
    // xxx provide a control panel for setting the default theme
    // xxx check module exists
    Config::setValue('page', 'theme', $defaultTheme);
  }
}

?>