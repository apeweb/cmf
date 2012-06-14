<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>Bad Request</title>
    <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
  </head>
  <body>
    <h1>Bad Request</h1>
    <p>
      The request cannot be fulfilled due to the URI provided.
    </p>
    <?php if (function_exists('xdebug_time_index')): ?>
      <p>Time taken to load page: <?php echo xdebug_time_index(); ?></p>
    <?php endif; ?>
  </body>
</html>