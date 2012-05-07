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

// xxx need some sort of filename validation...

// xxx windows also won't allow a filename to consist of just periods (ie ....) if Path::pathValidation is enabled

class File {
  protected function __construct () {}

  static public function exists ($path) {
    Assert::isString($path);

    $path = Path::normalisePath($path);

    if ($path == '') {
      throw new InvalidArgumentException("Path cannot be empty");
    }
    elseif ($path == '.' || $path == '..') {
      throw new InvalidArgumentException;
    }

    $path = Path::getAbsolutePath($path);

    $lastError = error_get_last();

    $fileExists = @is_file($path);

    if ($lastError !== error_get_last()) {
      throw new Unauthorised_Access_Exception("Access to path '" . $path . "' is denied"); // Handles open_basedir restrictions
    }

    return $fileExists;
  }
  
  static public function isWritable ($path) {
    Assert::isString($path);

    $path = Path::normalisePath($path);

    if ($path == '') {
      throw new InvalidArgumentException("Path cannot be empty");
    }
    elseif ($path == '.' || $path == '..') {
      throw new InvalidArgumentException;
    }

    $path = Path::getAbsolutePath($path);

    try {
      $log = @fopen($path, 'a');
      if ($log == FALSE) {
        $writable = FALSE;
      }
      else {
        @fclose($log);
        $writable = TRUE;
      }

      return $writable;
    }
    catch (Exception $ex) {
      return FALSE;
    }
  }

  public function copy ($sourcePath, $destinationPath) {
    // xxx assert

    if (trim($sourcePath) == '') {
      throw new InvalidArgumentException('Source path cannot be empty');
    }
    elseif (trim($destinationPath) == '') {
      throw new InvalidArgumentException('Destination path cannot be empty');
    }

    if ($sourcePath == '.' || $sourcePath == '..') {
      throw new InvalidArgumentException;
    }
    elseif ($destinationPath == '.' || $destinationPath == '..') {
      throw new InvalidArgumentException;
    }

    if (Path::isDirectorySeparator(substr($sourcePath, -1, 1)) == TRUE) {
      throw new File_System_Exception('Source filename, directory name, or volume label syntax is incorrect');
    }
    elseif (Path::isDirectorySeparator(substr($destinationPath, -1, 1)) == TRUE) {
      throw new File_System_Exception('Destination filename, directory name, or volume label syntax is incorrect');
    }

    $sourcePath = Path::getAbsolutePath($sourcePath);
    $destinationPath = Path::getAbsolutePath($destinationPath);

    if ($sourcePath == $destinationPath) {
      throw new InvalidArgumentException('Source and destination path must be different');
    }

    if (is_file($sourcePath) == FALSE) {
      throw new Directory_Not_Found_Exception('One or more parts of the path could not be found');
    }

   if (file_exists($destinationPath) == TRUE) {
      throw new File_System_Exception('Destination already exists');
    }

    return copy($sourcePath, $destinationPath);
  }

  // xxx default changemode (chmod) should be 0666
  public function create ($path, $mode = File_Mode::createNew, $access = File_Access::readWrite) {
    // xxx assert

    if (trim($path) == '') {
      throw new InvalidArgumentException('Path cannot be empty');
    }
    elseif ($path == '.' || $path == '..') {
      throw new InvalidArgumentException;
    }
    elseif (Path::isDirectorySeparator(substr($path, -1, 1)) == TRUE) {
      throw new File_System_Exception('Filename, directory name, or volume label syntax is incorrect');
    }

    $path = Path::getAbsolutePath($path);

    if (is_dir($path) == TRUE) {
      throw new Unauthorised_Access_Exception;
    }

    return new File_Stream($path, $mode, $access);
  }

