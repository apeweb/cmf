<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Cmf_Template_Engine {
  public static function initialise () {
    Event_Dispatcher::attachObserver(Cmf_Application_Event::terminate, __CLASS__ . '::renderMasterTemplate');
  }

  public static function setMasterTemplate ($template) {
    if (is_file($template) == FALSE) {
      throw new Argument_Exception("Template '{$template}' could not be found");
    }

    Memory::setValue('page', 'template', 'master', $template);
  }

  public static function getMasterTemplate () {
    return Memory::getValue('page', 'template', 'master');
  }

  public static function renderMasterTemplate () {
    // Allows modules to populate the View with data
    Event_Dispatcher::notifyObservers(Cmf_Template_Engine_Event::buildContent);
    // Allows modules to modify the View data as this event means that all content to be added should be added by now
    Event_Dispatcher::notifyObservers(Cmf_Template_Engine_Event::modifyContent);

    $templatePath = self::getMasterTemplate();
    self::renderTemplate($templatePath);
  }
  
  public static function renderTemplate ($templatePath) {
    if ($templatePath == NULL) {
      throw new Argument_Exception("Template is not defined");
    }

    if (is_file($templatePath) == FALSE) {
      throw new Argument_Exception("Template '{$templatePath}' could not be found");
    }

    Response_Buffer::addContent(self::processTemplate($templatePath));
  }

  public static function processTemplate ($templatePath) {
    ob_start();
    require_once($templatePath);
    return ob_get_clean();
  }
}

?>