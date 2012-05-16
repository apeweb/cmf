<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

/**
 * Although very much complete, the request is still missing a lot of
 * methods that should really be here.
 * Need to support authentication and port on URL's
 */
class Request {
  static private $_hostValidated = FALSE;

  private function __construct () {}

  public static function getCookie ($name, $sanitiser = FILTER_DEFAULT, $flags = NULL, $xssClean = TRUE) {
    static $cache = array();

    if (isset($_COOKIE[$name]) == FALSE) {
      throw new RuntimeException("Cookie '{$name}' not found");
    }

    if (isset($cache[$name]) == TRUE) {
      return $cache[$name];
    }

    $cookie = new Cookie;
    $cookie->setName($name);
    $cookie->setValue(Filter::input($_COOKIE[$name], $sanitiser, $flags, $xssClean));
    
    $cache[$name] = $cookie;

    return $cookie;
  }

  public static function cookieExists ($name) {
    return isset($_COOKIE[$name]);
  }

  /**
   * Returns whether we are using http or https based on the connection type
   * @return string
   */
  public static function scheme () {
    $scheme = 'http';

    if (Request::isSecure() == TRUE) {
       $scheme = 'https';
    }

    return $scheme;
  }

  /**
   * Returns the username provided by the user when authenticating
   * @return string
   */
  public static function username ($sanitize = FILTER_SANITIZE_STRING, $flags = NULL) {
    $username = '';

    if (isset($_SERVER['PHP_AUTH_USER'])) {
      $username = filter_var($_SERVER['PHP_AUTH_USER'], $sanitize, $flags);
    }

    return $username;
  }

  /**
   * Returns the password provided by the user when authenticating
   * @return string
   */
  public static function password ($sanitize = FILTER_SANITIZE_STRING, $flags = NULL) {
    $password = '';

    if (isset($_SERVER['PHP_AUTH_PW'])) {
      $username = filter_var($_SERVER['PHP_AUTH_PW'], $sanitize, $flags);
    }

    return $password;
  }

  /**
   * Returns username:password based on the details supplied when authenticating
   * for use when building the URL's
   * @return string username or username:password but never password by itself
   */
  public static function authentication () {
    if (Request::username() != '' && Request::password() != '') {
      return Request::username() . ':' . Request::password();
    }
    elseif (Request::username() != '') {
      return Request::username();
    }
    else {
      return '';
    }
  }

  /**
   * The requested hostname, ie dev.apeweb.co.uk
   * @return string requested hostname
   */
  public static function host () {
    static $host = NULL;

    if ($host !== NULL) {
      return $host;
    }

    if (Environment::isCommandLine() == TRUE) {
      $cliArguments = self::getArguments();

      if (isset($cliArguments['host'])) {
        $host = $cliArguments['host'];
      }
      else {
        $host = 'localhost';
      }

      return $host;
    }

    if (self::$_hostValidated == FALSE) {
      self::validateHost();
    }

    $portPosition = strpos($_SERVER['HTTP_HOST'], ':' . $_SERVER['SERVER_PORT']);
    if ($portPosition !== FALSE) {
      $host = substr($_SERVER['HTTP_HOST'], 0, $portPosition);
    }
    else {
      $host = $_SERVER['HTTP_HOST'];
    }

    return $host;
  }

  /**
   * Returns the port number being used in the request
   * @return integer
   */
  public static function port () {
    return $_SERVER['SERVER_PORT'];
  }

  /**
   * @example /path/filename
   * @return (string) the url
   */
  public static function path () {
    static $path = NULL;

    if ($path !== NULL) {
      return $path;
    }

    /*
    if (isset($_SERVER['SCRIPT_NAME']) && $_SERVER['SCRIPT_NAME'] != '') {
      $path = $_SERVER['SCRIPT_NAME'];
    }
    else {
      $path = $_SERVER['SCRIPT_FILENAME'];
    }*/

    if (Environment::isCommandLine() == FALSE) {
      // xxx needs testing in IIS
      $requestPath = strtok(self::_request_uri(), '?');
      $basePathLength = strlen(rtrim(dirname($_SERVER['SCRIPT_NAME']), '\/'));
      $path = substr(urldecode($requestPath), $basePathLength);
      
      if ($path == basename($_SERVER['PHP_SELF'])) {
        $path = '';
      }
    }
    else {
      $path = '/' . array_shift($_SERVER['argv']);
    }

    return $path;
  }

  // Because IIS doesn't have $_SERVER['REQUEST_URI'] we ensure the same data is returned regardless of the web server
  private static function _request_uri() {
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

    return $uri;
  }

  /**
   * @example /path/ (where path would normally be /path/index.php)
   * @example /page/filename
   * @param string $filename
   * @return string path
   */
  public static function canonicalPath ($canonicaliseFilename = 'index.php') {
    if (isset($_SERVER['SCRIPT_NAME'])) {
      $path = $_SERVER['SCRIPT_NAME'];
    }
    else {
      $path = $_SERVER['SCRIPT_FILENAME'];
    }

    $path = str_replace($canonicaliseFilename, '', $path);

    return $path;
  }

