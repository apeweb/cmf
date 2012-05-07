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

// Need to define some extra constants so that we can use them in the class
// *nix/Mac
if (DIRECTORY_SEPARATOR == '/') {
  define('VOLUME_SEPARATOR', '/');
  define('MAX_PATH_LENGTH', 4096); // xxx test
  define('MAX_DIRECTORY_LENGTH', 255); // xxx test
  define('ALT_DIRECTORY_SEPARATOR', '\\');
}
// Win*
else {
  define('VOLUME_SEPARATOR', ':');
  define('MAX_PATH_LENGTH', 254);
  define('MAX_DIRECTORY_LENGTH', 244);
  define('ALT_DIRECTORY_SEPARATOR', '/');
}

/**
 * Path parsing and manipulation
 * @link http://intranet/docs/framework/v3/Path/
 */
class Path {
  private static $_invalidPathChars = '*?<>|"';
  private static $_pathValidation = TRUE;

  private function __construct () {}

  /**
   * Disables the validating of path characters and certain syntax checking
   * @link http://intranet/docs/framework/v3/Path/disablePathValidation/
   */
  public static function disablePathValidation () {
    Path::$_pathValidation = FALSE;
  }

  /**
   * Enables the validating of path characters and certain syntax checking
   * @link http://intranet/docs/framework/v3/Path/enablePathValidation/
   */
  public static function enablePathValidation () {
    Path::$_pathValidation = TRUE;
  }

  /**
   * Checks to see whether path validation is enabled or disabled
   * @link http://intranet/docs/framework/v3/Path/pathValidation/
   */
  public static function pathValidation () {
    return Path::$_pathValidation;
  }

  /**
   * Returns the path without a filename if one exists
   * @link http://intranet/docs/framework/v3/Path/getDirectoryName/
   */
  public static function getDirectoryName ($path) {
    if (trim($path) == '') {
      throw new InvalidArgumentException("Path cannot be empty");
    }

    $path = Path::getAbsolutePath($path);

    if (Path::isDirectorySeparator(substr($path, -1, 1)) == TRUE) {
      return Path::getAbsolutePath($path);
    }
    else {
      return Path::getAbsolutePath($path . '/../');
    }
  }

  /**
   * Checks to see if the path contains illegal characters
   * @link http://intranet/docs/framework/v3/Path/hasInvalidPathChars/
   */
  public static function hasInvalidPathChars ($path) {
    for ($i = 0; $i < strlen($path); $i++) {
      if (ord($path[$i]) < 32) {
        return TRUE;
      }
    }

    // As most *nix/Mac systems only limit a NULL byte (\0) which is already checked for no
    // further checking is required
    if (DIRECTORY_SEPARATOR == '/' || Path::$_pathValidation == FALSE) {
      return FALSE;
    }
    else {
      return preg_match('#[' . Path::$_invalidPathChars . ']#', $path);
    }
  }

  /**
   * Expands the path to a fully qualified path, does not check to see if the file/directory exists
   * @link http://intranet/docs/framework/v3/Path/getAbsolutePath/
   */
  public static function getAbsolutePath ($path, $reconstruct = TRUE) {
    $path = Path::normalisePath($path);

    if ($path == '') {
      throw new InvalidArgumentException("Path cannot be empty");
    }

    if (preg_match('#^([0-9]:)|([a-z]{2,}:)#i', $path) == TRUE) {
      throw new Not_Supported_Exception("URI format paths are not supported");
    }

    if (Path::$_pathValidation == FALSE) {
      return $path;
    }

    $path = Path::_expandPath($path, $reconstruct);

    if (strlen($path) > MAX_PATH_LENGTH) {
      throw new Path_Too_Long_Exception("The specified path '" . $path . "' is too long");
    }

    return $path;
  }

