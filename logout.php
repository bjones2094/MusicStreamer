
<?php

	session_start(); 
	/* Strictly used for logging out of the site */
	
	session_destroy();
	header("Location: login.php");
	
?>
