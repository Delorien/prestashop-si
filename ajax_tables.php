<?php
require_once(dirname(__FILE__).'../../../config/config.inc.php');
require_once(dirname(__FILE__).'../../../init.php');

$error = 'error';

$table = Tools::getValue('table');

if (empty($table)) {
	echo(http_response_code(404));
	exit;
}

$prefix = '';

if (strpos($table, _DB_PREFIX_) === false) {
	$prefix = _DB_PREFIX_;
}

$sql = 'DESCRIBE ' . $prefix . $table;

$db = Db::getInstance();
$results = Db::getInstance()->ExecuteS($sql);

if (!$results) {
	echo(http_response_code(404));
	exit;
}

$columns = array();

foreach ($results as $row) {
	array_push($columns, $row['Field']);
}

if ($columns) {
	echo(Tools::jsonEncode($columns));
	exit;
}

echo(http_response_code(404));
exit;
