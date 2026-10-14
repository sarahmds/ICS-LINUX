<?php

define('APP_ROOT', __DIR__);
define('IMAGES_DIR', APP_ROOT . '/imgs');
define('IMAGES_URL', '/imgs');

$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') ?: '';
$dbName = getenv('DB_NAME') ?: 'peopledb';

?>
