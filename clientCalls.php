<?php
	//The following lines are NOT desired on a public page
	// These lines display errors (including usernames and pw to DB)
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

	function createUser($username, $password, $email) {
		$connect = new mysqli("127.0.0.1", "root", "A2!y123Sql", "music_db");
		
		if(mysqli_connect_error()) {
			return 'Connection Error';
		}
		
		$password = crypt($password, ";&ss!stv");	// Hash password before storing
		$password = substr($password, 0, 10);		// Only use first 10 characters to keep hash short
		
		// Check if username is already taken
		
		$stmt = $connect->prepare("SELECT * FROM users WHERE username = ?");
		$stmt->bind_param("s", $username);
		$stmt->execute();
		
		$result = $stmt->get_result();
		$row = $result->fetch_array();
		
		if($row == NULL) {
			$stmt = $connect->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
			$stmt->bind_param("sss", $username, $password, $email);
			$stmt->execute();
			
			return 'UserCreated';
		}
		else {
			return 'UserExists';
		}
	}
	function deleteUser($username) {
		$connect = new mysqli("127.0.0.1", "root", "A2!y123Sql", "music_db");
		
		if(mysqli_connect_error()) {
			return 'Connection Error';
		}
		
		$stmt = $connect->prepare("SELECT * FROM users WHERE username = ?");
		$stmt->bind_param("s", $username);
		$stmt->execute();
		
		$result = $stmt->get_result();
		$row = $result->fetch_array();
		
		if($row == NULL) {
			return 'NoUser';
		}
		else {
			$stmt = $connect->prepare("DELETE FROM users WHERE username = ?");
			$stmt->bind_param("s", $username);
			$stmt->execute();
			
			return "UserDeleted";
		}
	}
	
	// checks for valid email address
	function validEmail($email) {
	
		// if NOT valid, return false
		return true;
	}
	
	// logIn function logs a specific user in for a session
	
	function logIn($username, $password) {
		// This function should only be called if username and PW are passed in
		$connect = new mysqli("127.0.0.1", "root", "A2!y123Sql", "music_db");
		
		if(mysqli_connect_error()) {
			return 'Connection Error';
		}
		
		$password = crypt($password, ";&ss!stv");
		$password = substr($password, 0, 10);
		
		$stmt = $connect->prepare("SELECT * FROM users WHERE username = ?");
		$stmt->bind_param("s", $username);
		$stmt->execute();
		
		$result = $stmt->get_result();
		
		
		$row = $result->fetch_array();
		
		if($row == NULL) {
			return 'NoUser';
		}
		
		else if($result->fetch_array() == NULL) {	// Only one result
			$compare = $row["password"];
			
			if($password == $compare) {
				return 'UserFound';
			}
			else {
				return false;
			}
		}
		else {
			return 'MultiUser';
		}
	}

	// getLibrary function is used to get the users music library to display to the user

	function getLibrary($username) {
		$connect = new mysqli("127.0.0.1", "root", "A2!y123Sql", "music_db");
		
		// Get all music uploaded by user from database
		
		$stmt = $connect->prepare("SELECT * FROM music WHERE owner=?");
		$stmt->bind_param("s", $username);
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
		
		if(file_exists($username . "Shared")) {
		
			$sharedString = file_get_contents($username . "Shared");
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
		
			if($sharedString == "") {
				unlink($username . "Shared");		// Delete file if it's empty
			}
			else {
				file_put_contents($username . "Shared", $sharedString);		// Update file after any changes are made
			}
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
		$connect = new mysqli("127.0.0.1", "root", "A2!y123Sql", "music_db");
		
		// Check if user exists
		
		$stmt = $connect->prepare("SELECT * FROM users WHERE username = ?");
		$stmt->bind_param("s", $username);
		$stmt->execute();
		
		$result = $stmt->get_result();
		$row = $result->fetch_array();
		
		if($row == NULL) {
			print("<b>User does not exist</b>");
			return false;
		}
	
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
			print("<b>A file with id $newID.mp3 already exists</b>");
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
			
			// Insert tags into database
			$stmt = $connect->prepare("INSERT INTO music (file_name, title, artist, album, owner) VALUES (?, ?, ?, ?, ?)");
			
			// Extract ID3 tags from file
			
			$reader = new ID3TagsReader();
			$tags = $reader->getTagsInfo($fileName);
			
			$stmt->bind_param("sssss", $fileName, $tags["Title"], $tags["Author"], $tags["Album"], $username);
			$stmt->execute();
		}
	}
	
	function removeSongFromDB($username, $songName) {
		$connect = new mysqli("127.0.0.1", "root", "A2!y123Sql", "music_db");
		
		$stmt = $connect->prepare("DELETE FROM music WHERE owner = ? AND title = ?");
		$stmt->bind_param("ss", $username, $songName);
		$stmt->execute();
	}
	
	function deleteSong($username, $songName) {
		// Remove entry from database
		// Check if file is used by other users
		// Delete if not
	}
	
	// Playlist functions
	
	function createPlaylist($username, $playlistName) {
		$fileName = "./playlists/" . $username . "Playlists.ini";
	
		if(!file_exists($fileName)) {
			$playlistFile = fopen($fileName, "w");	// Create file if it doesn't exist yet
			fclose($playlistFile);
			
			$default = array(
				"$playlistName" =>  array ());	// Initialize empty array for empty playist
				
			writeIniFile($default, $fileName, true);
		}
		else {
			$playlists = parse_ini_file($fileName, true);	// Append new playlist to file contents
			if(!isset($playlists[$playlistName])) {
				$playlists[$playlistName] = array ();
				writeIniFile($playlists, $fileName, true);
			}
			else {
				print("Already there\n");
			}
		}			
	}
	
	function deletePlaylist($username, $playlistName) {
		$fileName = "./playlists/" . $username . "Playlists.ini";
	
		if(file_exists($fileName)) {
			$playlists = parse_ini_file($fileName, true);
			if(isset($playlists[$playlistName])) {
				unset($playlists[$playlistName]);
				writeIniFile($playlists, $fileName, true);
			}
			else {
				print("Not there\n");
			}
		}
		else {
			// File doesn't exist
		}
	}
	
	function addToPlaylist($username, $playlistName, $songName) {
		$fileName = "./playlists/" . $username . "Playlists.ini";
		
		if(file_exists($fileName)) {
			$playlists = parse_ini_file($fileName, true);
			
			if(!in_array($songName, $playlists[$playlistName])) {
				$playlists[$playlistName] []= $songName;
				writeIniFile($playlists, $fileName, true);
			}
			else {
				print("Already there\n");
			}
		}
		else {
			// return error
		}
	}
	
	function removeFromPlaylist($username, $playlistName, $songName) {
		$fileName = "./playlists/" . $username . "Playlists.ini";
		
		if(file_exists($fileName)) {
			$playlists = parse_ini_file($fileName, true);
			
			if(in_array($songName, $playlists[$playlistName])) {
				$key = array_search($songName, $playlists[$playlistName]);
				unset($playlists[$playlistName][$key]);
				writeIniFile($playlists, $fileName, true);
			}
			else {
				print("Not there\n");
			}
		}
		else {
			// return error
		}
	}
	
	function getPlaylist($username, $playlistName) {
		$fileName = "./playlists/" . $username . "Playlists.ini";
		
		if(file_exists($fileName)) {
			$playlists = parse_ini_file($fileName, true);
			$returnList = array();
			
			if(isset($playlists[$playlistName])) {
				$connect = new mysqli("127.0.0.1", "root", "A2!y123Sql", "music_db");
			
				foreach($playlists[$playlistName] as $title) {
					$stmt = $connect->prepare("SELECT * FROM music WHERE title = ? AND owner = ?");
					$stmt->bind_param("ss", $title, $username);
					$stmt->execute();
					
					$result = $stmt->get_result();
					$row = $result->fetch_array();
					$row["permission"] = "u";
					
					$returnList []= $row;
				}
				
				return $returnList;
			}
			else {
				// Playlist doesn't exist
			}
		}
		else {
			// return error
		}
	}
	
	// Search functions
	
	function basicSearch($username, $query) {
		// Query database with user query across all fields (e.g. title, artist, album)
	}
	
	function advancedSearch($username, $title, $artist, $album) {
		// If field is NULL, don't use it, otherwise query database with correct terms
	}
	
	// Custom function to create ini files from associative arrays
	
	function writeIniFile($assoc_arr, $path, $has_sections=FALSE) { 
    		$content = ""; 
	    	if ($has_sections) { 
			foreach ($assoc_arr as $key=>$elem) { 
				$content .= "[" . $key . "]\n"; 
			 	foreach ($elem as $key2=>$elem2) { 
					if(is_array($elem2)) {
				    		for($i=0;$i<count($elem2);$i++) {
				        		$content .= $key2 . "[] = \"" . $elem2[$i] . "\"\n"; 
				    		}
					}
					else if($elem2 == "") { 
						$content .= $key2 . " = \n";
					}
					else {
						$content .= $key2 . " = \"".$elem2."\"\n";
					}
			    	}
			}
	    	}
	    	else { 
			foreach ($assoc_arr as $key=>$elem) { 
				if(is_array($elem)) {
					for($i=0;$i<count($elem);$i++) {
				    		$content .= $key . "[] = \"" . $elem[$i] . "\"\n"; 
					} 
			    	} 
			    	else if($elem=="") {
			    		$content .= $key . " = \n";
			    	}
			    	else {
			    		$content .= $key . " = \"" . $elem . "\"\n";
			    	}
			}
	    	}

	    	if (!$handle = fopen($path, 'w')) { 
			return false; 
	    	}

	   	$success = fwrite($handle, $content);
	    	fclose($handle); 

		return $success;
	}
	
?>
