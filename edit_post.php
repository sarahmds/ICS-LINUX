<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	</head>

	<body>
		<?php
		$id = $_POST['id'];
		$fname = $_POST['firstname'];
		$lname = $_POST['lastname'];
		$mname = $_POST['Middlename'];
		$address = $_POST['Address'];
		$contct = $_POST['Contact'];
		$comment = $_POST['comment'];
		
		include('connection.php');
		
		$query = 'UPDATE people set first_name ="'.$fname.'",
				last_name ="'.$lname.'", mid_name="'.$mname.'",
				address="'.$address.'",contact='.$contct.', 
				comment="'.$comment.'" WHERE
				people_id ="'.$id.'"';
		$result = mysqli_query($db, $query) or die(mysqli_error($db));
							
		?>	

		<script type="text/javascript">
			alert("Update Successfull.");
			window.location = "index.php";
		</script>
	</body>
</html>
