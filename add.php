
<?php
       
       include('connection.php');
       include('header.php');
       
?>  
<body>
	<h1 class="page-header">
		PHP CRUD <small>Create, Read, Update and Delete</small>
	</h1>

	<h2>Add New Record</h2>
	<br/>
	<form method="post" action="add_post.php">
                            
		<input placeholder="First Name" name="firstname"><br/><br/>
		<input placeholder="Middle Name" name="Middlename"><br/><br/>
		<input placeholder="Last Name" name="lastname"><br/><br/>
		<input placeholder="Address" name="Address"><br/><br/>
		<input placeholder="Contact" name="Contact"><br/><br/>
		<label>Comment:</label><br/>
		<textarea name="comment"></textarea>
		<br/><br/>
		<button type="submit" class="btn btn-success">Save Record</button>&nbsp;
		<button type="reset" class="btn btn-warning">Clear Entry</button>&nbsp;
		<a class="btn btn-info" href="index.php">Return</a>

	</form>  
</body>

</html>

