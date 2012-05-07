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

/**
 * Provides security information relating to a path including user permissions and ownership
 * information
 */
final class File_System_Security {
  protected $path = '';

  public function __construct ($path) {
    if (trim($path) == '') {
      throw new InvalidArgumentException('Path cannot be empty');
    }

    $path = Path::getAbsolutePath($path);

    if (File::exists($path) == FALSE && Dir::exists($path)) {
      if (Path::isDirectorySeparator(substr($path, -1, 1)) == TRUE) {
        throw new Directory_Not_Found_Exception('One or more parts of the path could not be found');
      }
      else {
        throw new File_Not_Found_Exception('One or more parts of the path could not be found');
      }
    }

    $this->_path = $path;
  }

  public function isReadable () {
    return is_readable($this->_path);
  }

  public function isWritable () {
    return is_writable($this->_path);
  }

  public function isExecutable () {
    if (is_file($this->_path) == TRUE) {
      return is_writable($this->_path);
    }
    return FALSE;
  }

  public function getOwner () {
    return fileowner($this->_path);
  }

  public function setOwner ($username) {
    return chown($this->_path, $username);
  }

  // xxx in future this will use an enum
  public function setMode ($mode) {
    return chmod($this->_path, $mode);
  }
}

?>