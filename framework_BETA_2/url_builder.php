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

// xxx needs a rewrite!

/**
 * Will build a url, or part of a url, that can be used for linking and will
 * maintain values passed in the request if set to
 */
class Url_Builder {
  private $_request = Request;

  private $_queryStringParts = array();
  private $_urlParts = array();
  private $_url = '';

  private $_includeScheme = TRUE;
  private $_includeHost = TRUE;
  private $_includePort = NULL; // NULL = detect automatically
  private $_includePath = TRUE;
  private $_includeQueryString = TRUE;

  public function __construct () {
    $this->_request = new Request;

    $this->_urlParts['scheme'] = $this->_request->scheme;

    $this->_urlParts['host'] = $this->_request->host;

    if ($this->_request->isCommonPort() == FALSE) {
      $this->_urlParts['port'] = $this->_request->port;
    }
    else {
      $this->_urlParts['port'] = '';
    }

    $this->_urlParts['path'] = $this->_request->path;

    if (trim(substr($this->_request->queryString(), 1)) != '') {
      parse_str($this->_request->queryString(), $this->_queryStringParts);
    }
    else {
      $this->_queryStringParts = array();
    }
  }

  public function __set ($variableName, $value) {
    switch ($variableName) {
      case 'scheme':
      case 'host':
      case 'path':
        if (is_object($value) == FALSE && is_array($value) == FALSE) {
          $this->{'_' . $variableName} = $value;
          return;
        }
        else {
          throw new Exception ("Conversion from '" . gettype($value) . "' to type 'string' in '" . $variableName . "' is not valid.");
        }
      break;

      case 'queryString':
        if (is_object($value) == FALSE && is_array($value) == FALSE) {
          $this->_queryStringParts = array($value);
          return;
        }
        else {
          throw new Exception ("Conversion from '" . gettype($value) . "' to type 'string' in '" . $variableName . "' is not valid.");
        }
      break;

      case 'port':
        if (is_numeric($value) == TRUE && is_string($value) == FALSE) {
          $this->{'_' . $variableName} = $value;
          return;
        }
        else {
          throw new Exception ("Conversion from '" . gettype($value) . "' to type 'integer' in '" . $variableName . "' is not valid.");
        }
      break;

      default:
        throw new Missing_Value_Exception($variableName);
      break;
    }
  }

  public function __get ($variableName) {
    switch ($variableName) {
      case 'scheme':
      case 'host':
      case 'path':
      case 'port':
        return $this->{'_' . $variableName};
      break;

      case 'queryString':
        return $this->_buildQueryString($this->_queryStringParts, '', '&amp;');
      break;

      case 'url':
        return $this->url();
      break;

      default:
        throw new Missing_Value_Exception($variableName);
      break;
    }
  }

  public function addQueryStringArg ($name, $value = NULL, $replace = TRUE) {
    if ($replace == TRUE || isset($this->_queryStringParts[$name]) == FALSE) {
      $this->_queryStringParts[$name] = $value;
    }
  }

  public function removeQueryStringArg ($name) {
    if (isset($this->_queryStringParts[$name])) {
      unset($this->_queryStringParts[$name]);
    }
  }

  public function url () {
    // blank out the url so we can start over building a new one
    $this->_url = '';

    // if the host is empty then we presume the programmer wants the current host
    if ($this->_includeHost == TRUE && trim($this->_urlParts['host']) == '') {
      $this->_includeHost = FALSE;
    }

    // the scheme can only be used if the host is included to ensure the url works
    if ($this->_includeScheme == TRUE && $this->_includeHost == TRUE) {
      $this->_url .= $this->_urlParts['scheme'] . '://';
    }

    // include the host if required
    if ($this->_includeHost == TRUE) {
      $this->_url .= $this->_urlParts['host'];
    }

    // the port can only be used if the host is included to ensure the url works
    if ($this->_includePort === NULL && $this->_includeHost == TRUE) {
      if (Port::isCommon($this->_urlParts['port']) == FALSE) {
        $this->_url .= ':' . $this->_urlParts['port'];
      }
    }
    elseif ($this->_includePort == TRUE && $this->_includeHost == TRUE) {
      $this->_url .= ':' . $this->_urlParts['port'];
    }

    if ($this->_includePath == TRUE) {
      if (trim($this->_urlParts['path'] != '')) {
        $this->_url .= $this->_urlParts['path'];
      }
      /**
       * If the host is included and the path is empty then if we didn't include
       * the following the visitor would be directed back to the homepage, not
       * what the programmer would want
       */
      elseif ($this->_includeHost == TRUE) {
        $this->_url .= $this->_request->path;
      }
    }

    // last but not least, build back up the query string
    if ($this->_includeQueryString == TRUE) {
      $this->_url .= '?' . $this->_buildQueryString($this->_queryStringParts, '', '&amp;');
    }

    return $this->_url;
  }

  // taken from the PHP website
  // http://www.php.net/manual/en/function.http-build-query.php#90438
  private function _buildQueryString ($data, $prefix = '', $sep = '', $key = '') {
    $ret = array();

    foreach ((array)$data as $k => $v) {
      if (is_int($k) == TRUE && $prefix != NULL) {
        $k = urlencode($prefix . $k);
      }

      if (empty($key) == FALSE || $key === 0) {
        $k = $key. '['. urlencode($k) . ']';
      }

      if (is_array($v) == TRUE || is_object($v) == TRUE) {
        array_push($ret, $this->_buildQueryString($v, '', $sep, $k));
      }
      else {
        array_push($ret, $k . '=' . urlencode($v));
      }
    }

    if (empty($sep) == TRUE) {
      $sep = ini_get('arg_separator.output');
    }

    return implode($sep, $ret);
  }
}

?>