  /**
   * Internal helper function to expand the path
   * @link http://intranet/docs/framework/v3/Path/getAbsolutePath/
   */
  protected static function _expandPath ($path, $reconstruct) {
    // xxx assert

    $uncPath = FALSE;
    $prefix = '';
    $postfix = '';
    $directorySeparator = DIRECTORY_SEPARATOR;
    static $cache = array();

    // Check to see if we have the path in the cache first, because path cannot be empty (ie NULL) isset is fine to use here opposed to array_key_exists
    if (isset($cache[$path][intval($reconstruct)])) {
      return $cache[$path][intval($reconstruct)];
    }

    if ($reconstruct == FALSE) {
      if (strpos($path, '/') !== FALSE && strpos($path, '\\') === FALSE) {
        $directorySeparator = '/';
      }
      elseif (strpos($path, '\\') !== FALSE && strpos($path, '/') === FALSE) {
        $directorySeparator = '\\';
      }
    }

    // Needed to make sure we don't break UNC paths
    if (substr($path, 0, 2) == '\\\\' && substr($path, 3, 1) != '\\') {
      $prefix = '\\\\';
      $directorySeparator = '\\';
      $uncPath = TRUE;
    }
    // Win* paths such as c:\progra~1\ and d:\intetpub\wwwroot\
    elseif (preg_match('#^[a-z]\:#i', $path) == TRUE) {
      if ($reconstruct == TRUE) {
        $directorySeparator = '\\';
        $path = ucfirst($path);
      }
    }
    elseif (Path::isPathRooted($path) == FALSE) {
      // If the path ends with a dot or 2 dots
      if ($path == '.' || $path == '..') {
        $postfix = $directorySeparator;
      }
      // The following is require to catch illegal paths
      elseif (strlen($path) > 2) {
        if (substr($path, -3, 3) == DIRECTORY_SEPARATOR . '..' || substr($path, -2, 2) == DIRECTORY_SEPARATOR . '.') {
          $postfix = $directorySeparator;
        }
        elseif (substr($path, -3, 3) == ALT_DIRECTORY_SEPARATOR . '..' || substr($path, -2, 2) == ALT_DIRECTORY_SEPARATOR . '.') {
          $postfix = $directorySeparator;
        }
      }

      $path = getcwd() . $directorySeparator . $path;

      if (preg_match('#^[a-z]\:#i', $path) != TRUE) {
        $prefix = $directorySeparator;
      }
    }
    else {
      if ($reconstruct == TRUE && DIRECTORY_SEPARATOR == '\\') {
        $prefix = substr(getcwd(), 0, 3);
      }
      else {
        $prefix = $directorySeparator;
      }
    }

    if (Path::isDirectorySeparator(substr($path, -1, 1)) == TRUE) {
      $postfix = $directorySeparator;
    }

    // Remove any duplicate path separators
    $path = preg_replace('#[' . preg_quote(DIRECTORY_SEPARATOR, '#') . preg_quote(ALT_DIRECTORY_SEPARATOR, '#') . ']{2,}#', $directorySeparator, $path);

    // Quick escape for root paths
    if (Path::isDirectorySeparator($path) == TRUE) {
      if ($reconstruct == TRUE) {
        if (DIRECTORY_SEPARATOR == '\\') {
          return substr(getcwd(), 0, 3);
        }
        else {
          return $directorySeparator;
        }
      }
      else {
        return $path;
      }
    }

    $pathParts = array_filter(preg_split('#' . preg_quote(DIRECTORY_SEPARATOR, '#') . '|' . preg_quote(ALT_DIRECTORY_SEPARATOR, '#') . '#', $path), 'strlen');
    $absolutes = array();

    $levelsUp = 0;
    $levelsDown = 0;
    $part = 1;
    $numPathParts = count($pathParts);

    if ($uncPath == TRUE && $numPathParts < 2) {
      throw new Invalid_Path_Exception("The UNC path should be of the form '\\\\server\\share'");
    }

    // Fixes c:foo or c:. issues and helps prevent c:.. later
    if ($numPathParts > 0) {
      $firstPart = reset($pathParts);
      if (preg_match('#^[a-z]\:#i', $firstPart) == TRUE) {
        if (strlen($firstPart) > 2 && Path::isDirectorySeparator(substr($firstPart, 2, 1)) == FALSE) {
          array_shift($pathParts);
          array_unshift($pathParts, substr($firstPart, 2));
          array_unshift($pathParts, substr($firstPart, 0, 2));
        }
      }
    }

    foreach ($pathParts as $pathPart) {
      if ('.' == $pathPart) {
        ++$part;
        continue;
      }
      if ('..' == $pathPart) {
        array_pop($absolutes);

        // check if is dir then if so go up
        if ($part < $numPathParts || ($part >= $numPathParts && $postfix != '')) {
          ++$levelsUp;
        }
      }
      else {
        if (strlen($pathPart) > MAX_DIRECTORY_LENGTH) {
          throw new Path_Too_Long_Exception("The directory name '" . trim($pathPart) . "' is too long in path '" . trim(func_get_arg(0)) . "'");
        }

        if (DIRECTORY_SEPARATOR == '\\') {
          $pathPart = ltrim($pathPart);
          $absolutes[] = rtrim($pathPart, " \t\n\r\0\x0B.");
        }
        else {
          $absolutes[] = $pathPart;
        }

        if ($part < $numPathParts || ($part >= $numPathParts && $postfix != '')) {
          ++$levelsDown;
        }
      }

      ++$part;
    }

    // if path goes up too many levels path is illegal
    if ($levelsUp > $levelsDown) {
      throw new Invalid_Path_Exception("Path '" . trim(func_get_arg(0)) . "' is illegal");
    }
    elseif (preg_match('#^[a-z]\:#i', $firstPart) == TRUE && count($absolutes) == 0) {
      throw new Invalid_Path_Exception("Path '" . trim(func_get_arg(0)) . "' is illegal");
    }

    $path = implode($directorySeparator, $absolutes);

    // The path is the root
    if ($path == '') {
      return $prefix;
    }

    // If the path is an existing directory make sure a path separator is added to the end of the
    // path, don't use the Dir::exists function here as it will create an infinite loop
    if (@is_dir($prefix . $path) == TRUE && $postfix == '') {
      $postfix = $directorySeparator;
    }

    // Build path and cache it
    $cache[$path][intval($reconstruct)] = $prefix . $path . $postfix;

    return $cache[$path][intval($reconstruct)];
  }

