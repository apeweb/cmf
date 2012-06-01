<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

/**
 * USE THIS FILE ONLY FOR SETTING THE DEFAULT DATA SOURCE SETTINGS AND
 * SETTINGS THAT ONLY PROGRAMMERS SHOULD CHANGE, ALL OTHER SETTINGS
 * SHOULD BE STORED IN THE DEFAULT DATA SOURCE REGISTRY
 */
$settings = array(
  'databases' => array(
    'default' =>
    array(
      'dsn' => 'mysql:host=localhost;dbname=cmf',
      'username' => 'root',
      'password' => 'root',
      'options' =>
      array(
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
      )
    )
  ),
  'site' => array(
    /**
     * Is the website in production (live) or not, if in production assertions don't run and various other aspects
     * of the website change such as the error reporting and logging
     */
    'inProduction' => FALSE,
    /**
     * If multiple websites need to share the same database configure this value so that
     * each website has a unique value, valid values range from 1 to 9, by default the value
     * used here and in the database is 1
     */
    'id' => 2,

    /**
     * Site security settings
     */
    'security' => array(
      /**
       * The service account cannot be deleted and users can't modify the service account's permissions, this is to
       * ensure the service account cannot be locked out by a rogue user
       */
      'serviceAccountUserId' => 1,
    )
  ),
);

return $settings;

?>