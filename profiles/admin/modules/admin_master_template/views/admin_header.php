<?php

/**
 * Copyright (c) 2011 Ape Web Ltd.  All rights reserved.
 *
 * The use and distribution terms for this software are contained in the file
 * named license.txt, which can be found in the root of this distribution.
 * By using this software in any fashion, you are agreeing to be bound by the
 * terms of this license.
 *
 * You must not remove this notice, or any other, from this software.
 */

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

  <div class="text_links">
    <div class="menu_text">
      <?= View_Data::getValue('text_links'); ?>
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