  // returns $_GET['x'] or the full query string
  // saves looping around $_GET, only cleans when required for performance
  // if people then choose to use $_GET it's their own fault
  public static function queryString ($name = NULL, $sanitiser = FILTER_DEFAULT, $flags = NULL, $xssClean = TRUE) {
    if ($name !== NULL) {
      if (isset($_GET[$name])) {
        if (!is_array($_GET[$name])) {
          $value = Filter::input($_GET[$name], $sanitiser, $flags, $xssClean);
        }
        else {
          $value = Filter::input(serialize($_GET[$name]), $sanitiser, $flags, $xssClean);
        }
      }
      else {
        // return NULL so we can check if the value exists or not
        return NULL;
      }
    }
    else {
      $value = Filter::input($_SERVER['QUERY_STRING'], $sanitiser, $flags, $xssClean);
    }

    return (string) $value;
  }

  /**
   * @example scheme://host:port/path/?arg=val
   * @return (string) the url
   */
  public static function url () {
    $url = Request::scheme() . '://';

    $url .= Request::host();

    if (Request::isCommonPort() == FALSE) {
      $url .= ':' . Request::port();
    }

    $url .= Request::path();

    if (Request::queryString() != '') {
      $url .= '?' . Request::queryString();
    }

    return $url;
  }

  public static function baseUrl () {
    $url = Request::scheme() . '://';

    $url .= Request::host();

    if (Request::isCommonPort() == FALSE) {
      $url .= ':' . Request::port();
    }

    return $url;
  }

  /**
   * @example scheme://host/path/
   * @return (string) the url
   */
  public static function canonicalUrl ($canonicaliseFilename = 'index.php') {
    $url = Request::scheme() . '://';

    $url .= Request::host();

    if (Request::isCommonPort() == FALSE) {
      $url .= ':' . Request::port();
    }

    $url .= Request::canonicalPath($canonicaliseFilename);

    return $url;
  }

  /**
   * @example /path/?arg=val
   * @return (string) the url
   */
  public static function relativeUrl ($canonicaliseFilename = 'index.php') {
    $url = Request::canonicalPath($canonicaliseFilename);

    if (Request::queryString() != '') {
      $url .= '?' . Request::queryString();
    }

    return $url;
  }

  /**
   * Checks whether the connection is secure or not
   * @return boolean
   */
  public static function isSecure () {
    if (Environment::isCommandLine() == TRUE) {
			return FALSE;
		}
		elseif (empty($_SERVER['HTTPS']) == FALSE && $_SERVER['HTTPS'] == 'on') {
			return TRUE;
		}

    return FALSE;
  }

