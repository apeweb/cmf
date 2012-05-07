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

class Literal_Xml_Driver extends Literal_Driver {
  private $_document = NULL; // DOMDocument
  private $_phraseXpath = array();
  private $_arguments = array(0 => '$phrase', 1 => '$group');
  private $_options = array();

  public function getPhrase ($phrase, $group = '') {
    $xpath = new DOM_XPath_Expression;

    foreach (func_get_args() as $parameter => $value) {
      if (isset($this->_arguments[$parameter])) {
        $xpath->bind($this->_arguments[$parameter], $value);
      }
      else {
        // Most likely caused because too many arguments are passed to the Literal::getPhraseByGroup() or if the group does not exist
        return $phrase;
      }
    }

    if (isset($this->_phraseXpath[func_num_args()])) {
      $xPathExpression = $xpath->compile($this->_phraseXpath[func_num_args()]);
    }
    elseif ($this->_phraseXpath['_default']) {
      $xPathExpression = $xpath->compile($this->_phraseXpath['_default']);
    }
    else {
      throw new DOM_XPath_Exception('XPath expression cannot be evaluated due to incorrect number of arguments');
    }

    $result = $this->_document->xpath($xPathExpression);

    $numResults = count($result);
    if ($numResults > 1) {
      throw new DOM_XPath_Exception('XPath expression returned more than 1 result');
    }
    elseif ($numResults < 1) {
      return $phrase;
    }

    return $result[0];
  }

  // xxx make this more efficient, or include caching within this
  public function phraseExists ($phrase, $group = '') {
    $xpath = new DOM_XPath_Expression;

    foreach (func_get_args() as $parameter => $value) {
      if (isset($this->_arguments[$parameter])) {
        $xpath->bind($this->_arguments[$parameter], $value);
      }
      else {
        // Most likely caused because too many arguments are passed to the Literal::getPhraseByGroup() or if the group does not exist
        return FALSE;
      }
    }

    if (isset($this->_phraseXpath[func_num_args()])) {
      $xPathExpression = $xpath->compile($this->_phraseXpath[func_num_args()]);
    }
    elseif ($this->_phraseXpath['_default']) {
      $xPathExpression = $xpath->compile($this->_phraseXpath['_default']);
    }
    else {
      throw new DOM_XPath_Exception('XPath expression cannot be evaluated due to incorrect number of arguments');
    }

    $result = $this->_document->xpath($xPathExpression);

    $numResults = count($result);
    if ($numResults > 1) {
      throw new DOM_XPath_Exception('XPath expression returned more than 1 result');
    }
    elseif ($numResults < 1) {
      return FALSE;
    }

    return TRUE;
  }

  private function _setPhraseXpath ($xpath, $numArgs = NULL) {
    // xxx assert $numArgs is numeric

    if ($numArgs === NULL) {
      $numArgs = '_default';
    }
    $this->_phraseXpath[$numArgs] = $xpath;
  }

  //public function load ($path, $usePathLibrary = TRUE) {
  public function load ($options) {
    if (!isset($options['path'])) {
      // xxx throw better exception
      throw new exception ('path must be set');
    }

    foreach ($options as $optionName => $optionValue) {
      switch ($optionName) {
        case 'bindArgumentToParameter':
          foreach ($optionValue as $option) {
            $this->_bindArgumentToParameter($option['argument'], $option['parameter']);
          }
        break;

        case 'setPhraseXpath':
          foreach ($optionValue as $option) {
            if (!isset($option['numArgs'])) {
              $option['numArgs'] = NULL;
            }
            $this->_setPhraseXpath($option['xpath'], $option['numArgs']);
          }
        break;
      }
    }

    // xxx add support for path library
    $this->_document = simplexml_load_file($options['path']);

    if ($this->_document == FALSE) {
      // xxx throw better exception
      throw new exception ('file failed to load');
    }

    $this->_options = $options;
  }

  private function _bindArgumentToParameter ($parameter, $argument) {
    $this->_arguments[$parameter] = $argument;
  }
}

?>