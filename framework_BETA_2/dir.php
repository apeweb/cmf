<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

/**
 * Core directory functions for adding and deleting directories and listing/searching for files and
 * directories within each directory
 * @link http://intranet/docs/framework/v3/Dir/
 */
class Dir {
  private static $_cache = array();

  private function __construct () {}

  /**
   * Determines whether the given path refers to an existing directory
   * @link http://intranet/docs/framework/v3/Dir/exists/
   */
  static public function exists ($path) {
    Assert::isString($path);

    $path = Path::normalisePath($path);

    if ($path == '') {
      throw new InvalidArgumentException("Path cannot be empty");
    }
    // if path is current working directory or parent of current working directory
    elseif ($path == '.') {
      return TRUE;
    }
    elseif ($path == '..') {
      // If the current path is the root, we can't go any higher
      if (Dir::getParentDirectory('.') == '') {
        $path = Path::getAbsolutePath($path);
        throw new Unauthorised_Access_Exception("Parent of path '" . $path . "' is inaccessible");
      }
      else {
        return TRUE;
      }
    }

    $path = Path::getAbsolutePath($path);

    $lastError = error_get_last();

    $dirExists = @is_dir($path);

    if ($lastError !== error_get_last()) {
      throw new Unauthorised_Access_Exception("Access to path '" . $path . "' is denied"); // Handles open_basedir restrictions
    }

    return $dirExists;
  }

  /**
   * Returns an array of files with their paths within the path specified
   * @link http://intranet/docs/framework/v3/Dir/getFiles/
   */
  static public function getFiles ($path, $searchPattern = '', $recursive = FALSE, $sort = Sort_Order::inherit) {
    Assert::isString($path);
    Assert::isString($searchPattern);
    Assert::isBoolean($recursive);
    Assert::isInteger($sort, TRUE);

    static $cache = array();

    $path = Path::normalisePath($path);

    if ($path == '') {
      throw new InvalidArgumentException("Path cannot be empty");
    }

    if (Reflection_Enum::hasConstant('Sort_Order', $sort) == FALSE) {
      if (is_int($sort) == TRUE) {
        throw new OutOfRangeException("Sort value '" . $sort . "' is out of legal range");
      }
      else {
        throw new Invalid_Cast_Exception($sort, Integer);
      }
    }

    $path = Path::getAbsolutePath($path);

    if (Dir::exists($path) == FALSE) {
      throw new Directory_Not_Found_Exception("One or more parts of the path '" . $path . "' could not be found");
    }

    $searchPattern = trim($searchPattern);

    if (isset($cache[$path][$searchPattern][intval($recursive)][$sort]) == TRUE) {
      return $cache[$path][$searchPattern][intval($recursive)][$sort];
    }

    switch ($sort) {
      case Sort_Order::natural:
        $cache[$path][$searchPattern][intval($recursive)][$sort] = Sort::fileSystemAlphabetical(Dir::_getFilesInternal($path, $searchPattern, $recursive));
        return $cache[$path][$searchPattern][intval($recursive)][$sort];
      break;

      // Sorts as a filesystem would display the files
      case Sort_Order::regular:
        $cache[$path][$searchPattern][intval($recursive)][$sort] = Dir::_getFilesInternal($path, $searchPattern, $recursive);
        return $cache[$path][$searchPattern][intval($recursive)][$sort];
      break;

      case Sort_Order::inherit:
      default:
        $cache[$path][$searchPattern][intval($recursive)][$sort] = array_reverse(Dir::_getFilesInternal($path, $searchPattern, $recursive, Sort_Order::inherit));
        return $cache[$path][$searchPattern][intval($recursive)][$sort];
      break;
    }
  }

