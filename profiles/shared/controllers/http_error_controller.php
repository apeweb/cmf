<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Http_Error_Controller extends Controller {
  public static function pageNotFound () {
    Cmf_Template_Engine::setMasterTemplate(CMF_ROOT . 'profiles/shared/views/page_not_found' . PHP_EXT);
  }
}

?>