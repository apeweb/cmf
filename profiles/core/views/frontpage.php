<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
    <title><?php echo Cmf_Page::get(Cmf_Page::title); ?></title>
    <style type="text/css">
    </style>
  </head>
  <body>
    <h1>Welcome To Your New Website</h1>
    <p>If you can see this message then your website has installed successfully.</p>
    <h2>What Next?</h2>
    <p>The next step is to configure your website to work how you want it to.</p>
  </body>
</html>