  /**
   * Internal helper function which returns a list of matching files
   * @link http://intranet/docs/framework/v3/Dir/_getFilesInternal/
   */
  static protected function _getFilesInternal ($path, $searchPattern, $recursive, $sort = NULL) {
    Assert::isString($path);
    Assert::isString($searchPattern);
    Assert::isBoolean($recursive);
    Assert::isInteger($sort, TRUE);

    $paths = array();
    $files = array();
    $cache = array();

    if (isset($cache[$path][$searchPattern][intval($recursive)][$sort])) {
      return $cache[$path][$searchPattern][intval($recursive)][$sort];
    }

    if (Path::isDirectorySeparator(substr($path, -1, 1)) == FALSE) {
      $path .= DIRECTORY_SEPARATOR;
    }

    if (isset(self::$_cache[$path]) == TRUE) {
      $directoryEntries = self::$_cache[$path];
    }
    else {
      $directoryEntries = @scandir($path);
      if (is_array($directoryEntries) == TRUE) {
        $directoryEntries = array_diff($directoryEntries, array('.', '..'));
      }
      self::$_cache[$path] = $directoryEntries;
    }

    if (is_array($directoryEntries) == FALSE || count($directoryEntries) < 1) {
      return array();
    }

    foreach ($directoryEntries as $entry) {
      try {
        $currentPath = $path . $entry;
        if (is_dir($currentPath) == TRUE) {
          $paths[] = $files[] = $currentPath . DIRECTORY_SEPARATOR;
        }
        elseif (is_file($currentPath) == TRUE && ($searchPattern == '' || preg_match($searchPattern, $entry) == TRUE || preg_match($searchPattern, $currentPath) == TRUE)) {
          $files[] = $currentPath;
        }
      }
      catch (Unauthorised_Access_Exception $ex) {
        // Ignore errors where we can't tell if the entry is a file or directory as we don't have access to the file/directory
      }
    }

    if (count($files) > 0) {
      $files = Sort::fileSystemAlphabetical($files);
      if ($sort == Sort_Order::inherit) {
        $files = array_reverse($files);
      }
    }

    if (count($paths) > 0) {
      if ($recursive == TRUE) {
        $files = array_diff($files, $paths);
        if ($sort != Sort_Order::inherit) {
          rsort($paths);
        }
        foreach ($paths as $path) {
          $files = array_merge(Dir::_getFilesInternal($path, $searchPattern, $recursive, $sort), $files);
        }
      }
      else {
        $files = array_diff($files, $paths);
      }
    }

    $cache[$path][$searchPattern][intval($recursive)][$sort] = $files;

    return $files;
  }

  /**
   * Returns an array of directories within the path specified
   * There is no need for a sort order as directories should always be returned in alphabetical order
   * @link http://intranet/docs/framework/v3/Dir/getDirectories/
   */
  static public function getDirectories ($path, $searchPattern = '', $recursive = FALSE) {
    Assert::isString($path);
    Assert::isString($searchPattern);
    Assert::isBoolean($recursive);

    static $cache = array();

    $path = Path::normalisePath($path);

    if ($path == '') {
      throw new InvalidArgumentException("Path cannot be empty");
    }

    $path = Path::getAbsolutePath($path);

    $searchPattern = trim($searchPattern);

    if (Dir::exists($path) == FALSE) {
      throw new Directory_Not_Found_Exception("One or more parts of the path '" . $path . "' could not be found");
    }

    if (isset($cache[$path][$searchPattern][intval($recursive)]) == TRUE) {
      return $cache[$path][$searchPattern][intval($recursive)];
    }

    $cache[$path][$searchPattern][intval($recursive)] = Sort::fileSystemAlphabetical(Dir::_getDirectoriesInternal($path, $searchPattern, $recursive));
    return $cache[$path][$searchPattern][intval($recursive)];
  }

  /**
   * Internal helper function which returns a list of matching directories
   * @link http://intranet/docs/framework/v3/Dir/_getDirectoriesInternal/
   */
  static protected function _getDirectoriesInternal ($path, $searchPattern, $recursive) {
    Assert::isString($path);
    Assert::isString($searchPattern);
    Assert::isBoolean($recursive);

    $paths = array();
    $files = array();
    $cache = array();

    if (isset($cache[$path][$searchPattern][intval($recursive)])) {
      return $cache[$path][$searchPattern][intval($recursive)];
    }

    if (isset(self::$_cache[$path]) == TRUE) {
      $directoryEntries = self::$_cache[$path];
    }
    else {
      $directoryEntries = @scandir($path);
      if (is_array($directoryEntries) == TRUE) {
        $directoryEntries = array_diff($directoryEntries, array('.', '..'));
      }
      self::$_cache[$path] = $directoryEntries;
    }

    if (is_array($directoryEntries) == FALSE || count($directoryEntries) < 1) {
      return array();
    }

    foreach ($directoryEntries as $entry) {
      try {
        $currentPath = $path . $entry;
        if (is_dir($currentPath) == TRUE && ($searchPattern == '' || preg_match($searchPattern, $entry) == TRUE || preg_match($searchPattern, $currentPath) == TRUE)) {
          $paths[] = $currentPath . DIRECTORY_SEPARATOR;
        }
      }
      catch (Unauthorised_Access_Exception $ex) {
        // Ignore errors where we can't tell if the entry is a file or directory as we don't have access to the file/directory
      }
    }

    if (count($paths) > 0 && $recursive == TRUE) {
      rsort($paths);
      foreach ($paths as $path) {
        $paths = array_merge(Dir::_getDirectoriesInternal($path, $searchPattern, $recursive), $paths);
      }
      rsort($paths);
    }

    $cache[$path][$searchPattern][intval($recursive)] = $paths;

    return $paths;
  }

