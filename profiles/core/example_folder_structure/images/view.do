<?php

if (!defined('ROOT')) {
  $current = $_SERVER['SCRIPT_NAME'];
  $folders = count(explode('/', $current));

  $root = '';
  for ($i = 2; $i < $folders; ++$i) $root .= '../';
  define('ROOT', $root);
}

#Region "imports"
require_once(ROOT . 'framework/data/sql_client.php');
require_once(ROOT . 'framework/data.php');
require_once(ROOT . 'framework/image.php');
require_once(ROOT . 'framework/network/request.php');
#End Region

$request = new Request;
$db = new Database;
$ds = Data_Set;
$row = Data_Row;

$filename = trim($request->queryString());
$filename = array_shift(explode('&', $filename));
$filename = preg_replace('/[^a-z0-9\-_]/i', '', $filename);

$sp = new Stored_Procedure('news_get_image');
$sp->addParameter('@public_filename', $filename, $db->type['nvarchar']);
$ds = $db->execute($sp);

$row = $ds->table(0)->row(0);

$image = new Image;
$image->determineMimeType($row->mime_type);
$image->load($row->local_filename);
$image->show();

?>