  /**
   * Returns the extension (without a period) of the filename or an empty string if an extension was not found
   * @link http://intranet/docs/framework/v3/Path/getExtension/
   */
  public static function getExtension ($path) {
    $path = trim($path);

    if ($path == '') {
      return '';
    }

    // make sure filename is passed as we can have /foo.bar/ passed and the ext would be .bar/
    if (Path::isDirectorySeparator(substr($path, -1, 1)) == FALSE) {
      return strval(substr(strrchr(substr($path, 1), '.'), 1));
    }
    else {
      return '';
    }
  }

  /**
   * Changes the extension of a file if one is found, if no extension is found then the extension
   * is appended
   * @link http://intranet/docs/framework/v3/Path/changeExtension/
   */
  public static function changeExtension ($path, $newExtension) {
    $path = trim($path);

    if ($path == '') {
      return '';
    }

    // makes sure filename is passed as we can have /foo.bar/ passed and the ext would be .bar/
    if (Path::isDirectorySeparator(substr($path, -1, 1)) == FALSE) {
      if ($newExtension[0] != '.') {
        $newExtension = '.' . $newExtension;
      }

      $currentExtension = Path::getExtension($path);
      if ($currentExtension != '') {
        return preg_replace('#' . preg_quote('.' . $currentExtension) . '$#i', $newExtension, $path);
      }
      else {
        return $path . $newExtension;
      }
    }
    else {
      return '';
    }
  }

