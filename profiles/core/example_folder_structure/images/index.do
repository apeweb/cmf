<?php

header("HTTP/1.0 404 Not Found", TRUE);

if (!defined('ROOT')) {
  $current = $_SERVER['SCRIPT_NAME'];
  $folders = count(explode('/', $current));

  $root = '';
  for ($i = 2; $i < $folders; ++$i) $root .= '../';
  define('ROOT', $root);
}

require_once(ROOT . 'functions/global.php');

publish('page_not_found');

?>