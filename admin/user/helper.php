




<?php
	$con = mysqli_connect("localhost", "root", "", "rucu_discussion_forum_db"); 

	$val = $_GET['value'];
	$val_M= mysqli_real_escape_string($con, $val);
	$res = mysqli_query($con, "SELECT * FROM faculty WHERE program_short_name = '$val'");
	if (mysqli_num_rows($res) > 0) {
		echo "<select>";
		while ($row = mysqli_fetch_assoc($res)) {
			echo "<option>".$row['faculty_name']."</option>";
		}
		echo "</select>";
	}
	else{
		echo "<select>
				<option>Select Here</option>
				</select>";
	}


?>