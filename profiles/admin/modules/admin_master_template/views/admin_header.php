<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

?>
<div class="header">
  <a id="top"></a>

  <div class="logo">
    <?= View_Data::getValue('logo'); ?>
  </div>

  <div class="system_information">
    <div class="system_links">
      <?= View_Data::getValue('system_links'); ?>
    </div>
    <div class="session_information">
      <?= View_Data::getValue('session_information'); ?>
    </div>
  </div>

  <div class="navigation_bar">
    <?= View_Data::getValue('navigation_bar'); ?>
  </div>

  <?= View_Data::getValue('navigation_search'); ?>
</div>