  /**
   * Returns an array of files and directories within the path specified
   * @link http://intranet/docs/framework/v3/Dir/getFileSystemEntries/
   */
  static public function getFileSystemEntries ($path, $searchPattern = '', $recursive = FALSE, $sort = Sort_Order::inherit) {
    Assert::isString($path);
    Assert::isString($searchPattern);
    Assert::isBoolean($recursive);
    Assert::isInteger($sort, TRUE);

    static $cache = array();

    $path = Path::normalisePath($path);

    if ($path == '') {
      throw new InvalidArgumentException("Path cannot be empty");
    }

    if (Reflection_Enum::hasConstant('Sort_Order', $sort) == FALSE) {
      if (is_int($sort) == TRUE) {
        throw new OutOfRangeException("Sort value '" . $sort . "' is out of legal range");
      }
      else {
        throw new Invalid_Cast_Exception($sort, Integer);
      }
    }

    $path = Path::getAbsolutePath($path);

    if (Dir::exists($path) == FALSE) {
      throw new Directory_Not_Found_Exception("One or more parts of the path '" . $path . "' could not be found");
    }

    $searchPattern = trim($searchPattern);

    if (isset($cache[$path][$searchPattern][intval($recursive)][$sort]) == TRUE) {
      return $cache[$path][$searchPattern][intval($recursive)][$sort];
    }

    switch ($sort) {
      case Sort_Order::natural:
        $cache[$path][$searchPattern][intval($recursive)][$sort] = Sort::fileSystemAlphabetical(Dir::_getFileSystemEntriesInternal($path, $searchPattern, $recursive));
        return $cache[$path][$searchPattern][intval($recursive)][$sort];
      break;

      // Sorts as a filesystem would display the files
      case Sort_Order::regular:
        $cache[$path][$searchPattern][intval($recursive)][$sort] = Dir::_getFileSystemEntriesInternal($path, $searchPattern, $recursive);
        return $cache[$path][$searchPattern][intval($recursive)][$sort];
      break;

      // Sorts as a filesystem would search for the files
      case Sort_Order::inherit:
      default:
        $cache[$path][$searchPattern][intval($recursive)][$sort] = array_reverse(Dir::_getFileSystemEntriesInternal($path, $searchPattern, $recursive, Sort_Order::inherit));
        return $cache[$path][$searchPattern][intval($recursive)][$sort];
      break;
    }
  }

