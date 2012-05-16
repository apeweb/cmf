<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class File_Mode extends Enum {
  const append = 1;
  const create = 2;
  const createNew = 3;
  const open = 4;
  const openOrCreate = 5;
  const truncate = 6;
}

?>