<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

if (DIRECTORY_SEPARATOR == '/') {
  echo "\033[0;31m[Error] \033[0m{$message} on line {$line} of {$file}\r\n";
}
else {
  echo "[Error] {$message} on line {$line} of {$file}\r\n";
}

?>