  /**
   * Internal helper function which returns a list of matching file system entries
   * @link http://intranet/docs/framework/v3/Dir/_getFileSystemEntriesInternal/
   */
  static protected function _getFileSystemEntriesInternal ($path, $searchPattern, $recursive, $sort = NULL) {
    Assert::isString($path);
    Assert::isString($searchPattern);
    Assert::isBoolean($recursive);
    Assert::isInteger($sort, TRUE);

    $paths = array();
    $files = array();
    $cache = array();

    if (isset($cache[$path][$searchPattern][intval($recursive)][$sort])) {
      return $cache[$path][$searchPattern][intval($recursive)][$sort];
    }

    if (isset(self::$_cache[$path]) == TRUE) {
      $directoryEntries = self::$_cache[$path];
    }
    else {
      $directoryEntries = @scandir($path);
      if (is_array($directoryEntries) == TRUE) {
        $directoryEntries = array_diff($directoryEntries, array('.', '..'));
      }
      self::$_cache[$path] = $directoryEntries;
    }

    if (is_array($directoryEntries) == FALSE || count($directoryEntries) < 1) {
      return array();
    }

    foreach ($directoryEntries as $entry) {
      try {
        $currentPath = $path . $entry;
        if (is_dir($path . $entry) == TRUE) {
          $paths[] = $path . $entry . DIRECTORY_SEPARATOR;
        }
        elseif (is_file($path . $entry) && ($searchPattern == '' || preg_match($searchPattern, $entry) == TRUE || preg_match($searchPattern, $path . $entry) == TRUE)) {
          $files[] = $path . $entry;
        }
      }
      catch (Unauthorised_Access_Exception $ex) {
        // Ignore errors where we can't tell if the entry is a file or directory as we don't have access to the file/directory
      }
    }

    // If we have files sort them into the right order to make them more readable
    if (count($files) > 0) {
      $files = Sort::fileSystemAlphabetical($files);
      if ($sort == Sort_Order::inherit) {
        $files = array_reverse($files);
      }
    }

    if (count($paths) > 0) {
      if ($recursive == TRUE) {
        if ($sort != Sort_Order::inherit) {
          rsort($paths);
        }
        foreach ($paths as $path) {
          $files = array_merge(Dir::_getFileSystemEntriesInternal($path, $searchPattern, $recursive, $sort), $files);
          if ($searchPattern == '' || preg_match($searchPattern, $path) == TRUE) {
            array_unshift($files, $path);
          }
        }
      }
      else {
        $files = array_merge(array_reverse($paths), $files);
      }
    }

    $cache[$path][$searchPattern][intval($recursive)][$sort] = $files;

    return $files;
  }

  /**
   * Creates all directories specified in the supplied path
   * @link http://intranet/docs/framework/v3/Dir/create/
   */
  static public function create ($path, $permissions = 0777) {
    Assert::isString($path);
    Assert::isInteger($permissions);

    $path = Path::normalisePath($path);

    if ($path == '') {
      throw new InvalidArgumentException("Path cannot be empty");
    }
    elseif ($path == '.' || $path == '..') {
      return;
    }
    // whereas a file name can contain just one or more dots, folder names can't as this could potentially break paths
    elseif (DIRECTORY_SEPARATOR == '\\' && preg_match('#^\.+$#', $path) == TRUE) {
      throw new InvalidArgumentException("File name '" . $path . "' is illegal");
    }

    $path = Path::getAbsolutePath($path);

    if (DIRECTORY_SEPARATOR == '\\' && preg_match('#^[a-z]\:$#i', trim($path, DIRECTORY_SEPARATOR . ALT_DIRECTORY_SEPARATOR)) == TRUE && Dir::exists($path) == FALSE) {
      throw new Directory_Not_Found_Exception("One or more parts of the path '" . $path . "' could not be found");
    }

    if (Dir::exists($path) == TRUE) {
      return;
    }

    if (file_exists($path) == TRUE) {
      throw File_System_Exception("A file or directory with the name '" . $path . "' already exists");
    }

    $lastError = error_get_last();

    @mkdir($path, $permissions, TRUE);

    if ($lastError !== error_get_last()) {
      throw new Unauthorised_Access_Exception("Access to path '" . $path . "' is denied"); // Handles open_basedir restrictions
    }

    if (Dir::exists($path) == FALSE) {
      throw new File_System_Exception("The directory '" . $path . "' cannot be created");
    }
  }

  /**
   * Deletes all directories and files within the specified path
   * @link http://intranet/docs/framework/v3/Dir/delete/
   */
  static public function delete ($path, $recursive = FALSE) {
    Assert::isString($path);
    Assert::isBoolean($recursive);

    $path = Path::normalisePath($path);

    if ($path == '') {
      throw new InvalidArgumentException("Path cannot be empty");
    }
    elseif ($path == '.' || $path == '..') {
      $path = Path::getAbsolutePath($path);
      throw new File_System_Exception("The directory '" . $path . "' cannot be deleted");
    }

    $path = Path::getAbsolutePath($path);

    if (Dir::exists($path) == FALSE) {
      throw new Directory_Not_Found_Exception("One or more parts of the path '" . $path . "' could not be found");
    }

    if ($recursive == FALSE) {
      $directoryEntryCount = count(Dir::getFileSystemEntries($path));

      if ($directoryEntryCount === 0) {
        $lastError = error_get_last();

        @rmdir($path);

        if ($lastError !== error_get_last()) {
          throw new Unauthorised_Access_Exception("Access to path '" . $path . "' is denied"); // Handles open_basedir restrictions
        }
      }
      else {
        throw new File_System_Exception("The directory '" . $path . "' is not empty");
      }
    }
    else {
      Dir::_deleteInternal($path);
    }

    if (Dir::exists($path) == TRUE) {
      throw new File_System_Exception("The directory '" . $path . "' cannot be deleted");
    }
  }