  /**
   * Gets the filename from the path
   * @link http://intranet/docs/framework/v3/Path/getFileName/
   */
  public static function getFileName ($path) {
    if (trim($path) == '') {
      return '';
    }

    $path = Path::getAbsolutePath($path);

    $pathWithoutFileName = Path::getDirectoryName($path);

    if (strlen($path) == strlen($pathWithoutFileName)) {
      return '';
    }
    else {
      return substr($path, strlen($pathWithoutFileName));
    }
  }

  /**
   * Gets the filename from the path without an extension
   * @link http://intranet/docs/framework/v3/Path/getFileNameWithoutExtension/
   */
  public static function getFileNameWithoutExtension ($path) {
    $path = Path::getFileName($path);

    $extension = Path::getExtension($path);

    if ($extension != '') {
      return substr($path, 0, strlen($path) - strlen($extension) - 1);
    }
    else {
      return $path;
    }
  }

  /**
   * Removes any whitespace around the path and makes various checks to ensure the path is
   * not going to cause any strange errors while working with the path
   * @link http://intranet/docs/framework/v3/Path/normalisePath/
   */
  public static function normalisePath ($path) {
    if (DIRECTORY_SEPARATOR == '\\' && trim($path) == '') {
      return '';
    }

    if (Path::$_pathValidation == TRUE) {
      // UNC path root (which is not possible), Win* freaks out when doing this (but is translated as the root in *nix)
      if ($path == '\\\\') {
        throw new Invalid_Path_Exception("The UNC path '" . $path . "' should be of the form '\\\\server\\share'");
      }

      if (Path::hasInvalidPathChars($path) == TRUE) {
        throw new Invalid_Path_Exception("Illegal characters in path '" . $path . "'");
      }

      if (strlen($path) > MAX_PATH_LENGTH) {
        throw new Path_Too_Long_Exception("The path '" . $path . "' is too long");
      }
    }
    elseif (strpos($path, chr(0)) !== FALSE) {
      throw new Invalid_Path_Exception("Illegal characters in path '" . $path . "'");
    }

    if (DIRECTORY_SEPARATOR == '/') {
      return $path;
    }
    else {
      return trim($path);
    }
  }

  /**
   * Checks to see whether the character is a directory separator or not
   * @link http://intranet/docs/framework/v3/Path/isDirectorySeparator/
   */
  public static function isDirectorySeparator ($character) {
    return ($character == DIRECTORY_SEPARATOR || $character == ALT_DIRECTORY_SEPARATOR);
  }

  /**
   * Checks to see if the given path contains a root or not
   * @link http://intranet/docs/framework/v3/Path/isPathRooted/
   */
  public static function isPathRooted ($path) {
    $path = Path::normalisePath($path);

    if (preg_match('#^[a-z]\:#i', $path) == TRUE) {
      return TRUE;
    }

    return Path::isDirectorySeparator(substr($path, 0, 1));
  }

  /**
   * Returns the root of the given path
   * @link http://intranet/docs/framework/v3/Path/getPathRoot/
   */
  public static function getPathRoot ($path) {
    if (trim($path) == '') {
      throw new InvalidArgumentException("Path cannot be empty");
    }

    $path = Path::getAbsolutePath($path, FALSE);

    // Needed to make sure we don't break UNC paths
    if (substr($path, 0, 2) == '\\\\') {
        $path = trim($path, '\\');
        $pathParts = explode('\\', $path, 3);
        return '\\\\' . implode('\\', array_slice($pathParts, 0, 2)) . '\\';
    }
    // Win* paths such as c:\progra~1\ and d:\intetpub\wwwroot\
    elseif (preg_match('#^[a-z]\:#i', $path) == TRUE) {
      preg_match('#^[a-z]\:#i', $path, $match);
      return ucfirst($match[0]) . '\\';
    }
    else {
      return '/';
    }
  }
}

?>