  /**
   * Returns the visitor's IP address, ie 85.172.23.19
   * @return string IP address
   */
  public static function visitorIpAddress () {
    $ipAddress = NULL;

    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) == TRUE && trim($_SERVER['HTTP_X_FORWARDED_FOR']) != '' && $_SERVER['HTTP_X_FORWARDED_FOR'] != 'unknown') {
      $ipAddress = Request::validateIpAddress($_SERVER['HTTP_X_FORWARDED_FOR']);
    }

    if ($ipAddress == NULL && isset($_SERVER['HTTP_CLIENT_IP']) == TRUE && trim($_SERVER['HTTP_CLIENT_IP']) != '' && $_SERVER['HTTP_CLIENT_IP'] != 'unknown') {
      $ipAddress = Request::validateIpAddress($_SERVER['HTTP_CLIENT_IP']);
    }

    if ($ipAddress == NULL && isset($_SERVER['REMOTE_ADDR']) == TRUE && trim($_SERVER['REMOTE_ADDR']) != '' && $_SERVER['REMOTE_ADDR'] != 'unknown') {
      $ipAddress = Request::validateIpAddress($_SERVER['REMOTE_ADDR']);
    }

    // add checks for the following:
    //HTTP_PRAGMA, HTTP_XONNECTION, HTTP_CACHE_INFO, HTTP_XPROXY, HTTP_PROXY, HTTP_PROXY_CONNECTION, HTTP_VIA, HTTP_X_COMING_FROM, HTTP_X_FORWARDED, HTTP_COMING_FROM, HTTP_FORWARDED_FOR, HTTP_FORWARDED, ZHTTP_CACHE_CONTROL

    if ($ipAddress == NULL) {
      $ipAddress = '0.0.0.0';
    }

    return $ipAddress;
  }

  public static function userAgent () {
    return $_SERVER['HTTP_USER_AGENT'];
  }

  public static function method () {
    $method = strtoupper($_SERVER['REQUEST_METHOD']);

    if ($method == 'POST' && (!isset($_POST) || (count($_POST) < 1 && Request::isUploadFileSizeOverflow() == FALSE))) {
      $method == 'GET';
    }

    return $method;
  }

  public static function acceptEncoding () {
    return $_SERVER['HTTP_ACCEPT_ENCODING'];
  }

  public static function referrer () {
    return $_SERVER['HTTP_REFERER'];
  }
  
  public static function protocol () {
    return $_SERVER['SERVER_PROTOCOL'];
  }

  public static function isCommonPort () {
    if (Environment::isCommandLine() == TRUE) {
      return NULL;
    }

    if ($_SERVER['SERVER_PORT'] == '80') {
      return TRUE;
    }
    elseif ($_SERVER['SERVER_PORT'] == '443' && self::isSecure() == TRUE) {
      return TRUE;
    }

    return FALSE;
  }

  public static function pathInfo () {
    return $_SERVER['PATH_INFO'];
  }

  public static function validateIpAddress ($ipAddress) {
    $comma = strrpos($ipAddress, ',');
    if ($comma !== FALSE) {
      $ipAddress = substr($ipAddress, $comma + 1);
    }

    if (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) == FALSE) {
      $ipAddress = '';
    }

    return $ipAddress;
  }

  public static function validateGlobals () {
    static $validated = FALSE;

	  if ($validated == FALSE && ini_get('register_globals')) {
      if (isset($_REQUEST['GLOBALS']) || isset($_FILES['GLOBALS'])) {
        // Can't carry on here as the $GLOBALS variable has be overridden
        header('HTTP/1.0 400 Bad Request');
        Log::writeEntry('Register globals attack', '$GLOBALS overridden by visitor request', Log_Type::security, Log_Level::attack, $_REQUEST);
        exit;
      }

	    $globalVariables = array_keys($GLOBALS);
	    $globalVariables = array_diff($globalVariables, array('GLOBALS', '_REQUEST', '_GET', '_POST', '_FILES', '_COOKIE', '_SERVER', '_ENV', '_SESSION'));

      // These globals are standard and should not be removed
	    foreach ($globalVariables as $name) {
	      unset($GLOBALS[$name]);
	    }

      // Warn the developer about register globals
      Log::writeEntry('PHP configuration', 'Register globals needs to be disabled', Log_Type::security, Log_Level::warning);
    }

    $validated = TRUE;
  }

  public static function validateHost () {
    if (isset($_SERVER['HTTP_HOST']) == TRUE) {
      $_SERVER['HTTP_HOST'] = strtolower($_SERVER['HTTP_HOST']);
      // xxx move regex out of here into it's own function
      if (preg_match('/^\[?(?:[a-zA-Z0-9-:\]_]+\.?)+$/', $_SERVER['HTTP_HOST']) == FALSE) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
        Log::writeEntry('Http host attack', '$_SERVER[\'HTTP_HOST\'] overridden by visitor request', Log_Type::security, Log_Level::attack, $_SERVER);
        exit;
      }
    }
    else {
      $_SERVER['HTTP_HOST'] = '';
    }

    self::$_hostValidated = TRUE;
  }
  
  public static function validateProtocol () {
    if (!isset($_SERVER['SERVER_PROTOCOL']) || ($_SERVER['SERVER_PROTOCOL'] != 'HTTP/1.0' && $_SERVER['SERVER_PROTOCOL'] != 'HTTP/1.1')) {
      $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.0';
    }
  }

  public static function validatePort () {
    if (!isset($_SERVER['SERVER_PORT']) || $_SERVER['SERVER_PORT'] < 1) {
      $_SERVER['SERVER_PORT'] = '';
    }
  }
  
  public static function validateReferrer () {
    if (!isset($_SERVER['HTTP_REFERER'])) {
      $_SERVER['HTTP_REFERER'] = '';
    }
  }

  public static function validateAcceptedEncoding () {
    if (!isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
      $_SERVER['HTTP_ACCEPT_ENCODING'] = '';
    }
  }
  
  public static function validateUserAgent () {
    if (!isset($_SERVER['HTTP_USER_AGENT'])) {
      $_SERVER['HTTP_USER_AGENT'] = '';
    }
  }

  public static function validateUrl () {
    if (!isset($_SERVER['REQUEST_URI'])) {
      $_SERVER['REQUEST_URI'] = '';
    }
  }
  
  public static function getHeaders () {
    return getallheaders();
  }

  public static function getArguments () {
    static $out = NULL; // array

    if (is_array($out) == TRUE) {
      return $out;
    }

    $out = array();

    foreach ($_SERVER['argv'] as $arg) {
      if (substr($arg,0,2) == '--') {
        $eqPos = strpos($arg, '=');
        if ($eqPos === FALSE){
          $key = substr($arg, 2);

          if (isset($out[$key]) == FALSE) {
            $out[$key] = TRUE;
          }
        }
        else {
          $key = substr($arg, 2, $eqPos - 2);
          $out[$key] = substr($arg, $eqPos + 1);
        }
      }
      elseif (substr($arg, 0, 1) == '-') {
        if (substr($arg, 2, 1) == '='){
          $key = substr($arg, 1, 1);
          $out[$key] = substr($arg, 3);
        }
        else {
          $chars = str_split(substr($arg, 1));
          foreach ($chars as $char){
            $key = $char;
            
            if (isset($out[$key]) == FALSE) {
              $out[$key] = TRUE;
            }
          }
        }
      }
      else {
        $out[] = $arg;
      }
    }
    
    return $out;
  }
}

?>