  /**
   * Provides basic file encryption to allow files to be encrypted with a password
   */
  public function encrypt ($path, $key) {
    // xxx assert

    if (trim($path) == '') {
      throw new InvalidArgumentException('Path cannot be empty');
    }
    elseif ($path == '.' || $path == '..') {
      throw new InvalidArgumentException;
    }
    elseif (Path::isDirectorySeparator(substr($path, -1, 1)) == TRUE) {
      throw new File_System_Exception('Filename, directory name, or volume label syntax is incorrect');
    }

    $path = Path::getAbsolutePath($path);

    if (is_file($path) == FALSE) {
      throw new File_Not_Found_Exception;
    }

    $contents = file_get_contents($path);
    $contents = Encryption::encrypt($contents, $key);

    if (file_put_contents($path, $contents) !== FALSE) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Allows encrypted files to be decrypted
   */
  public function decrypt ($path, $key) {
    // xxx assert

    if (trim($path) == '') {
      throw new InvalidArgumentException('Path cannot be empty');
    }
    elseif ($path == '.' || $path == '..') {
      throw new InvalidArgumentException;
    }
    elseif (Path::isDirectorySeparator(substr($path, -1, 1)) == TRUE) {
      throw new File_System_Exception('Filename, directory name, or volume label syntax is incorrect');
    }

    $path = Path::getAbsolutePath($path);

    if (is_file($path) == FALSE) {
      throw new File_Not_Found_Exception;
    }

    $contents = file_get_contents($path);
    $contents = Encryption::decrypt($contents, $key);

    if (file_put_contents($path, $contents) !== FALSE) {
      return TRUE;
    }

    return FALSE;
  }

  public static function delete ($path) {
    // xxx assert

    if (trim($path) == '') {
      throw new InvalidArgumentException('Path cannot be empty');
    }
    elseif ($path == '.' || $path == '..') {
      throw new InvalidArgumentException;
    }
    elseif (Path::isDirectorySeparator(substr($path, -1, 1)) == TRUE) {
      throw new File_System_Exception('Filename, directory name, or volume label syntax is incorrect');
    }

    $path = Path::getAbsolutePath($path);

    if (is_file($path) == FALSE) {
      throw new File_Not_Found_Exception;
    }

    unlink($path);

    if (is_file($path) == FALSE) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  // alias
  public function rename ($sourcePath, $destinationPath) {
    File::move($sourcePath, $destinationPath);
  }

  public function move ($sourcePath, $destinationPath) {
    // xxx assert

    $moved = FALSE;

    if (trim($sourcePath) == '') {
      throw new InvalidArgumentException();
    }
    elseif (trim($destinationPath) == '') {
      throw new InvalidArgumentException();
    }

    if ($sourcePath == '.' || $sourcePath == '..') {
      throw new InvalidArgumentException;
    }
    elseif ($destinationPath == '.' || $destinationPath == '..') {
      throw new InvalidArgumentException;
    }

    if (Path::isDirectorySeparator(substr($sourcePath, -1, 1)) == TRUE) {
      throw new File_System_Exception('Filename, directory name, or volume label syntax is incorrect');
    }
    elseif (Path::isDirectorySeparator(substr($destinationPath, -1, 1)) == TRUE) {
      throw new File_System_Exception('Filename, directory name, or volume label syntax is incorrect');
    }

    $sourcePath = Path::getAbsolutePath($sourcePath);
    $destinationPath = Path::getAbsolutePath($destinationPath);

    if (is_file($sourcePath) == FALSE) {
      throw new File_Not_Found_Exception('Source file not found');
    }

    if (file_exists($destinationPath) == TRUE) {
      throw new File_System_Exception('Destination already exists');
    }

    if (rename($sourcePath, $destinationPath) == FALSE) {
      if (copy($sourcePath, $destinationPath) == TRUE) {
        unlink($sourcePath);
        $moved = TRUE;
      }
      else {
        $moved = FALSE;
      }
    }
    else {
      $moved = TRUE;
    }

    if ($moved == TRUE && is_file($sourcePath) == FALSE && is_file($destinationPath) == TRUE) {
      return TRUE;
    }

    return FALSE;
  }

  public function replace ($sourcePath, $destinationPath, $backupPath = '') {
    // xxx assert

    $moved = FALSE;

    if (trim($sourcePath) == '') {
      throw new InvalidArgumentException();
    }
    elseif (trim($destinationPath) == '') {
      throw new InvalidArgumentException();
    }

    if ($sourcePath == '.' || $sourcePath == '..') {
      throw new InvalidArgumentException;
    }
    elseif ($destinationPath == '.' || $destinationPath == '..') {
      throw new InvalidArgumentException;
    }

    if (Path::isDirectorySeparator(substr($sourcePath, -1, 1)) == TRUE) {
      throw new File_System_Exception('Filename, directory name, or volume label syntax is incorrect');
    }
    elseif (Path::isDirectorySeparator(substr($destinationPath, -1, 1)) == TRUE) {
      throw new File_System_Exception('Filename, directory name, or volume label syntax is incorrect');
    }

    $sourcePath = Path::getAbsolutePath($sourcePath);
    $destinationPath = Path::getAbsolutePath($destinationPath);

    if (is_file($sourcePath) == FALSE) {
      throw new File_Not_Found_Exception('Source file not found');
    }

    if (file_exists($destinationPath) == TRUE) {
      throw new File_System_Exception('Destination already exists');
    }

    if ($backupPath != '') {
      if ($backupPath == '.' || $backupPath == '..') {
        throw new InvalidArgumentException;
      }

      $backupPath = Path::getAbsolutePath($backupPath);

      if ($backupPath == $sourcePath) {
        throw new InvalidArgumentException('Backup path must be different to source path');
      }
      elseif ($backupPath == $destinationPath) {
        throw new InvalidArgumentException('Backup path must be different to destination path');
      }

      if (file_exists($backupPath) == TRUE) {
        throw new File_System_Exception('Backup destination already exists');
      }
      elseif (Dir::exists(Path::getDirectoryName($backupPath)) == FALSE) {
        throw new Directory_Not_Found_Exception('One or more parts of the backup path could not be found');
      }

      if (copy($sourcePath, $backupPath) == FALSE) {
        throw new File_System_Exception('Failed to create a backup of source');
      }
    }

    if (unlink($destinationPath) == TRUE) {
      return copy($sourcePath, $destinationPath);
    }

    return FALSE;
  }

  // $fileInfo = File::security('.htaccess');
  // $fileInfo->isReadable();
  static public function security ($path) {
    // xxx assert

    if (trim($path) == '') {
      throw new InvalidArgumentException('Path cannot be empty');
    }
    elseif ($path == '.' || $path == '..') {
      throw new InvalidArgumentException;
    }
    elseif (Path::isDirectorySeparator(substr($path, -1, 1)) == TRUE) {
      throw new File_System_Exception('Filename, directory name, or volume label syntax is incorrect');
    }

    $path = Path::getAbsolutePath($path);

    if (is_file($path) == FALSE) {
      throw new File_Not_Found_Exception;
    }

    return new File_System_Security($path);
  }

  static public function open ($path, $mode = File_Mode::openOrCreate, $access = File_Access::readWrite) {
    // xxx assert

    if (trim($path) == '') {
      throw new InvalidArgumentException('Path cannot be empty');
    }
    elseif ($path == '.' || $path == '..') {
      throw new InvalidArgumentException;
    }
    elseif (Path::isDirectorySeparator(substr($path, -1, 1)) == TRUE) {
      throw new File_System_Exception('Filename, directory name, or volume label syntax is incorrect');
    }

    $path = Path::getAbsolutePath($path);

    if (is_dir($path) == TRUE) {
      throw new Unauthorised_Access_Exception;
    }

    return new File_Stream($path, $mode, $access);
  }

  // xxx need to test
  static public function getLastAccessTime ($path) {
    // xxx assert

    if (trim($path) == '') {
      throw new InvalidArgumentException('Path cannot be empty');
    }
    elseif ($path == '.' || $path == '..') {
      throw new InvalidArgumentException;
    }
    elseif (Path::isDirectorySeparator(substr($path, -1, 1)) == TRUE) {
      throw new File_System_Exception('Filename, directory name, or volume label syntax is incorrect');
    }

    $path = Path::getAbsolutePath($path);

    if (is_file($path) == FALSE) {
      throw new File_Not_Found_Exception;
    }

    return intval(fileatime($path));
  }

  // xxx need to test
  static public function getLastWriteTime ($path) {
    // xxx assert

    if (trim($path) == '') {
      throw new InvalidArgumentException('Path cannot be empty');
    }
    elseif ($path == '.' || $path == '..') {
      throw new InvalidArgumentException;
    }
    elseif (Path::isDirectorySeparator(substr($path, -1, 1)) == TRUE) {
      throw new File_System_Exception('Filename, directory name, or volume label syntax is incorrect');
    }

    $path = Path::getAbsolutePath($path);

    if (is_file($path) == FALSE) {
      throw new File_Not_Found_Exception;
    }

    return intval(filectime($path));
  }

  // xxx need to test
  static public function setLastAccessTime ($path) {
    // xxx assert

    if (trim($path) == '') {
      throw new InvalidArgumentException('Path cannot be empty');
    }
    elseif ($path == '.' || $path == '..') {
      throw new InvalidArgumentException;
    }
    elseif (Path::isDirectorySeparator(substr($path, -1, 1)) == TRUE) {
      throw new File_System_Exception('Filename, directory name, or volume label syntax is incorrect');
    }

    $path = Path::getAbsolutePath($path);

    if (is_file($path) == FALSE) {
      throw new File_Not_Found_Exception;
    }

    if (is_writable($path) == FALSE) {
      throw new File_Not_Writable_Exception;
    }

    if (touch($path) == TRUE) {
      clearstatcache();
      return TRUE;
    }

    return FALSE;
  }

  // xxx need to test
  static public function setLastWriteTime ($path) {
    // xxx assert

    if (trim($path) == '') {
      throw new InvalidArgumentException('Path cannot be empty');
    }
    elseif ($path == '.' || $path == '..') {
      throw new InvalidArgumentException;
    }
    elseif (Path::isDirectorySeparator(substr($path, -1, 1)) == TRUE) {
      throw new File_System_Exception('Filename, directory name, or volume label syntax is incorrect');
    }

    $path = Path::getAbsolutePath($path);

    if (is_file($path) == FALSE) {
      throw new File_Not_Found_Exception;
    }

    if (is_writable($path) == FALSE) {
      throw new File_Not_Writable_Exception;
    }

    // Add and then remove byte from the end of the file, a bit of a hack but it works
    $fileSize = filesize($path);
    $bytesAdded = file_put_contents($path, '1');
    if ($bytesAdded > 0) {
      $fileHandle = fopen($path, 'r+b');
      if ($fileHandle != FALSE) {
        ftruncate($fileHandle, $fileSize);
        fclose($fileHandle);
        clearstatcache();
        return TRUE;
      }
    }

    return FALSE;
  }

  // xxx need to create
  static public function getCreationTime ($path) {

  }

  // xxx need to create
  static public function getLastChangeTime ($path) {

  }
}

?>