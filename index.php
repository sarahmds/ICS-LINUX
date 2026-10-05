
<?php
       include('header.php');
       include('connection.php');
?>
<body>

	<h1 class="page-header">
		PHP CRUD With MySQLi Datatable
	</h1>
	
	<h2>List of Records</h2>
	<br/>
	<a href="add.php">Add New (CREATE)</a>
	<br/><br/>
	<table border="1"> 
		<thead>
			<tr>
				<th>First Name</th>
				<th>Last Name</th>
				<th>Address</th>
				<th>Contact</th>
				<th>Options</th>
			</tr>
		</thead>
		<tbody>
			<?php                  
			$query = 'SELECT * FROM people';
			$result = mysqli_query($db, $query) or die (mysqli_error($db));

			while ($row = mysqli_fetch_assoc($result)) {
                                
				echo '<tr>';
					echo '<td>'. $row['first_name'].'</td>';
					echo '<td>'. $row['last_name'].'</td>';
					echo '<td>'. $row['address'].'</td>';
					echo '<td>'. $row['contact'].'</td>';
					echo '<td>';
						echo '<a href="detail.php?id='.$row['people_id'].'" >Detail (READ)</a><br/>';
						echo '<a href="edit.php?id='.$row['people_id'].'">Edit (UPDATE)</a><br/>';
						echo '<a href="remove.php?id='.$row['people_id'].'">Remove (DELETE)</a>';
					echo '</td>';
				echo '</tr> ';
			}
			?> 
                                    
		</tbody>
	</table>

</body>

</html>
