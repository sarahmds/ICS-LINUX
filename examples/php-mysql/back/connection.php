<?php
require __DIR__ . '/config.php';

$db = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName)
    or die('Unable to connect. Check your connection parameters.');

mysqli_set_charset($db, 'utf8');
?>
