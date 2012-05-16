<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class File_System_Folder extends Enum {
  const config = 'config';
  const lang = 'i18n';
  const controllers = 'controllers';
  const models = 'models';
  const views = 'views';
  const library = 'library';
}

?>