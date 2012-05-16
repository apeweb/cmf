<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Cmf_File_Storage {
  public static function install () {
    // Stored in the DB so that the paths can be changed easily
    Config::setValue(CMF_REGISTRY, 'site', 'storage', 'temp', 'path', Cmf_Settings::getSiteSettingsDirectory() . 'temp' . DIRECTORY_SEPARATOR);
    Config::setValue(CMF_REGISTRY, 'site', 'storage', 'files', 'path', Cmf_Settings::getSiteSettingsDirectory() . 'files' . DIRECTORY_SEPARATOR);
  }

  /**
   * @param int $maxFileAge age of files before being removed (in seconds)
   */
  public static function purgeTempFiles ($maxFileAge = 5) {
    $maxFileAge = 5 * 3600;
    $tempPath = Config::getValue('site', 'storage', 'temp', 'path');

    if (is_dir($tempPath) == FALSE) {
      // xxx throw exception
    }

    $dir = opendir($tempPath);

    if ($dir == FALSE) {
      // xxx throw exception
    }

    while (($tempFileName = readdir($dir)) !== FALSE) {
      $tempFilePath = $tempPath . $tempFileName;

      // Remove temp file if it is older than the max age and is not the current file
      if (preg_match('/\.part$/', $tempFileName) && (filemtime($tempFilePath) < time() - $maxFileAge)) {
        @unlink($tempFilePath);
      }
    }

    closedir($dir);
  }

  // xxx provide an admin option for changing the temp path
}

?>