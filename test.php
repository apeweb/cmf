<?php

echo $_SERVER['PHP_SELF'];
echo "\r\n";
exit;

class Request {
public static function path () {
    if (isset($_SERVER['REQUEST_URI'])) {
      $uri = $_SERVER['REQUEST_URI'];
    }
    else {
      if (isset($_SERVER['argv'])) {
        $uri = $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['argv'][0];
      }
      elseif (isset($_SERVER['QUERY_STRING'])) {
        $uri = $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING'];
      }
      else {
        $uri = $_SERVER['SCRIPT_NAME'];
      }
    }

    $uri = '/' . ltrim($uri, '/');

    $requestPath = strtok($uri, '?');
    $basePathLength = strlen(rtrim(dirname($_SERVER['SCRIPT_NAME']), '\/'));
    $path = substr(urldecode($requestPath), $basePathLength);
    if ($path == basename($_SERVER['PHP_SELF'])) {
      $path = '';
    }

return '/products/view10';
  }
}

// regex doesn't allow admin
//var_dump(preg_match('#^(?!/admin/)[^/.,;?\n]+/?$#uD', '/products/add/'));
//exit;

var_dump(preg_match('#^/admin/?(?:(?P<action>[^/.,;?\n]+/?))?$#uD', '/admin/'));
exit;


class Cmf_Route {
  private $_name = '';
  private $_url = '';
  private $_defaultValues = array();
  private $_masks = array();

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
    $this->_url = $url;
  }

  public function getUrl () {
    return $this->_url;
  }

  // returns #^[^/.,;?\n]+/[^/.,;?\n]+/\d+$#uD
  public function getUrlMask () {
    $expression = preg_replace('#[.\\+*?[^\\]$<>=!|]#', '\\\\$0', $this->_url);

    foreach ($this->_defaultValues as $name => $mask) {
      $expression = str_replace('{' . $name . '}', '(?:{' . $name . '})?', $expression);
    }

    foreach ($this->_masks as $name => $mask) {
      $expression = str_replace('{' . $name . '}', '(?P<' . $name . '>' . $mask . ')', $expression);
    }

    $expression = preg_replace('#{([a-z0-9_-]+)}#i', '(?P<$1>[^/.,;?\n]+)', $expression);
    $expression = preg_replace('#[{}]#', '\\\\$0', $expression);

    $expression = '#^'. $expression .'$#uD';

    return $expression;
  }

  // returns array('controller' => 'cmf_frontpage', 'action' => 'weclome', 'id' => 0);
  public function getArguments () {
    $arguments = array();

    if (preg_match($this->getUrlMask(), Request::path(), $matches) == FALSE) {
      return $arguments;
    }

    foreach ($matches as $name => $value) {
      if (is_int($name) == TRUE) {
        // Skip all unnamed keys
        continue;
      }

      // Set the value for all matched keys
      $arguments[$name] = $value;
    }

    foreach ($this->_defaultValues as $name => $value) {
      if (isset($arguments[$name]) == FALSE || $arguments[$name] === '') {
        // Set default values for any key that was not matched
        $arguments[$name] = $value;
      }
    }

    return $arguments;
  }
}

$masks = array('id' => '\d+');
$defaultValues = array('id' => 10);
$route = new Cmf_Route('test', '/{controller}/{action}{id}', $defaultValues, $masks);

echo '<pre>';
print_r($route->getArguments());

?>