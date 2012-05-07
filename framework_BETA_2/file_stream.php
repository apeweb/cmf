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

//File_System_Exception;
//File_Not_Found_Exception;

// xxx finish adding support for file access and update file mode

class File_Stream implements iStream {
  private $_buffer = NULL;
  private $_filename = '';
  private $_mode = File_Mode::open;
  private $_access = File_Access::read;
  private $_lockMode = NULL;

  public function __construct ($path, $mode = File_Mode::openOrCreate, $access = File_Access::readWrite) {
    if (trim($path) == '') {
      throw new InvalidArgumentException('Path cannot be empty');
    }
    elseif ($path == '.' || $path == '..') {
      throw new InvalidArgumentException;
    }
    elseif (Path::isDirectorySeparator(substr($path, -1, 1)) == TRUE) {
      throw new File_System_Exception('Filename, directory name, or volume label syntax is incorrect');
    }

    if (Reflection_Enum::hasConstant(File_Mode, $mode) == FALSE) {
      if (is_int($mode) == TRUE) {
        throw new OutOfRangeException('Enum value is out of legal range');
      }
      else {
        throw new Invalid_Cast_Exception($mode, Integer);
      }
    }

    if (Reflection_Enum::hasConstant(File_Access, $access) == FALSE) {
      if (is_int($access) == TRUE) {
        throw new OutOfRangeException('Enum value is out of legal range');
      }
      else {
        throw new Invalid_Cast_Exception($access, Integer);
      }
    }

    $path = Path::getAbsolutePath($path);

    switch ($mode) {
      case File_Mode::open:
      case File_Mode::truncate:
        if (is_file($path) == FALSE) {
          throw new File_Not_Found_Exception;
        }
      break;

      case File_Mode::createNew:
        if (file_exists($path) == TRUE) {
          throw new File_System_Exception('Path already exists');
        }
      break;
    }

    // xxx check if File_Mode, etc. are correct

    // Do we really need the path and filename?
    $this->_path = Path::getFullPath($path);
    $this->_filename = Filename::getFileName($path);
    $this->_mode = $mode;

    /**
     * Determines the way the file is opened, will either open or create the
     * file and set the pointer at the start or end, truncating if needed
     */
    switch ($mode) {
      /**
       * open/create file or throw exception
       * seek pointer to end of file
       */
      case File_Mode::append:

      break;

      /**
       * open/create file or throw exception
       * if the file exists truncate, otherwise create new file
       */
      case File_Mode::create:

      break;

      /**
       * create file or throw exception
       * seek pointer to start
       */
      case File_Mode::createNew:

      break;

      /**
       * open file or throw exception
       * seek pointer to start
       */
      case File_Mode::open:

      break;

      /**
       * open file or throw exception
       * seek pointer to end of file
       */
      case File_Mode::openOrCreate:

      break;

      /**
       * open file or throw exception
       * truncate, then seek pointer to start
       */
      case File_Mode::truncate:

      break;
    }

    /**
     * Determines whether the file can be read or written or both to depending
     * on which method of access is required
     */
    switch ($access) {
      case File_Access::read:
        if ($this->isReadable() == FALSE) {
          throw new File_Not_Readable_Exception($filename);
        }
      break;

      case File_Access::readWrite:
        if ($this->isReadable() == FALSE) {
          throw new File_Not_Readable_Exception($filename);
        }

        if ($this->isWritable() == FALSE) {
          throw new File_Not_Writable_Exception($filename);
        }
      break;

      case File_Access::write:
        if ($this->isWritable() == FALSE) {
          throw new File_Not_Writable_Exception($filename);
        }
      break;
    }
  }

  //public static function open ($filename, $mode = File_Mode::read, $access = File_Access::open, $folder = '') {
  public static function open ($path, $mode = File_Mode::openOrCreate, $access = File_Access::readWrite) {
    return new File_Stream($path, $mode, $access);
  }

  protected function _isFile ($filename) {
    return is_file($filename);
  }

  public function read () {
    if ($this->_isReadable($this->_path . $this->_filename) == TRUE) {
      return file_get_contents($this->_path . $this->_filename);
    }
    else {
      throw new File_Not_Readable_Exception($filename);
    }
  }

  public function write ($content) {
    if (is_scalar($content) == FALSE) {
      throw new InvalidArgumentException("Scalar data type expected");
    }

    if ($this->_mode != File_Access::write && $this->_mode != File_Access::append) {
      return FALSE;
    }

    if (is_array($content)) {
      $content = implode('', $content);
    }

    $this->_buffer .= $content;

    return TRUE;
  }

