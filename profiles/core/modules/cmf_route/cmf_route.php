<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Cmf_Route {
  private $_name = '';
  private $_url = '';
  private $_defaultValues = array();
  private $_masks = array();
  private $_arguments = NULL;
  private $_paths = array();

  // $name = 'Default'
  // $url = '{controller}/{action}/{id}'
  // $defaultValues['argument_name'] = 'default_value';
  // $defaultValues['controller'] = 'cmf_frontpage';
  // $masks['argument_name'] = 'mask';
  // $masks['page_id'] = '\d+';

  public function __construct ($name = '', $url = '', $defaultValues = array(), $masks = array()) {
    $this->_name = $name;
    $this->_url = $url;
    $this->_defaultValues = $defaultValues;
    $this->_masks = $masks;
  }

  public function setName ($name) {
    Assert::isString($name);
    $this->_name = $name;
  }

  public function getName () {
    return $this->_name;
  }

  public function setUrl ($url) {
    Assert::isString($url);

    // This will break the regular expressions, so don't allow it
    if (strpos($url, '}{') !== FALSE) {
      throw new Argument_Exception("Unsupported url '{$url}' set");
    }

    $this->_url = $url;
  }

  public function getUrl ($arguments = array()) {
    $url = $this->_url;

    foreach ($arguments as $argumentName => $argumentValue) {
      $url = str_replace('{' . $argumentName. '}', $argumentValue, $url);
    }

    foreach ($this->_defaultValues as $argumentName => $argumentValue) {
      $url = str_replace('{' . $argumentName. '}', $argumentValue, $url);
    }

    return $url;
  }

  /**
   * By setting a default value, the system considers that part of the URL optional
   * So /product/view/10 (view and 10 are both optional) will make /product/10 product the controller and 10 the action
   * which obviously could cause an issue so it is worth making sure you are aware of this
   */
  public function setDefaultValue ($name, $value) {
    // xxx finish
  }

  public function getDefaultValue ($name) {
    // xxx finish
  }

  public function removeDefaultValue ($name) {
    // xxx finish
  }

  // Parses the route and returns the value
  public function getArgumentValue ($name) {
    if ($this->_arguments === NULL) {
      $this->getArguments();
    }

    if (array_key_exists($name, $this->_arguments) == FALSE) {
      throw new Argument_Exception("Argument '{$name}' does not exist");
    }

    return $this->_arguments[$name];
  }

  // Parses the route and overrides a value
  public function setNewArgumentValue ($name, $value) {
    if ($this->_arguments === NULL) {
      $this->getArguments();
    }

    if (array_key_exists($name, $this->_arguments) == FALSE) {
      throw new Argument_Exception("Argument '{$name}' does not exist");
    }

    $this->_arguments[$name] = $value;
  }

  public function getAllDefaultValues () {
    return $this->_defaultValues;
  }

  public function setMask ($name, $value) {
    // xxx finish
  }

  public function getMask ($name) {
    // xxx finish
  }

  public function removeMask ($name) {
    // xxx finish
  }

  public function getAllMasks () {
    return $this->_masks;
  }

  // returns #^[^/.,;?\n]+/[^/.,;?\n]+/\d+$#uD
  public function getUrlMask () {
    $expression = preg_replace('#[.\\+*?[^\\]$<>=!|]#', '\\\\$0', $this->_url);

    foreach ($this->_defaultValues as $name => $mask) {
      //$expression = str_replace('{' . $name . '}', '(?:{' . $name . '})?', $expression);
      $expression = str_replace('/{' . $name . '}', '/?(?:{' . $name . '})?', $expression);
    }

    foreach ($this->_masks as $name => $mask) {
      if ($mask == '') {
        $mask = '[^/.,;?\n]+/?';
      }
      $expression = str_replace('{' . $name . '}', '(?P<' . $name . '>' . $mask . ')', $expression);
    }

    $expression = preg_replace('#{([a-z0-9_-]+)}#i', '(?P<$1>[^/.,;?\n]+)', $expression);
    // breaks x{0,1} regex
    //$expression = preg_replace('#[{}]#', '\\\\$0', $expression);

    $expression = '#^'. $expression .'$#uD';

    return $expression;
  }

  // returns array('controller' => 'cmf_frontpage', 'action' => 'weclome', 'id' => 0);
  public function getArguments ($reset = FALSE) {
    if ($reset == FALSE && is_array($this->_arguments) == TRUE) {
      return $this->_arguments;
    }

    if (preg_match($this->getUrlMask(), Request::path(), $matches) == FALSE) {
      $this->_arguments = array();
      return $this->_arguments;
    }

    foreach ($matches as $name => $value) {
      if (is_int($name) == TRUE) {
        // Skip all unnamed keys
        continue;
      }

      // Set the value for all matched keys
      $this->_arguments[$name] = $value;
    }

    foreach ($this->_defaultValues as $name => $value) {
      if (isset($this->_arguments[$name]) == FALSE || $this->_arguments[$name] === '') {
        // Set default values for any key that was not matched
        $this->_arguments[$name] = $value;
      }
    }

    return $this->_arguments;
  }

  public function setPathAccessDenied ($path) {
    Assert::isString($path);
    $this->_paths[$path] = FALSE;
  }

  public function unsetPathAccessDenied ($path) {
    Assert::isString($path);
    if (array_key_exists($path, $this->_paths) == TRUE && $this->_paths[$path] === FALSE) {
      unset($this->_paths[$path]);
    }
  }

  public function getPathsDenied () {
    return array_keys($this->_paths, FALSE, TRUE);
  }

  public function setPathAccessAllowed ($path) {
    Assert::isString($path);
    $this->_paths[$path] = TRUE;
  }

  public function unsetPathAccessAllowed ($path) {
    Assert::isString($path);
    if (array_key_exists($path, $this->_paths) == TRUE && $this->_paths[$path] === TRUE) {
      unset($this->_paths[$path]);
    }
  }

  public function getPathsAllowed () {
    return array_keys($this->_paths, TRUE, TRUE);
  }

  public function getAllPaths () {
    return $this->_paths;
  }
}

?>