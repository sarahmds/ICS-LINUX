
<html>
	<?php
	include('connection.php');
	?>   

<body>

	<?php
	$fname = $_POST['firstname'];
	$lname = $_POST['lastname'];
	$mname = $_POST['Middlename'];
	$address = $_POST['Address'];
	$contct = $_POST['Contact'];
	$comment = $_POST['comment'];
				
	$query = "INSERT INTO people
			(people_id,first_name, last_name, mid_name, address,contact, comment)
			VALUES (NULL,'".$fname."','".$lname."','".$mname."','".$address."','$contct','".$comment."')";
	mysqli_query($db,$query) or die ('Error in Database: '.mysqli_error($db));
					
	?>
	
	<script type="text/javascript">
		alert("Successfully added.");
		window.location = "index.php";
	</script>

</body>

</html>

