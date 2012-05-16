<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Filter {
  static $magicQuotesDetected = NULL;

  #Region "shared functions"
  static function regexp ($pattern, $data) {
    return filter_var($data, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $pattern)));
  }

  static function excess ($data) {
    return trim($data);
  }

  static function asciiCtrls ($data) {
    return preg_replace('#[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+#S', '', $data);
  }

  /**
   * Kohana's xss_clean method
   */
  static function xss ($data) {
    $data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
    $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
    $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
    $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

    // Remove any attribute starting with "on" or xmlns
    $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

    // Remove javascript: and vbscript: protocols
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

    // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#is', '$1>', $data);
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#is', '$1>', $data);
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#ius', '$1>', $data);

    // Remove namespaced elements (we do not need them)
    $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

    do {
      // Remove really unwanted tags
      $old_data = $data;
      $data = preg_replace('#<[\x00-\x20]*/*[\x00-\x20]*+(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+#i', '', $data);
    }
    while ($old_data !== $data);

    return $data;
  }

  static function input ($value, $sanitiser = FILTER_DEFAULT, $flags = NULL, $xssClean = TRUE) {
    if (is_array($value) == FALSE) {
      if ($sanitiser == FALSE) {
        $sanitiser = FILTER_UNSAFE_RAW;
      }

      // checks to see if magic quotes is enabled
      if (self::$magicQuotesDetected === NULL) {
        self::$magicQuotesDetected = get_magic_quotes_gpc();
      }

      // filter the var using the standard PHP filter
      $value = strval(filter_var($value, $sanitiser, $flags));

      // xss clean
      if ($xssClean == TRUE) {
        $value = self::xss($value);
      }

      // strips slashes if enabled
      if (self::$magicQuotesDetected == TRUE) {
        $value = stripslashes($value);
      }

      // remove excess whitespace
      $value = self::excess($value);
    }
    else {
      foreach ($value as $k => $v) {
        self::input($v, $sanitiser, $flags, $xssClean);
      }
    }

    return $value;
  }

  static public function userName ($userName) {
    Assert::isString($userName);
    return trim($userName);
  }
  #End Region
}

?>