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

define('Route', 'Route');
class Route {
  protected $_url = '';
  protected $_constraints = array();
  protected $_defaults = array();

  public function __construct ($url) {
    $this->setUrl($url);
  }

  public function getConstraints () {
    return $this->_constraints;
  }

  public function setConstraints (array $constraints) {
    $this->_constraints = $constraints;
  }

  public function getDefaults () {
    return $this->_defaults;
  }

  public function setDefaults (array $defaults) {
    $this->_defaults = $defaults;
  }

  public function getRouteData () {
    $expression = $this->_compile();

    // xxx need to test Request::pathInfo() with rewrite turned on

    if (preg_match($expression, Request::pathInfo(), $matches) == TRUE) {
      foreach ($matches as $matchKey => $value) {
        if (is_numeric($matchKey) == TRUE) {
          unset($matches[$matchKey]);
        }
      }

      return $matches;
    }

    return array();
  }

  public function isMatch () {
    $expression = $this->_compile();

    // xxx need to test Request::pathInfo() with rewrite turned on

    if (preg_match($expression, Request::pathInfo()) == TRUE) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  public function getUrl () {
    return $this->_url;
  }

  public function setUrl ($url) {
    // xxx do some validation?
    $this->_url = $url;
  }

  protected function _compile () {
		$expression = preg_replace('#[.\\+*?[^\\]${}=!|]#', '\\\\$0', $this->_url);

		if (strpos($expression, '(') !== FALSE) {
			// Make optional parts of the URI non-capturing and optional
			$expression = str_replace(array('(', ')'), array('(?:', ')?'), $expression);
		}

		$expression = str_replace(array('<', '>'), array('(?P<', '>[^/.,;?\n]++)'), $expression);

		if (count($this->_constraints) > 0) {
			$search = $replace = array();

			foreach ($this->_constraints as $key => $value) {
				$search[]  = '<' . $key . '>[^/.,;?\n]++';
				$replace[] = '<' . $key . '>' . $value;
			}

			$expression = str_replace($search, $replace, $expression);
		}

		return '#^' . $expression . '$#uD';
	}
}

?>