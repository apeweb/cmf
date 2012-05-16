<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Sort {
  //Sorts paths and files based on how a human would expect to see them
  static public function fileSystemAlphabetical ($paths) {
    array_walk($paths, create_function('&$path','$path = str_replace("_", "/*", $path);'));
    natcasesort($paths);
    $paths = array_merge(array(), $paths); // resets array keys
    array_walk($paths, create_function('&$path','$path = str_replace("/*", "_", $path);'));
    return $paths;
  }
}

?>