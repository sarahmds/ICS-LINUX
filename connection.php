<?php
	$db = mysqli_connect('192.168.100.20', 'user_peopledb', 'ifrn') or die ('Unable to connect. Check your connection parameters.');
	mysqli_select_db($db, 'peopledb' ) or die(mysqli_error($db));
?>
