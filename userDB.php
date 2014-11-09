<?php
	ini_set('display_errors',1);
	error_reporting(E_ALL);


	$connect = new mysqli("127.0.0.1", "root", "A2!y123Sql", "music_db");
	
	if(mysqli_connect_error()) {
		print("bad connect");
	}
	
	$stmt = $connect->prepare("SELECT * FROM users");
	$stmt->execute();
	
	$results = $stmt->get_result();
	
	while($row = $results->fetch_array()) {
		print("<b>ID:</b> " . $row["id"]);
		print("&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp");
		print("<b>Username:</b> " . $row["username"]);
		print("&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp");
		print("<b>Password:</b> " . $row["password"]);
		print("<br><br>");
	}

?>
