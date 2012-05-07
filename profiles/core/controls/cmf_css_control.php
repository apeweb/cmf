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

/** xxx there is an IE bug that prevents more than 31 linked stylesheets, need to build something to work around this
        such as a caching mechanism that loads all files in 1 request **/
class Cmf_Css_Control extends Cmf_Control {
  private $_cssFiles = array();

  public function addCss ($url, $media = 'all') {
    $this->_cssFiles[$url] = array(
      'media' => $media
    );
  }

  public function removeCss ($url) {
    unset($this->_cssFiles[$url]);
  }

  public static function render (Cmf_Css_Control $control) {
    foreach ($control->_cssFiles as $url => $attributes) {
      $control->_content .= '<link href="' . $url . '" type="text/css" rel="stylesheet" media="' . $attributes['media'] . '" />';
    }
  }
}

?>