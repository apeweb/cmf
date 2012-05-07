<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

// this should be split into another section and folder
// should be Media::styleSheets
// SEO::robotsMetaTag
// Analytics::trackVisit

// xxx include css registration

define('Page', 'Page');
class Page {
  public static function robotsMetaTags () {
  }

  public static function styleSheets () {
    $styleSheets = '';

    foreach (Css::registeredStyleSheets() as $styleSheet) {
      // format the stylesheet like such?
      //$styleSheets .= '<link rel="' . $styleSheet->rel . '"
    }
  }

  public static function theme () {
  }

  public static function scripts () {
  }

  public static function favicon () {
  }

  public static function tracking () {
  }

  public static function deferredScripts () {
  }
}

?>