<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Cmf_Response_Buffer_Compression {
  static private $_initialCompression = NULL;
  static private $_outputCompressed = FALSE;
  static private $_runtimeCompressionEnabled = FALSE;

  static public function install () {
    Config::setValue(CMF_REGISTRY, 'page', 'response', 'compression', FALSE);
  }

  static public function initialise () {
    self::$_runtimeCompressionEnabled = Config::getValue('page', 'response', 'compression');
    Event_Dispatcher::attachObserver(Response_Buffer_Event_Helper_Event::flush, __CLASS__ . '::flushEventHandler');
  }

  static public function enableCompression () {
    // Content is already be compressed so make sure it isn't compressed twice
    self::$_initialCompression = ini_set('zlib.output_compression', '0');
    Response_Buffer::setHeader('Content-Encoding', 'gzip');
    self::$_runtimeCompressionEnabled = TRUE;
  }

  static public function disableCompression () {
    Response_Buffer::deleteHeader('Content-Encoding', 'gzip');

    // Reset the compression back to what it was to avoid any output issues
    if (self::$_initialCompression !== NULL && ini_get('zlib.output_compression') === '0') {
      ini_set('zlib.output_compression', self::$_initialCompression);
    }

    self::$_runtimeCompressionEnabled = FALSE;
  }

  static public function flushEventHandler (Event_Data $eventData) {
    if (isset($eventData->buffer) == FALSE || $eventData->buffer == '') {
      return;
    }

    if (self::$_runtimeCompressionEnabled == TRUE && strpos(Request::acceptEncoding(), 'gzip') !== FALSE) {
      self::enableCompression();
      $eventData->buffer = gzencode($eventData->buffer, 9, FORCE_GZIP);
      self::$_outputCompressed = TRUE;
    }
    else {
      self::disableCompression();
    }
  }

  static public function outputCompressed () {
    return self::$_outputCompressed;
  }
}

?>