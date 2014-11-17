<?php

	$connect = new mysqli("127.0.0.1", "root", "A2!y123Sql", "music_db");
	
	if(mysqli_connect_error()) {
		print("bad connect");
	}
	
	$stmt = $connect->prepare("SELECT * FROM music");
	
	if(!$stmt) {
		print("Error");
	}
	
	$stmt->execute();
	
	$results = $stmt->get_result();
	
	while($row = $results->fetch_array()) {
		print("<b>ID:</b> " . $row["id"]);
		print("&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp");
		
		print("<b>Filename:</b> " . $row["file_name"]);
		print("&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp");
		
		print("<b>Title:</b> " . $row["title"]);
		print("&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp");
		
		print("<b>Artist:</b> " . $row["artist"]);
		print("&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp");
		
		print("<b>Album:</b> " . $row["album"]);
		print("&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp");
		
		print("<b>Owner:</b> " . $row["owner"]);
		print("&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp");
		
		print("<br><br>");
	}

?>
