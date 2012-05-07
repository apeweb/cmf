<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

require_once(ROOT . 'framework/network/request.php');

define('Response_Compression', NULL);
class Response_Compression {
  public function __construct () {
    self::startBuffer();
  }

  // xxx should be Response_Compression::enable() and Response_Compression::disable()
  // and use gzencode/gzdeflate to compress the output like in Kohana
  static public function startBuffer () {
    $request = new Request;
    $internetExplorerVersion = 0.0;

    if (ini_get('output_handler') !== 'ob_gzhandler' && intval(ini_get('zlib.output_compression')) === 0) {
      if (substr_count($request->acceptEncoding, 'gzip') > 0) {
        // quick escape for non-IEs
        if (strpos($request->userAgent, 'Mozilla/4.0 (compatible; MSIE ') !== 0) {
          if (ob_get_length() < 1) {
            ob_start('ob_gzhandler');
          }
        }
        // if opera
        elseif (strpos($request->userAgent, 'Opera') !== FALSE) {
          if (ob_get_length() < 1) {
            ob_start('ob_gzhandler');
          }
        }
        // if IE
        else {
          $internetExplorerVersion = floatval(substr($request->userAgent, 30));

          if ($internetExplorerVersion > 6) {
            if (ob_get_length() < 1) {
              ob_start('ob_gzhandler');
            }
          }
          elseif ($internetExplorerVersion == 6 && strpos($request->userAgent, 'SV1') === FALSE) {
            if (ob_get_length() < 1) {
              ob_start('ob_gzhandler');
            }
          }
        }
      }
    }
  }
}

?>