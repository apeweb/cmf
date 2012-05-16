<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

/**
 * The path library enables a way to recursively include a list of directories and or files from a
 * series of paths. This is useful for the inclusion of modules or grouping together a selection
 * of microsites to make a website.
 * @link http://intranet/docs/framework/v3/Path_Library/
 */
class Path_Library {
  private static $_paths = array();
  private static $_excludedPaths = array();
  private static $_excludedPatterns = array();

  /**
   * Prevent an object from being created
   * @link http://intranet/docs/framework/v3/Path_Library/__construct/
   */
  private function __construct () {}

  /**
   * Adds a list of paths supplied to the start of the path library
   * @link http://intranet/docs/framework/v3/Path_Library/add/
   */
  static public function add ($paths) {
    Assert::isString($paths);

    $paths = func_get_args();
    $paths = array_reverse($paths);

    foreach ($paths as $path) {
      $path = Path::getAbsolutePath($path);

      if (array_search($path, self::$_paths) === FALSE) {
        array_unshift(self::$_paths, $path);
      }
    }
  }

  /**
   * Adds a path before another path within the path library
   * @link http://intranet/docs/framework/v3/Path_Library/addBefore/
   */
  static public function addBefore ($newPath, $existingPath) {
    Assert::isString($newPath);
    Assert::isString($existingPath);

    $newPath = Path::getAbsolutePath($newPath);
    $existingPath = Path::getAbsolutePath($existingPath);

    if (($index = array_search($newPath, self::$_paths)) !== FALSE) {
      unset(self::$_paths[$index]);
    }

    if (count(self::$_paths) == 0 || array_search($existingPath, self::$_paths) === FALSE) {
      return self::add($newPath);
    }
    else {
      $position = array_search($existingPath, self::$_paths, TRUE);
      $initialPaths = array_splice(self::$_paths, 0, $position);
      $newPath = array($newPath);
      self::$_paths = array_merge($initialPaths, $newPath, self::$_paths);

      return TRUE;
    }
  }

  /**
   * Adds a path after another path within the path library
   * @link http://intranet/docs/framework/v3/Path_Library/addAfter/
   */
  static public function addAfter ($newPath, $existingPath) {
    Assert::isString($newPath);
    Assert::isString($existingPath);

    $newPath = Path::getAbsolutePath($newPath);
    $existingPath = Path::getAbsolutePath($existingPath);

    if (($index = array_search($newPath, self::$_paths)) !== FALSE) {
      unset(self::$_paths[$index]);
    }

    if (count(self::$_paths) == 0 || array_search($existingPath, self::$_paths) === FALSE) {
      // Just add the observer if there are no events or the existing observer doesn't exist
      return self::add($newPath);
    }
    else {
      $position = array_search($existingPath, self::$_paths, TRUE) + 1;
      $initialPaths = array_splice(self::$_paths, 0, $position);
      $newPath = array($newPath);
      self::$_paths = array_merge($initialPaths, $newPath, self::$_paths);

      return TRUE;
    }
  }

  /**
   * Deletes each path supplied out of the path library
   * @link http://intranet/docs/framework/v3/Path_Library/delete/
   */
  static public function delete ($paths) {
    Assert::isString($paths);

    $paths = func_get_args();

    foreach ($paths as $path) {
      $path = Path::getAbsolutePath($path);

      if (($index = array_search($path, self::$_paths)) !== FALSE) {
        unset(self::$_paths[$index]);
      }
      else {
        continue;
      }
    }
  }

  /**
   * Returns an array of paths that make up the path library
   * @link http://intranet/docs/framework/v3/Path_Library/paths/
   */
  static public function paths () {
    return self::$_paths;
  }

