<?php

if (!defined('ROOT')) {
  $current = $_SERVER['SCRIPT_NAME'];
  $folders = count(explode('/', $current));

  $root = '';
  for ($i = 2; $i < $folders; ++$i) $root .= '../';
  define('ROOT', $root);
}

require_once(ROOT . 'classes/securimage/securimage_show.php');

?>