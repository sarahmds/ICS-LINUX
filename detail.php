
<?php
       include('connection.php');
       include('header.php');
?>  
<body>

	<h1 class="page-header">
		PHP CRUD <small>Create, Read, Update and Delete</small>
	</h1>

	<?php 
	$query = 'SELECT * FROM people WHERE people_id ='.$_GET['id'];
	$result = mysqli_query($db, $query) or die(mysqli_error($db));
	
	while($row = mysqli_fetch_array($result)) {

		$id = $row['people_id'];
                $first_name = $row['first_name'];
                $last_name = $row['last_name'];
                $mid_name = $row['mid_name'];
                $address = $row['address'];
                $contact = $row['contact'];
                $comment = $row['comment'];
             
  	}
              
	?>

	<h2>Detailed Record</h2>
	<table>
		<tbody>
			<tr>
				<td><strong>Id:</strong></td>
				<td><?php echo $id; ?></td>
			</tr>
			<tr>
				<td><strong>First Name:</strong></td>
				<td><?php echo $first_name; ?></td>
			</tr>
			<tr>
				<td><strong>Middle Name:</strong></td>
				<td><?php echo $mid_name; ?></td>
			</tr>
			<tr>
				<td><strong>Last Name:</strong></td>
				<td><?php echo $last_name; ?></td>
			</tr>
			<tr>
				<td><strong>Address:</strong></td>
				<td><?php echo $address; ?></td>
			</tr>
			<tr>
				<td><strong>Contact:</strong></td>
				<td><?php echo $contact; ?></td>
			</tr>
			<tr>
				<td><strong>Comment:</strong></td>
				<td><?php echo $comment; ?></td>
			</tr>
		</tbody>
	</table>
	<a href="index.php">Return</a></td>

</body>

</html>