  /**
   * Internal helper function providing the ability to recursively delete a directory and all of it's
   * file system entries
   * @link http://intranet/docs/framework/v3/Dir/_deleteInternal/
   */
  static protected function _deleteInternal ($path) {
    Assert::isString($path);

    if (file_exists($path) == FALSE) {
      return;
    }

    // xxx is_link... File::isLink()
    if (File::exists($path) == TRUE || is_link($path) == TRUE) {
      $lastError = error_get_last();

      @unlink($path);

      if ($lastError !== error_get_last()) {
        throw new Unauthorised_Access_Exception("Access to path '" . $path . "' is denied"); // Handles open_basedir restrictions
      }

      return;
    }

    $directoryEntries = Dir::getFileSystemEntries($path);
    foreach ($directoryEntries as $directoryEntry) {
      Dir::_deleteInternal($directoryEntry);
    }

    $lastError = error_get_last();

    @rmdir($path);

    if ($lastError !== error_get_last()) {
      throw new Unauthorised_Access_Exception("Access to path '" . $path . "' is denied"); // Handles open_basedir restrictions
    }

    if (Dir::exists($path) == TRUE) {
      throw new File_System_Exception("The directory '" . $path . "' cannot be deleted");
    }
  }

  /**
   * Attempts to move the source path to the destination path
   * @link http://intranet/docs/framework/v3/Dir/move/
   */
  static public function move ($sourcePath, $destinationPath, $backupPath = '') {
    Assert::isString($sourcePath);
    Assert::isString($destinationPath);
    Assert::isString($backupPath);

    $sourcePath = Path::normalisePath($sourcePath);
    $destinationPath = Path::normalisePath($destinationPath);
    $backupPath = Path::normalisePath($backupPath);

    if ($sourcePath == '') {
      throw new InvalidArgumentException("Source path cannot be empty");
    }
    elseif ($destinationPath == '') {
      throw new InvalidArgumentException("Destination path cannot be empty");
    }

    if ($sourcePath == '.' || $sourcePath == '..') {
      $sourcePath = Path::getAbsolutePath($sourcePath);
      throw new File_System_Exception("The directory '" . $sourcePath . "' cannot be moved");
    }

    $sourcePath = Path::getAbsolutePath($sourcePath);
    $destinationPath = Path::getAbsolutePath($destinationPath);

    if ($sourcePath == $destinationPath) {
      throw new InvalidArgumentException("Source and destination path must be different");
    }

    if (Dir::exists($sourcePath) == FALSE) {
      throw new Directory_Not_Found_Exception("One or more parts of the path '" . $sourcePath . "' could not be found");
    }

   if (file_exists($destinationPath) == TRUE) {
      throw new File_System_Exception("Destination path '" . $destinationPath . "' already exists");
    }

    if ($backupPath != '') {
      if ($backupPath == '.' || $backupPath == '..') {
        throw new InvalidArgumentException;
      }

      $backupPath = Path::getAbsolutePath($backupPath);

      if ($backupPath == $sourcePath) {
        throw new InvalidArgumentException("Backup path '" . $backupPath . "' must be different to source path '" . $sourcePath . "'");
      }
      elseif ($backupPath == $destinationPath) {
        throw new InvalidArgumentException("Backup path '" . $backupPath . "' must be different to destination path '" . $destinationPath . "'");
      }

      if (file_exists($backupPath) == TRUE) {
        throw new File_System_Exception("Backup path '" . $backupPath . "' already exists");
      }

      Dir::copy($sourcePath, $backupPath);
    }

    if (@rename($sourcePath, $destinationPath) == FALSE) {
      Dir::copy($sourcePath, $backupPath);
    }

    if (Dir::exists($destinationPath) == FALSE) {
      throw new File_System_Exception("The directory '" . $sourcePath . "' cannot be moved");
    }

    if (Dir::exists($sourcePath) == TRUE) {
      Dir::delete($sourcePath);
    }

    if ($backupPath != '' && Dir::exists($backupPath) == TRUE) {
      Dir::delete($backupPath, TRUE);
    }
  }

