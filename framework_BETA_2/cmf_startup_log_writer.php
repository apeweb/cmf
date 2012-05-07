<?php

/**
 * Copyright (c) 2011 Ape Web Ltd.  All rights reserved.
 *
 * The use and distribution terms for this software are contained in the file
 * named license.txt, which can be found in the root of this distribution.
 * By using this software in any fashion, you are agreeing to be bound by the
 * terms of this license.
 *
 * You must not remove this notice, or any other, from this software.
 */

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Cmf_Startup_Log_Writer implements iLog {
  const logFile = 'cmf_startup_errors.tsv';

  public function write ($title, $message, $type, $level, $dump) {
    if (Cmf_Application::getPhase() !== Cmf_Application::PHASE_INITIALISE) {
      return;
    }

    if (File::isWritable(CMF_ROOT . self::logFile) == TRUE) {
      file_put_contents(CMF_ROOT . self::logFile, $this->_format($title, $message, $type, $level, $dump) . PHP_EOL, FILE_APPEND);
    }
  }

  private function _format ($title, $message, $type, $level, $dump) {
    return implode("\t", array(
      $this->_getLiteral('Log_Type', $type),
      $this->_getLiteral('Log_Level', $level),
      $title,
      $message,
      str_replace("\t", "\s", serialize($dump))
    ));
  }

  private function _getLiteral ($class, $literal) {
    try {
      return ucfirst(Reflection_Enum::getConstantName($class, $literal));
    }
    catch (Exception $ex) {
      return $literal;
    }
  }

  public function getLog () {
    return array();
  }
}

?>