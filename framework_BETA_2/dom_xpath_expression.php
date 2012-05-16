<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

// xxx fix the class name
class DOM_XPath_Expression {
  private $_variables = array();

  public function bind ($name, $value) {
    // xxx move this regex out of here
    if (preg_match('#^\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$#', $name) == FALSE) {
      throw new DOM_XPath_Exception("Illegal characters in variable name");
    }

    $this->_variables[$name] = $value;
  }

  public function compile ($query) {
    if (preg_match_all('#\{?\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\}?#', $query, $matches, PREG_OFFSET_CAPTURE) == TRUE) {
      $replace = $matches[0];
      $newQuery = '';

      if (empty($replace) == FALSE) {
        $offset = 0;
        $newQuery = $query;

        foreach ($replace as $group) {
          $adjustOffset = 0;

          if (substr($group[0], 0, 1) == '{' && substr($group[0], -1, 1) =='}') {
            $group[0] = substr($group[0], 1, strlen($group[0]) - 2);
            $adjustOffset = 2;
          }

          if (isset($this->_variables[$group[0]])) {
            $currentOffset = $group[1] + $offset;
            $newQuery = substr($newQuery, 0, $currentOffset) . $this->_variables[$group[0]] . substr($newQuery, $currentOffset + strlen($group[0]) + $adjustOffset);
            $offset += strlen($this->_variables[$group[0]]) - strlen($group[0]) - $adjustOffset;
          }
        }
      }

      return $newQuery;
    }
    else {
      return $query;
    }
  }
}

//$xpath = new DOMXPathExpression;
//$xpath->bind('$g', 'xxx');
//$xpath->bind('$group', 'whatever');
//$xpath->bind('$phrase', 'myphrase');
//$xpath->bind('$locale', 745);
//
//echo $xpath->compile('/literals/$group/\{$g}_{$phrase}') . '<br />';
//echo $xpath->compile('/literals/$group/$locale/$phrase/value[@locale="$locale"]') . '<br />';

?>