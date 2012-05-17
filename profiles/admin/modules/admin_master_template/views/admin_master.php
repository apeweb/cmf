<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
    <?= View_Data::getValue('head', 'seo'); ?>
    <title><?= View_Data::getValue('page', 'title'); ?></title>
    <?= View_Data::getValue('head', 'meta'); ?>
    <?= View_Data::getValue('head', 'css'); ?>
    <?= View_Data::getValue('head', 'js'); ?>
  </head>
  <body>
    <?= View_Data::getValue('header'); ?>
    <?= View_Data::getValue('content'); ?>
    <?= View_Data::getValue('footer'); ?>
    <?= View_Data::getValue('body', 'js'); ?>
  </body>
</html>