<?php
	/*
	 * Functions called by client to request a service or resource from the server
	 * REMEMBER TO ALWAYS SANITIZE INPUTS!!!
	 */

	// Use this line to connect to database:
	// $connect new mysqli("127.0.0.1", "root", "A2!y123Sql", "music_db");


	// createUser function adds user information to database for future log ins

	function createUser($username, $password) {
		$connect = new mysqli("127.0.0.1", "root", "A2!y123Sql", "music_db");
		
		if(mysqli_connect_error()) {
			return false;
		}
		
		$password = crypt($password);				// Hash password before storing
		$password = substr($password, 0, 10);			// Only use first 10 characters to keep hash short
		
		// Check if username is already taken
		
		$stmt = $connect->prepare("SELECT * FROM users WHERE username = ?");
		$stmt->bind_param("s", $username);
		$stmt->execute();
		
		$result = $stmt->get_result();
		$row = $result->fetch_array();
		
		if($row == NULL) {
			$stmt = $connect->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
			$stmt->bind_param("ss", $username, $password);
			$stmt->execute();
		}
		else {
			print("User already exists.");
		}
	}
	
	// logIn function logs a specific user in for a session
	
	function logIn($username, $password) {
		$connect = new mysqli("127.0.0.1", "root", "A2!y123Sql", "music_db");
		
		if(mysqli_connect_error()) {
			return false;
		}
		
		$password = crypt($password);
		$password = substr($password, 0, 10);
		
		$stmt = $connect->prepare("SELECT * FROM users WHERE username=?");
		$stmt->bind_param("s", $username);
		$stmt->execute();
		
		$result = $stmt->get_result();
		
		if(!$result) {		// Check if the user exists
			return false;
		}
		
		$row = $result->fetch_array();
		
		if($result->fetch_array() == NULL) {	// Only one result
			$compare = $row["password"];
			return ($row["password"] == $compare);
		}
		else {
			return false;
		}
	}

	// getLibrary function is used to get the users music library to display to the user

	/*

	function getLibrary($username) {
		$connect = new mysqli("127.0.0.1", "root", "A2!y123Sql", "music_db");
		
		$stmt = $connect->prepare("SELECT * FROM music WHERE uploader=?");	// Get all music uploaded by user from database
		$stmt->bind_param("s", $username);
		$stmt->execute();
		
		$result = $stmt->get_result();
		
		
		while($row = $result->fetch_array())
		{
			$rows []= $row;
		}
		
		$library = [];		// Holds each song as a string consisting of it's tags, seperated by colons (e.g. "title:artist:album:upload");
		
		foreach($rows as $row) {
			$songInfo = $row["title"] . ":" . $row["artist"] . ":" . $row["album"] . "u";
			$library []= $songInfo;
		}
		
		// Get filenames of shared songs from shared file
		
		$sharedString = file_get_contents("pathToSharedMusicFile");
		$sharedList = explode("\n", $sharedString);
		
		// Get info for each file from database
		
		foreach($sharedList as $fileName) {
			$stmt = $connect->prepare("SELECT * FROM music WHERE filename=?");
			$stmt->bind_param("s", $filename);
			$stmt->execute();
			
			$result = $stmt->get_result();
			$row = $result->fetch_array();
			
			$songInfo = $row["title"] . ":" . $row["artist"] . ":" . $row["album"] . "s";
			$library []= $songInfo;
		}
		
		// Might want to only show first 10/20/30 songs?
		
		return $library;
	}
	
	*/
	
	// Functions that still need to be implemented
	
	function shareSong($sender, $receiver, $songName) {
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