  protected function _isReadable ($filename) {
    if (file_get_contents($filename, FALSE, NULL, 0, 1) === TRUE) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  public function isReadable () {
    return $this->_isReadable($this->_path . $this->_filename);
  }

  protected function _isWritable ($filename, $mode = 0) {
    $writable = FALSE;

    if ($mode == 0) {
      $mode = $this->_mode;
    }

    if (($mode == File_Access::write || $mode == File_Access::append) == FALSE) {
      return FALSE;
    }

    $file = Filename::file($filename);
    $path = Filename::path($filename);

    if (trim($path) == '') {
      $path = realpath($path);
    }

    if ($this->_isFile($filename) == TRUE) {
      if (is_writable($filename) == FALSE) {
        $writable = FALSE;
      }
      else {
        $writable = TRUE;
      }
    }
    elseif ($this->_isDir($path) == TRUE) {
      if (is_writable($path) == FALSE) {
        $writable = FALSE;
      }
      else {
        $errorReportingOriginalValue = error_reporting();
        error_reporting(0);
        file_put_contents($filename, '');
         error_reporting($errorReportingOriginalValue);

        if ($this->_isFile($filename) == TRUE) {
          unlink($filename);
          $writable = TRUE;
        }
        else {
          $writable = FALSE;
        }
      }
    }
    else {
      $writable = FALSE;
    }

    return $writable;
  }

  protected function _isDir ($filename) {
    return is_dir($filename);
  }

  public function isWritable () {
    return $this->_isWritable($this->_path . $this->_filename);
  }

  /**
   * This method will only check for locks but will not lock the file itself
   */
  // xxx carry on testing from here (specifically the locking)
  public function save ($timeout = 5, $backup = '') {
    $fileHandle = NULL;
    $bytesWritten = 0;
    $bufferLength = 0;

    // Check for lock, keep checking for lock until lock released or timeout reached
    $timeStart = microtime(TRUE);
    while ($this->_lockMode == NULL && $this->locked() == TRUE) {
      $totalTime = microtime(TRUE) - $timeStart;
      if ($totalTime > $timeout) {
        throw new File_Locked_Exception($this->_path . $this->_filename);
      }
    }

    if ($this->_mode == File_Access::append) {
      if ($this->_buffer == '') {
        return;
      }

      // Append the buffer to the current file contents
      $this->_buffer = $this->read() . $this->_buffer;
    }
    elseif ($this->_mode != File_Access::write) {
      throw new File_Mode_Exception($this->_mode);
    }

    // We don't want the user exiting causing temporary files left all over the place
    $ignoreUserAbortOriginalValue = ignore_user_abort();
    $errorReportingOriginalValue = error_reporting();
    ignore_user_abort(TRUE);
    error_reporting(0);

    // Write to temp file first to avoid file locking race condition
    $tmpFile = $this->_path . '$~' . $this->_filename;

    $fileHandle = fopen($tmpFile, 'wb');

    if ($fileHandle === FALSE) {
      throw new File_Write_Failed_Exception($tmpFile);
    }

    $bufferLength = strlen($this->_buffer);
    $bytesWritten = fwrite($fileHandle, $this->_buffer);

    if ($bytesWritten === FALSE || $bytesWritten < $bufferLength) {
      throw new File_Write_Failed_Exception($tmpFile);
    }

    fclose($fileHandle);

    // If needed, make a backup of the original file first
    if (trim($backup) != '') {
      if (DIRECTORY_SEPARATOR == '\\' || $result = rename($this->_path . $this->_filename, $this->_path . $this->_filename . '.bak')) {
        unlink($this->_path . $this->_filename . '.bak');
        $result = rename($this->_path . $this->_filename, $this->_path . $this->_filename . '.bak');
      }

      if ($result == FALSE) {
        ignore_user_abort($ignoreUserAbortOriginalValue);
        error_reporting($errorReportingOriginalValue);
        throw new File_Write_Failed_Exception($backup);
      }
    }

    // Then replace the original file with the temp file
    // "if windows" check must be first, then check to see if rename failed for non-windows platforms
    if (DIRECTORY_SEPARATOR == '\\' || $result = rename($tmpFile, $this->_path . $this->_filename) == FALSE) {
      unlink($this->_path . $this->_filename);
      $result = rename($tmpFile, $this->_path . $this->_filename);
    }

    ignore_user_abort($ignoreUserAbortOriginalValue);
    error_reporting($errorReportingOriginalValue);

    if ($result == FALSE) {
      throw new File_Write_Failed_Exception($this->_path . $this->_filename);
    }
  }

  /**
   * Locks a file within the framework, a directory is used instead of a file to
   * indicate a lock because the mkdir function fails to create a directory if
   * it already exists.
   */
  public function lock ($timeout = 5) {
    $locked = FALSE;

    if ($this->_isDir($this->_path . '~$' . $this->_filename) == TRUE || $this->locked()) {
      return FALSE;
    }

    $timeStart = microtime(TRUE);

    $errorReportingOriginalValue = error_reporting();
    error_reporting(0);

    while ($locked == FALSE) {
      $totalTime = microtime(TRUE) - $timeStart;
      if ($totalTime > $timeout) {
        break;
      }

      if (mkdir($this->_path . '~$' . $this->_filename, 0777) == TRUE) {
        $this->_locked = TRUE;
        $locked = TRUE;
        break;
      }
      else {
        sleep(1);
      }
    }

    error_reporting($errorReportingOriginalValue);

    return $locked;
  }

  public function unlock ($force = FALSE) {
    $unlocked = FALSE;

    if ($this->_locked == TRUE || $force == TRUE) {
      $errorReportingOriginalValue = error_reporting();
      error_reporting(0);
      $unlocked = rmdir($this->_path . '~$' . $this->_filename);
      error_reporting($errorReportingOriginalValue);
    }

    return $unlocked;
  }

  public function locked () {
    if ($this->_locked == TRUE || $this->_isDir($this->_path . '~$' . $this->_filename) == TRUE) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  // Closing the file won't unlock the file, this is for the programmer to ensure
  public function close () {
    $this->_buffer = NULL;
  }
}

// xxx 1 taken from open method
    /** do this somewhere else, for instance File_System::find('*.php', 'views', 1) (1 = limit number of returned files)
                                                                   // folder, glob search, number of files to return
    if (trim($folder) != '') {
      $ext = substr(strrchr($filename, '.'), 1);
      // Remove the extension so we are not searching for foo.php.php when we should be looking for foo.php
      if ($ext != '') {
        $filename = substr($filename, 0, strlen($filename) - strlen($ext) - 1);
      }
      $files_found = File_System::find($filename, $folder, $ext);

      if (count($files_found) > 0) {
        $filename = $files_found[0];
      }
    }**/

?>