  /**
   * Checks to see whether the path exists within the path library
   * NB was called pathExists in BETA
   * @link http://intranet/docs/framework/v3/Path_Library/isPathInLibrary/
   */
  static public function isPathInLibrary ($path) {
    Assert::isString($path);

    $path = Path::getAbsolutePath($path);

    if (array_search($path, self::$_paths) !== FALSE) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Gets the absolute path of a file or folder including the path library path if there is a match
   * @link http://intranet/docs/framework/v3/Path_Library/getAbsolutePath/
   */
  static public function getAbsolutePath ($path, $recursive = TRUE) {
    Assert::isString($path);
    Assert::isBoolean($recursive);

    if (trim($path) == '') {
      throw new InvalidArgumentException('Path cannot be empty');
    }

    if ($recursive == TRUE) {
      foreach (self::$_paths as $library_path) {
        $searchPattern = '#[' . preg_quote(DIRECTORY_SEPARATOR . ALT_DIRECTORY_SEPARATOR, '#') . ']' . preg_quote($path, '#') . '[' . preg_quote(DIRECTORY_SEPARATOR . ALT_DIRECTORY_SEPARATOR, '#') . ']*$#i';
        $entriesFound = Dir::getFileSystemEntries($library_path, $searchPattern, $recursive);
        if (count($entriesFound) > 0) {
          return $entriesFound[0];
        }
      }
    }
    else {
      foreach (self::$_paths as $library_path) {
        $absolutePath = Path::getAbsolutePath($library_path . $path);
        if (File::exists($absolutePath) == TRUE || Dir::exists($absolutePath) == TRUE) {
          return $absolutePath;
        }
      }
    }

    return '';
  }

  /**
   * Gets the absolute path of a file including the path library path if there is a match
   * @link http://intranet/docs/framework/v3/Path_Library/getAbsoluteFilePath/
   */
  static public function getAbsoluteFilePath ($path, $recursive = TRUE) {
    Assert::isString($path);
    Assert::isBoolean($recursive);

    if (trim($path) == '') {
      throw new InvalidArgumentException('Path cannot be empty');
    }

    if ($recursive == TRUE) {
      foreach (self::$_paths as $library_path) {
        $searchPattern = '#[' . preg_quote(DIRECTORY_SEPARATOR . ALT_DIRECTORY_SEPARATOR, '#') . ']' . preg_quote($path, '#') . '$#i';
        $entriesFound = Dir::getFiles($library_path, $searchPattern, $recursive);
        if (count($entriesFound) > 0) {
          return $entriesFound[0];
        }
      }
    }
    else {
      foreach (self::$_paths as $library_path) {
        $absolutePath = Path::getAbsolutePath($library_path . $path);
        if (File::exists($absolutePath) == TRUE) {
          return $absolutePath;
        }
      }
    }

    return '';
  }

  /**
   * Gets the absolute path of a file including the path library path if there is a match
   * @link http://intranet/docs/framework/v3/Path_Library/getAbsoluteDirectoryPath/
   */
  static public function getAbsoluteDirectoryPath ($path, $recursive = TRUE) {
    Assert::isString($path);
    Assert::isBoolean($recursive);

    if (trim($path) == '') {
      throw new InvalidArgumentException('Path cannot be empty');
    }

    if ($recursive == TRUE) {
      foreach (self::$_paths as $library_path) {
        $searchPattern = '#[' . preg_quote(DIRECTORY_SEPARATOR . ALT_DIRECTORY_SEPARATOR, '#') . ']' . preg_quote($path, '#') . '[' . preg_quote(DIRECTORY_SEPARATOR . ALT_DIRECTORY_SEPARATOR, '#') . ']*$#i';
        $entriesFound = Dir::getDirectories($library_path, $searchPattern, $recursive);
        if (count($entriesFound) > 0) {
          return $entriesFound[0];
        }
      }
    }
    else {
      foreach (self::$_paths as $library_path) {
        $absolutePath = Path::getAbsolutePath($library_path . $path);
        if (Dir::exists($absolutePath) == TRUE) {
          return $absolutePath;
        }
      }
    }

    return '';
  }

  /**
   * Checks to see whether the folder exists within one or more path library paths
   * @link http://intranet/docs/framework/v3/Path_Library/directoryExists/
   */
  static public function directoryExists ($path, $recursive = TRUE) {
    Assert::isString($path);
    Assert::isBoolean($recursive);

    if (self::getAbsoluteDirectoryPath($path, $recursive) != '') {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Checks to see whether the file exists within one or more path library paths
   * @link http://intranet/docs/framework/v3/Path_Library/fileExists/
   */
  static public function fileExists ($path, $recursive = TRUE) {
    Assert::isString($path);
    Assert::isBoolean($recursive);

    if (self::getAbsoluteFilePath($path, $recursive) != '') {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Returns an array of folders that are found in each folder found within each path library that
   * matches that of the path given
   * @link http://intranet/docs/framework/v3/Path_Library/getDirectories/
   */
  static public function getDirectories ($path, $searchPattern = '', $recursive = TRUE, $merge = TRUE) {
    Assert::isString($path);
    Assert::isString($searchPattern);
    Assert::isBoolean($recursive);
    Assert::isBoolean($merge);

    $pathsFound = array();
    $directories = array();
    $cache = array();

    if (isset($cache[$path][$searchPattern][intval($recursive)][intval($merge)])) {
      $pathsFound = $cache[$path][$searchPattern][intval($recursive)][intval($merge)];
    }
    else {
      if ($path != '') {
        foreach (self::$_paths as $library_path) {
          if (Dir::exists($library_path . $path) == TRUE) {
            $pathsFound[] = Path::getAbsolutePath($library_path . $path);
            if ($merge == FALSE) {
              break;
            }
          }
        }
      }
      else {
        $pathsFound = self::$_paths;
      }

      $cache[$path][$searchPattern][intval($recursive)][intval($merge)] = $pathsFound;
    }

    if (count($pathsFound) < 1) {
      throw new Directory_Not_Found_Exception('One or more parts of the path could not be found');
    }

    foreach ($pathsFound as $path) {
      $directories = array_merge($directories, Dir::getDirectories($path, $searchPattern, $recursive));
    }

    foreach ($directories as $directoryId => $directory) {
      if (self::isPathExcluded($directory) == TRUE) {
        unset($directories[$directoryId]);
      }
    }

    return $directories;
  }

  /**
   * Returns an array of files, and their paths, that are found in each folder found within each
   * path library that matches that of the path given
   * @link http://intranet/docs/framework/v3/Path_Library/getFiles/
   */
  static public function getFiles ($path, $searchPattern = '', $recursive = TRUE, $merge = TRUE) {
    Assert::isString($path);
    Assert::isString($searchPattern);
    Assert::isBoolean($recursive);
    Assert::isBoolean($merge);

    $pathsFound = array();
    $files = array();
    $cache = array();

    if (isset($cache[$path][$searchPattern][intval($recursive)][intval($merge)])) {
      $pathsFound = $cache[$path][$searchPattern][intval($recursive)][intval($merge)];
    }
    else {
      if ($path != '') {
        foreach (self::$_paths as $library_path) {
          if (Dir::exists($library_path . $path) == TRUE) {
            $currentPath = Path::getAbsolutePath($library_path . $path);

            if (self::isPathExcluded($currentPath) == TRUE) {
              continue;
            }

            $pathsFound[] = $currentPath;
            if ($merge == FALSE) {
              break;
            }
          }
        }
      }
      else {
        $pathsFound = self::$_paths;
      }

      $cache[$path][$searchPattern][intval($recursive)][intval($merge)] = $pathsFound;
    }

    foreach ($pathsFound as $path) {
      $files = array_merge($files, Dir::getFiles($path, $searchPattern, $recursive));
    }

    // If the recursive argument is set to TRUE, and one path (path A) is within another path (path B), path B
    // will include paths found within path B and then path A will include the same files again, so the
    // the following line ensures that the same files are not included multiple times

    $files = array_unique($files);

    foreach ($files as $fileId => $file) {
      if (self::isPathExcluded($file) == TRUE) {
        unset($files[$fileId]);
      }
    }

    return $files;
  }

  /**
   * Returns an array of files, and their paths, and folders that are found in each folder found
   * within each path library that matches that of the path given
   * @link http://intranet/docs/framework/v3/Path_Library/getFileSystemEntries/
   */
  static public function getFileSystemEntries ($path, $searchPattern = '', $recursive = TRUE, $merge = TRUE) {
    Assert::isString($path);
    Assert::isString($searchPattern);
    Assert::isBoolean($recursive);
    Assert::isBoolean($merge);

    $pathsFound = array();
    $fileSystemEntries = array();
    $cache = array();

    if (isset($cache[$path][$searchPattern][intval($recursive)][intval($merge)])) {
      $pathsFound = $cache[$path][$searchPattern][intval($recursive)][intval($merge)];
    }
    else {
      if ($path != '') {
        foreach (self::$_paths as $library_path) {
          if (Dir::exists($library_path . $path) == TRUE) {
            $pathsFound[] = Path::getAbsolutePath($library_path . $path);
            if ($merge == FALSE) {
              break;
            }
          }
        }
      }
      else {
        $pathsFound = self::$_paths;
      }

      $cache[$path][$searchPattern][intval($recursive)][intval($merge)] = $pathsFound;
    }

    foreach ($pathsFound as $path) {
      $fileSystemEntries = array_merge($fileSystemEntries, Dir::getFileSystemEntries($path, $searchPattern, $recursive));
    }

    foreach ($fileSystemEntries as $fileSystemEntryId => $fileSystemEntry) {
      if (self::isPathExcluded($fileSystemEntry) == TRUE) {
        unset($fileSystemEntries[$fileSystemEntryId]);
      }
    }

    return $fileSystemEntries;
  }

  /**
   * Loads the first file found matching the parameters passed, if the file does not exist a fatal
   * error is trigged
   * @link http://intranet/docs/framework/v3/Path_Library/requireFile/
   */
  static public function requireFile ($path, $searchPattern = '', $recursive = TRUE) {
    Assert::isString($path);
    Assert::isString($searchPattern);
    Assert::isBoolean($recursive);

    $files = self::getFiles($path, $searchPattern, $recursive, FALSE);
    if (count($files) > 0) {
      return require($files[0]);
    }
  }

  /**
   * Loads the first file found matching the parameters passed, if the file has already loaded it
   * is not loaded again, if the file does not exist a fatal error is trigged
   * @link http://intranet/docs/framework/v3/Path_Library/requireFileOnce/
   */
  static public function requireFileOnce ($path, $searchPattern = '', $recursive = TRUE) {
    Assert::isString($path);
    Assert::isString($searchPattern);
    Assert::isBoolean($recursive);

    $files = self::getFiles($path, $searchPattern, $recursive, FALSE);
    if (count($files) > 0) {
      return require_once($files[0]);
    }
  }

  /**
   * Loads the first file found matching the parameters passed, if the file does not exist a
   * warning error is trigged
   * @link http://intranet/docs/framework/v3/Path_Library/includeFile/
   */
  static public function includeFile ($path, $searchPattern = '', $recursive = TRUE) {
    Assert::isString($path);
    Assert::isString($searchPattern);
    Assert::isBoolean($recursive);

    $files = self::getFiles($path, $searchPattern, $recursive, FALSE);
    if (count($files) > 0) {
      return include($files[0]);
    }
  }

  /**
   * Loads the first file found matching the parameters passed, if the file has already loaded it
   * is not loaded again, if the file does not exist a warning error is trigged
   * @link http://intranet/docs/framework/v3/Path_Library/includeFileOnce/
   */
  static public function includeFileOnce ($path, $searchPattern = '', $recursive = TRUE) {
    Assert::isString($path);
    Assert::isString($searchPattern);
    Assert::isBoolean($recursive);

    $files = self::getFiles($path, $searchPattern, $recursive, FALSE);
    if (count($files) > 0) {
      return include_once($files[0]);
    }
  }

  /**
   * Excludes a file or directory with a path matching the regular expression passed from being
   * loaded from within the path library
   * @link http://intranet/docs/framework/v3/Path_Library/excludePattern/
   */
  static public function excludePattern ($pattern) {
    Assert::isString($pattern);

    if (array_search($pattern, self::$_excludedPatterns) === FALSE) {
      self::$_excludedPatterns[] = $pattern;
    }
  }

  /**
   * Excludes a directory from being loaded from within the path library
   * @link http://intranet/docs/framework/v3/Path_Library/excludeDirectory/
   */
  static public function excludeDirectory ($path) {
    Assert::isString($path);

    $path = Path::getAbsolutePath($path);

    if (array_search($path, self::$_excludedPaths) === FALSE) {
      self::$_excludedPaths[] = $path;
    }
  }

  /**
   * Excludes a file from being loaded from within the path library
   * @link http://intranet/docs/framework/v3/Path_Library/excludeFile/
   */
  static public function excludeFile ($path) {
    Assert::isString($path);

    $path = Path::getAbsolutePath($path);

    if (array_search($path, self::$_excludedPaths) === FALSE) {
      self::$_excludedPaths[] = $path;
    }
  }

  /**
   * Checks to see if the file can be loaded or not based on the exclusion rules
   * @link http://intranet/docs/framework/v3/Path_Library/isPathExcluded/
   */
  static public function isPathExcluded ($path) {
    Assert::isString($path);

    $path = Path::getAbsolutePath($path);
    $directory = Path::getDirectoryName($path);

    if (count(self::$_excludedPaths) < 1 && count(self::$_excludedPatterns) < 1) {
      return FALSE;
    }

    if (count(self::$_excludedPaths) > 0) {
      foreach (self::$_excludedPaths as $excludedPath) {
        if (preg_match('#^' . preg_quote($excludedPath, '#') . '#', $path)) {
          return TRUE;
        }
      }
    }

    if (count(self::$_excludedPatterns) > 0) {
      foreach (self::$_excludedPatterns as $pattern) {
        $result = preg_match($pattern, $path);
        if ($result === FALSE) {
          throw new InvalidArgumentException('A regular expression parsing error occurred');
        }
        elseif ($result > 0) {
          return TRUE;
        }
      }
    }

    return FALSE;
  }
}

?>