<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>Log In</title>
    <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
    <link href="/themes/admin/css/login.css" rel="stylesheet" type="text/css" />
    <!-- xxx include files using controls -->
    <script type="text/javascript" src="/misc/scripts/jquery.min.js"></script>
    <script type="text/javascript" src="/themes/admin/js/centre.js"></script>
  </head>
  <body>
    <div id="content">
      <!-- xxx logo should be dynamic -->
      <h1 class="logo"><img src="/themes/admin/images/login-user.png" alt="Log In" /></h1>
      <?php if (count($errors = Cmf_Flash_Message::getMessages('error'))): ?>
        <div class="error">
          <ul>
            <?php foreach ($errors as $error): ?>
              <li><?php echo $error; ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
      <div class="column extended">
        <!-- xxx form should be built fully dynamically -->
        <form method="post" action="">
          <fieldset>
            <div class="security">
            <label>Security:</label>
              <!-- xxx if shared is selected, sessions shouldn't be kept alive -->
              <p class="radio"><input type="radio" name="security" value="shared" checked="checked" /> This is a public or shared computer</p>
              <p class="radio"><input type="radio" name="security" value="private" /> This is a private computer</p>
            </div>
            <label for="username">User name:</label> <?php echo View_Data::getValue('username_control'); ?><br />
            <label for="password">Password:</label> <?php echo View_Data::getValue('password_control'); ?><br />
            <input type="submit" name="action" id="submit" value="Log in" />
          </fieldset>
        </form>
      </div>
    </div>
    <p class="footer">Your activity is being monitored and recorded for security purposes.</p>
  </body>
</html>