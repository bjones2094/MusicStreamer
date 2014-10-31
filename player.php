<?php
	
	if(!isset($_SESSION["username"])) {
		// Username not set/no one logged in
	}
	else {
		$username = $_SESSION["username"];	// $username can be used to identify who is logged in
	}

?>