  /**
   * Alias of move
   * @link http://intranet/docs/framework/v3/Dir/rename/
   */
  static public function rename ($sourcePath, $destinationPath) {
    return Directory::move($sourcePath, $destinationPath);
  }

  /**
   * Make a copy of a directory
   * @link http://intranet/docs/framework/v3/Dir/copy/
   */
  static public function copy ($sourcePath, $destinationPath, $overwrite = FALSE) {
    Assert::isString($sourcePath);
    Assert::isString($destinationPath);
    Assert::isBoolean($overwrite);

    $sourcePath = Path::normalisePath($sourcePath);
    $destinationPath = Path::normalisePath($destinationPath);

    if ($sourcePath == '') {
      throw new InvalidArgumentException("Source path cannot be empty");
    }
    elseif ($destinationPath == '') {
      throw new InvalidArgumentException("Destination path cannot be empty");
    }

    $sourcePath = Path::getAbsolutePath($sourcePath);
    $destinationPath = Path::getAbsolutePath($destinationPath);

    if ($sourcePath == $destinationPath) {
      throw new InvalidArgumentException("Source path '" . $sourcePath . "' and destination path '" . $destinationPath . "' must be different");
    }

    if (Dir::exists($sourcePath) == FALSE) {
      throw new Directory_Not_Found_Exception("One or more parts of the path '" . $sourcePath . "' could not be found");
    }

    if (Dir::exists($destinationPath) == FALSE && file_exists($destinationPath) == TRUE && $overwrite == FALSE) {
      throw new File_System_Exception("Destination path '" . $destinationPath . "' already exists");
    }

    Dir::_copyInternal($sourcePath, $destinationPath, $overwrite);
  }

  /**
   * Internal helper function to copy files and directories
   * @link http://intranet/docs/framework/v3/Dir/_copyInternal/
   */
  static protected function _copyInternal ($sourcePath, $destinationPath, $overwrite) {
    Assert::isString($sourcePath);
    Assert::isString($destinationPath);
    Assert::isBoolean($overwrite);

    if (Dir::exists($sourcePath) == TRUE) {
      if (File::exists($destinationPath) == TRUE) {
        if ($overwrite == FALSE) {
          throw new File_System_Exception("Destination path '" . $destinationPath . "' already exists");
        }
        else {
          File::delete($destinationPath);
          Dir::create($destinationPath);
        }
      }
      elseif (Dir::exists($destinationPath) == FALSE) {
        Dir::create($destinationPath);
      }

      foreach (Dir::getFileSystemEntries($sourcePath) as $directoryEntry) {
        if (Path::isDirectorySeparator(substr($sourcePath, -1, 1)) == FALSE) {
          $sourcePath .= DIRECTORY_SEPARATOR;
        }

        if (Path::isDirectorySeparator(substr($destinationPath, -1, 1)) == FALSE) {
          $destinationPath .= DIRECTORY_SEPARATOR;
        }

        $fileName = basename($directoryEntry);

        Dir::_copyInternal($sourcePath . $fileName, $destinationPath . $fileName, $overwrite);
      }
    }
    elseif (File::exists($sourcePath) == TRUE) {
      if (File::exists($destinationPath) == TRUE) {
        if ($overwrite == FALSE) {
          throw new File_System_Exception("Destination path '" . $destinationPath . "' already exists");
        }
        else {
          File::delete($destinationPath);
        }
      }
      elseif (Dir::exists($destinationPath) == TRUE) {
        Dir::delete($destinationPath);
      }

      @copy($sourcePath, $destinationPath);

      if (File::exists($destinationPath) == FALSE) {
        throw new File_System_Exception("The file '" . $sourcePath . "' cannot be copied");
      }
    }
  }

  /************ xxx test the following 2 functions (see test25) ************/

  static public function getLastChangeTime ($path) {
    Assert::isString($path);

    $path = Path::normalisePath($path);

    if ($path == '') {
      throw new InvalidArgumentException("Path cannot be empty");
    }

    $path = Path::getAbsolutePath($path);

   if (Dir::exists($path) == FALSE) {
      throw new Directory_Not_Found_Exception("One or more parts of the path '" . $path . "' could not be found");
    }

    return filectime($path);
  }

  /**
   * Alias for Windows as on Windows filectime returns creation time instead of change time
   * @link http://intranet/docs/framework/v3/Dir/getCreationTime/
   */
  static public function getCreationTime ($path) {
    return Dir::getLastChangeTime($path);
  }

