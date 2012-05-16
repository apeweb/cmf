<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Cmf_Page_Cache {
  static public function load () {
    if (Config::getValue('cache', 'page', 'enabled') == TRUE) {
      // xxx finish
    }
  }

  static public function savePageToCache () {
    // xxx register with Cmf_Application_Event::terminate
  }
}

?>