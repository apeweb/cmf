<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Cmf_Frontpage {
  /**
   * 
 // if a path is set, ie /node/12/
 if (!empty($_GET['q'])) {
    $_GET['q'] = drupal_get_normal_path($_GET['q']);
  }
  // if a path isn't set, get the site_frontpage from the db or use 'node'
  else {
    $_GET['q'] = drupal_get_normal_path(variable_get('site_frontpage', 'node'));
  }
   */

  // xxx control panel for saying what page is the front page
  // xxx hook into route to specify the homepage
  // xxx homepage value is stored in registry
  // xxx make sure when a specific page is the front page that it isn't accessible
}

?>