  /**
   * Returns the time when the data blocks of a file were being written to within the directory
   * @link http://intranet/docs/framework/v3/Dir/getLastWriteTime/
   */
  static public function getLastWriteTime ($path) {
    Assert::isString($path);

    $path = Path::normalisePath($path);

    if ($path == '') {
      throw new InvalidArgumentException("Path cannot be empty");
    }

    $path = Path::getAbsolutePath($path);

   if (Dir::exists($path) == FALSE) {
      throw new Directory_Not_Found_Exception("One or more parts of the path '" . $path . "' could not be found");
    }

    $time = filemtime($path);

    // If Win* we need to make sure the time is correct
    if (DIRECTORY_SEPARATOR == '\\') {
      $isDST = date('I', $time);
      $systemDST = date('I');

      if ($isDST == 0 && $systemDST == 1) {
        $time += 3600;
      }
      elseif ($isDST == 1 && $systemDST == 0) {
        $time -= 3600;
      }
    }

    return $time;
  }

  /**
   * Gets last access time of the files within the directory
   * @link http://intranet/docs/framework/v3/Dir/getLastAccessTime/
   */
  static public function getLastAccessTime ($path) {
    Assert::isString($path);

    $path = Path::normalisePath($path);

    if ($path == '') {
      throw new InvalidArgumentException("Path cannot be empty");
    }

    $path = Path::getAbsolutePath($path);

   if (Dir::exists($path) == FALSE) {
      throw new Directory_Not_Found_Exception("One or more parts of the path '" . $path . "' could not be found");
    }

    return intval(fileatime($path));
  }

  /************ xxx test the following 2 functions (see test25) ************/

  static public function setLastWriteTime ($path) {
    Assert::isString($path);

    $path = Path::normalisePath($path);

    if ($path == '') {
      throw new InvalidArgumentException("Path cannot be empty");
    }

    $path = Path::getAbsolutePath($path);

   if (Dir::Exists($path) == FALSE) {
      throw new Directory_Not_Found_Exception("One or more parts of the path '" . $path . "' could not be found");
    }

    $tempWriteFile = tempnam($path, 'setLastWriteTime_');
    if ($tempWriteFile != FALSE) {
      if (preg_match('#^' . preg_quote($path, '#') . '#i', $tempWriteFile) == TRUE) {
        if (unlink($tempWriteFile) == TRUE) {
          clearstatcache();
          return TRUE;
        }
      }
      else {
        unlink($tempWriteFile);
      }
    }

    return FALSE;
  }

  static public function setLastAccessTime ($path) {
    Assert::isString($path);

    $path = Path::normalisePath($path);

    if ($path == '') {
      throw new InvalidArgumentException("Path cannot be empty");
    }

    $path = Path::getAbsolutePath($path);

   if (Dir::exists($path) == FALSE) {
      throw new Directory_Not_Found_Exception("One or more parts of the path '" . $path . "' could not be found");
    }

    // xxx touch then clearstatcache
  }

  /**
   * Gets the current working directory
   * Note: windows returns D:\inetpub\wwwroot\attendance for the cwd or something similar
   * @link http://intranet/docs/framework/v3/Dir/getCurrentDirectory/
   */
  static public function getCurrentDirectory () {
    return getcwd() . DIRECTORY_SEPARATOR;
  }

  /**
   * Gets the parent directory of the specified path
   * Returns the parent path or an empty string if the path has no parent
   * @link http://intranet/docs/framework/v3/Dir/getParentDirectory/
   */
  static public function getParentDirectory ($path = '.') {
    Assert::isString($path);

    $path = Path::normalisePath($path);

    if ($path == '') {
      throw new InvalidArgumentException("Path cannot be empty");
    }

    $path = Path::getAbsolutePath($path);

    if (Path::isDirectorySeparator(substr($path, -1, 1)) == TRUE) {
      return Path::getAbsolutePath($path . '../');
    }
    else {
      return Path::getAbsolutePath($path . '/../');
    }
  }

  /**
   * Gets the volume information, root directory or share that the path resides in
   * @link http://intranet/docs/framework/v3/Dir/getRootDirectory/
   */
  static public function getRootDirectory ($path = '.') {
    Assert::isString($path);
    return Path::getPathRoot($path);
  }
}

?>