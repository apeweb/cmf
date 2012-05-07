<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Network_Error {
  const Page_Not_Found = 404;
  const Forbidden_Access = 403;

  public static function log ($httpStatusCode, $dump = '') {
    $summary = '';

    switch ($httpStatusCode) {
      case 404:
        $summary = 'Page not found';
        self::_logAdditional404Information();
        break;

      case 403:
        $summary = 'Forbidden access';
        break;

      default:
        throw new Exception ('Unsupported status code');
    }

    Event_Log::add(Event_Log_Type::Http, $httpStatusCode, $summary, $dump, Event_Log_Level::Error);
  }

  // will be used to store 404s in another table to allow for redirects as
  // like in PA
  private static function _logAdditional404Information () {

  }
}

?>