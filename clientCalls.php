<?php
	/*
	 * Functions called by client to request a service or resource from the server
	 * REMEMBER TO ALWAYS SANITIZE INPUTS!!!
	 */


	// createUser function adds user information to database for future log ins

	function createUser($username, $password) {
		$connect = mysqli_connect("server", "username", "password", "users");
		
		$password = crypt($password);				// Hash password before storing
		$username = mysqli_real_escape_string($connect, $username);		// Escape all possible SQL syntax
		
		// Check if username is already taken
		
		$result = mysqli_query($connect, "SELECT * FROM users WHERE username='$username'");
		$row = mysqli_fetch_array($result);
		
		if($row == NULL) {
			mysqli_query($connect, "INSERT INTO users (username, password) VALUES ('$username', '$password')");
		}
		else {
			// User already exists
		}
	}
	
	// logIn function logs a specific user in for a session
	
	function logIn($username, $password) {
		$connect = mysqli_connect("server", "username", "password", "users");
		
		$password = crypt($password);
		$username = mysqli_real_escape_string($connect, $username);
		
		$result = mysqli_query($connect, "SELECT * FROM users WHERE username='$username'");
		
		if(!$result) {		// Check if the user exists
			return false;
		}
		
		$row = mysqli_fetch_array($result);
		
		if(mysqli_fetch_array($result) == NULL) {	// Only one result
			$compare = $row["password"];
			return ($password == $compare);
		}
		else {
			return false;
		}
	}

	// getLibrary function is used to get the users music library to display to the user

	function getLibrary($username) {
		$connect = mysqli_connect("server", "username", "password", "music");
		
		$username = mysqli_real_escape_string($connect, $username);
		
		$result = mysqli_query($connect, "SELECT * FROM music WHERE uploader='$username'");	// Get all music uploaded by user from database
		
		while($row = mysqli_fetch_array($result))
		{
			$rows []= $row;
		}
		
		$library = [];		// Holds each song as a string consisting of it's tags, seperated by colons (e.g. "title:artist:album:upload");
		
		foreach($rows as $row) {
			$songInfo = $row["title"] . ":" . $row["artist"] . ":" . $row["album"] . "upload";
			$library []= $songInfo;
		}
		
		// Get filenames of shared songs from shared file
		
		$sharedString = file_get_contents("pathToSharedMusicFile");
		$sharedList = explode("\n", $sharedString);
		
		// Get info for each file from database
		
		foreach($sharedList as $fileName) {
			$result = mysqli_query($connect, "SELECT * FROM music WHERE filename='$filename'");
			$row = mysqli_fetch_array($result);
			
			$songInfo = $row["title"] . ":" . $row["artist"] . ":" . $row["album"] . "share";
			$library []= $songInfo;
		}
		
		// Might want to only show first 10/20/30 songs?
		
		return $library;
	}
	
	// Functions that still need to be implemented
	
	function shareSong($sender, $receiver) {
		// Add filename of shared song to receiver's shared file
	}
	
	function uploadSong($username, $songName) {
		// Receive song through upload and save to appropriate location
		// Store tags and filename in database with uploader
	}
	
	function deleteSong($username, $songName) {
		// Remove entry from database
		// Check if file is used by other users
		// Delete if not
		// Remove file from all users who have had it shared to them
	}
	
	function addToPlaylist($username, $songName, $playlistName) {
		// playlists have not been designed yet
	}
	
	function removeFromPlaylist($username, $songName, $playlistName) {
		// playlists have not been designed yet
	}
	
	// Search functions
	
	function basicSearch($username, $query) {
		// Query database with user query across all fields (e.g. title, artist, album)
	}
	
	function advancedSearch($username, $title, $artist, $album) {
		// If field is NULL, don't use it, otherwise query database with correct terms
	}
	
?>
