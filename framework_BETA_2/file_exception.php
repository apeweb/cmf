<?php

// Although this file is in the exception folder, the folder could contain many exceptions so do not rename!!!

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

echo 'Deprecated file: ' . __FILE__;
Response::end();

class File_Exception extends Base_Exception {
  protected $message = 'File error';

  public function __construct ($filename) {
    $this->message .= ': ' . $filename;
  }
}

define('File_Open_Exception', 'File_Open_Exception');
class File_Open_Exception extends File_Exception {
  protected $message = 'File open error';
}

define('File_Mode_Exception', 'File_Mode_Exception');
class File_Mode_Exception extends File_Exception {
  protected $message = 'Incorrect file mode';
}

define('File_Access_Exception', 'File_Access_Exception');
class File_Access_Exception extends File_Exception {
  protected $message = 'Incorrect file access';
}

define('File_Upload_Error_Exception', 'File_Upload_Error_Exception');
class File_Upload_Error_Exception extends File_Exception {
  protected $message = 'File upload error';
}

define('Directory_Not_Writable_Exception', 'Directory_Not_Writable_Exception');
class Directory_Not_Writable_Exception extends File_Exception {
  protected $message = 'Directory not writable';
}

define('File_Not_Writable_Exception', 'File_Not_Writable_Exception');
class File_Not_Writable_Exception extends File_Exception {
  protected $message = 'File not writable';
}

define('File_Write_Failed_Exception', 'File_Write_Failed_Exception');
class File_Write_Failed_Exception extends File_Exception {
  protected $message = 'File write failed';
}

define('File_Not_Found_Exception', 'File_Not_Found_Exception');
class File_Not_Found_Exception extends File_Exception {
  protected $message = 'File not found: ';
}

define('File_Not_Readable_Exception', 'File_Not_Readable_Exception');
class File_Not_Readable_Exception extends File_Exception {
  protected $message = 'File not readable';
}

define('File_Locked_Exception', 'File_Locked_Exception');
class File_Locked_Exception extends File_Exception {
  protected $message = 'File locked';
}

?>