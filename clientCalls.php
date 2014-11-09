<?php
	ini_set('display_errors',1);
	error_reporting(E_ALL);
	
	include "tagReader.php";

	/*
	 * Functions called by client to request a service or resource from the server
	 * REMEMBER TO ALWAYS SANITIZE INPUTS!!!
	 */

	// Use this line to connect to database:
	// $connect = new mysqli("127.0.0.1", "root", "A2!y123Sql", "music_db");


	// createUser function adds user information to database for future log ins

	function createUser($username, $password) {
		if($username == "" or $password == "") {
			return false;
		}
	
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
		if($username == "" or $password == "") {
			return false;
		}
	
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

	function getLibrary($username) {
		$connect = new mysqli("127.0.0.1", "root", "A2!y123Sql", "music_db");
		
		// Get user id from username
		
		$stmt = $connect->prepare("SELECT * FROM users WHERE username=?");
		$stmt->bind_param("s", $username);
		$stmt->execute();
		
		$result = $stmt->get_result();
		
		if($result == NULL) {		// User doesn't exist
			return NULL;
		}
		else {
			$row = $result->fetch_array();
			$userId = $row["id"];
		}
		
		// Get all music uploaded by user from database
		
		$stmt = $connect->prepare("SELECT * FROM music WHERE owner=?");
		$stmt->bind_param("s", $userId);
		$stmt->execute();
		
		$result = $stmt->get_result();
		
		$library = array();		// Holds each song as an associative array;
		
		while($row = $result->fetch_array())
		{
			$songInfo = $row;
			$songInfo["permission"] = "u";
			$library []= $songInfo;
		}
		
		// Get filenames of shared songs from shared file
		
		if(file_exists("pathToSharedMusicFile")) {
		
			$sharedString = file_get_contents("pathToSharedMusicFile");
			$sharedList = explode("\n", $sharedString);
		
			// Get info for each file from database
		
			foreach($sharedList as $fileName) {
				if(!file_exists($fileName)) {
					$sharedString = str_replace($fileName . "\n", "", $sharedString);	// Remove non-existent file name
				}
				else {
					$stmt = $connect->prepare("SELECT * FROM music WHERE filename=?");
					$stmt->bind_param("s", $fileName);
					$stmt->execute();
			
					$result = $stmt->get_result();
					$row = $result->fetch_array();
			
					$songInfo = $row;
					$songInfo["permission"] = "s";
					$library []= $songInfo;
				}
			}
		
			file_put_contents($fileName, $sharedString);		// Update file after any changes are made
		}
		
		// Might want to only show first 10/20/30 songs?
		
		return $library;
	}
	
	function shareSong($sender, $receiver, $songName) {
		// Check that sender and receiver aren't same person (someone would actually try this)
		// Check if user exists
		// Check if user's shared file exists
		// Create file if necessary
		// Add filename of shared song to receiver's shared file
	}
	
	function uploadSong($username) {
		if(!is_uploaded_file($_FILES["uploadedFile"]["tmp_name"])) {	// File not selected
			print("<b>File failed to upload</b>");
			return false;
		}
		
		// Check file extension
	
		$checkName = "./MusicFiles/" . basename($_FILES["uploadedFile"]["name"]);
		$info = pathinfo($checkName);
		$fileExtension = $info["extension"];
		
		if($fileExtension != "mp3") {		// Only accept mp3s for now
			print("<b>Files must be in mp3 format</b><br>");
			return false;
		}
	
		// Create file name based off id
	
		$connect = new mysqli("127.0.0.1", "root", "A2!y123Sql", "music_db");
		
		$stmt = $connect->prepare("SELECT MAX(id) AS prevId FROM music");
		if(!$stmt) {
			print("<b>Error connecting to database</b>");
			return false;
		}
		$stmt->execute();
		
		$results = $stmt->get_result();
		$row = $results->fetch_assoc();
		
		if($row["prevId"] == "") {	// First file in db
			$newId = 0;
		}
		else {
			$prevId = $row["prevId"];	// Current highest id
			$newId = $prevId + 1;
		}
		
		$fileName = "./MusicFiles/" . $newId . ".mp3";
	
		if(file_exists($fileName)){
			print("<b>A file with that id already exists</b>");
			return false;
		}
		else if($_FILES["uploadedFile"]["size"] >= 500000000) {	// File too big
			print("<b>File must be smaller than 500MB</b>");
			return false;
		}
		else {		
			if(move_uploaded_file($_FILES["uploadedFile"]["tmp_name"], $fileName)) {
				return $fileName;
			}
			else {
				print("<b>File failed to upload</b>");
				return false;
			}
		}
	}
	
	function addSongToDB($username, $fileName) {
		if(file_exists($fileName)) {
			$connect = new mysqli("127.0.0.1", "root", "A2!y123Sql", "music_db");
			
			// Check if user exists
			
			$stmt = $connect->prepare("SELECT * FROM users WHERE username = ?");
			
			if(!$stmt) {
				print("<b>Error connecting to database</b>");
				return false;
			}
			
			$stmt->bind_param("s", $username);
			$stmt->execute();
			
			$results = $stmt->get_result();
			$row = $results->fetch_array();
			
			if($row == NULL) {
				print("User does not exist in database");
			}
			else {		// Insert tags into database
				$userId = $row["id"];
				$stmt = $connect->prepare("INSERT INTO music (file_name, title, artist, album, owner) VALUES (?, ?, ?, ?, ?)");
				
				if(!$stmt) {
					print("<b>Error connecting to database</b>");
					return false;
				}
				
				// Extract ID3 tags from file
				
				// TODO: Check if tags exist, prompt user for custom tags
				
				$reader = new ID3TagsReader();
				$tags = $reader->getTagsInfo($fileName);
				
				$stmt->bind_param("sssss", $fileName, $tags["Title"], $tags["Author"], $tags["Album"], $userId);
				
				if(!$stmt->execute()) {
					print("Bad query");
				}
			}
		}
	}
	
	function deleteSong($username, $songName) {
		// Remove entry from database
		// Check if file is used by other users
		// Delete if not
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
