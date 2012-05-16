<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class File_Attribute {
  // mime type of file
  public $mimeType = '';

  // size of uploaded file in bytes
  public $size = 0;

  // dimensions
  public $width = 0;
  public $height = 0;
}

?>