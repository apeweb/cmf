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

define('Config_Xml_Tree_Driver', 'Config_Xml_Tree_Driver');
class Config_Xml_Tree_Driver extends Config_Driver {
  public function getKey ($key, $group = '') {

  }

  public function keyExists ($key, $group = '') {
    $xpath = new DOM_XPath_Expression;
    $xpathString = '';

    $args = func_get_args();
    array_shift($args);
    if (count() > 0) {
      $xpathString = implode('/', $args);
    }

    $xPathExpression = $xpath->compile($xpathString);

    $numResults = count($result);
    if ($numResults > 1) {
      throw new DOM_XPath_Exception('XPath expression returned more than 1 result');
    }
    elseif ($numResults < 1) {
      return FALSE;
    }

    return TRUE;
  }

  public function load ($options) {
    if (!isset($options['path'])) {
      // xxx throw better exception
      throw new exception('path must be set');
    }

    // xxx add support for path library
    $this->_document = simplexml_load_file($options['path']);

    if ($this->_document == FALSE) {
      // xxx throw better exception
      throw new exception('file failed to load');
    }

    $this->_options = $options;
  }
}

?>