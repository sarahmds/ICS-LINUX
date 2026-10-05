
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

		$id= $row['people_id'];
		$first_name= $row['first_name'];
		$last_name=$row['last_name'];
		$mid_name=$row['mid_name'];
		$address=$row['address'];
		$contact=$row['contact'];
		$comment=$row['comment'];
	}
              
	?>

	<h2>Edit Record</h2>

	<form method="post" action="edit_post.php">
                            
	    	<input type="hidden" name="id" value="<?php echo $id; ?>" />
		<input placeholder="First Name" name="firstname" value="<?php echo $first_name; ?>"><br/><br/>
		<input placeholder="Last Name" name="lastname" value="<?php echo $last_name; ?>"><br/><br/>
		<input placeholder="Middle Name" name="Middlename" value="<?php echo $mid_name; ?>"><br/><br/>
		<input placeholder="Address" name="Address" value="<?php echo $address; ?>"><br/><br/>
		<input placeholder="Contact" name="Contact" value="<?php echo $contact; ?>"><br/><br/>
		<label>Comment:</label><br/>
		<textarea name="comment"><?php echo $comment; ?></textarea>
		<button type="submit" >Update Record</button>
		<a href="index.php">Return</a>

	</form>  
</body>

</html>

