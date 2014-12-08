<?php

	// Used to download a file. This page should be loaded in a separate window to prevent
	// playback of the the player stopping
	$dir = "/MusicFiles/";
	$file = ($dir . $_GET['filename']);
	$song = $_GET['songName'];
	
    header("Content-Description: File Transfer");
	header("Content-type: application/octet-stream"); 
    header("Content-Disposition: attachment; filename=".$song.";");
	header("Content-Transfer-Encoding: binary"); 
	header("Content-Type: audio/mpeg, audio/x-mpeg, audio/x-mpeg-3, audio/mpeg3");
	//header('Content-Length: ' . filesize($file))
	readfile($file, true);
	exit;

?>