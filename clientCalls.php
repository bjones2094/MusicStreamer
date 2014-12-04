<?php
	//The following lines are NOT desired on a public page
	// These lines display errors (including usernames and pw to DB)
	ini_set('display_errors',1);
	error_reporting(E_ALL);
	
	include "tagReader.php";

	/*
	 * Functions called by client to request a service or resource from the server
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
			
			if(file_exists("./playlists/" . $username . "Library.json")) {
				unlink("./playlists/" . $username . "Library.json");
			}
			if(file_exists("./playlists/" . $username . "Playlists.json")) {
				unlink("./playlists/" . $username . "Playlists.json");
			}
			
			$stmt = $connect->prepare("SELECT * FROM music WHERE owner = ?");
			$stmt->bind_param("s", $username);
			$stmt->execute();
			
			$result = $stmt->get_result();
			
			while($row = $result->fetch_array()) {
				$stmt = $connect->prepare("SELECT * FROM music WHERE file_name = ? AND owner != ?");
				$stmt->bind_param("ss", $row["file_name"], $username);
				$stmt->execute();
				
				$otherResult = $stmt->get_result();
				$otherRow = $otherResult->fetch_array();
				
				if($otherRow == NULL) {
					if(file_exists($row["file_name"])) {
						unlink($row["file_name"]);
					}
				}
			}
			
			$stmt = $connect->prepare("DELETE FROM music WHERE owner = ?");
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
				return $row["email"];
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}
	
	function groupType($username) {
		// This function should only be called if username and PW are passed in
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
		
		else if($result->fetch_array() == NULL) {	// Only one result
			return $row['groupId'];
		}else {
			return false;
		}
	}
	
	function userInfo($username) {
		// This function should only be called if username and PW are passed in
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
		
		else if($result->fetch_array() == NULL) {	// Only one result
			return array($row['username'], $row['email'], $row['groupId']);
		}else {
			return false;
		}
	}
	
	function pwChange($username, $password, $new_password, $admin) {
		// This function should only be called if username and PW are passed in
		$connect = new mysqli("127.0.0.1", "root", "A2!y123Sql", "music_db");
		
		if(mysqli_connect_error()) {
			return 'Connection Error';
		}
		
		if ($admin != "y") {
			$password = crypt($password, ";&ss!stv");
			$password = substr($password, 0, 10);		// Shorten the password to only 10 characters
		}
		
		$stmt = $connect->prepare("SELECT * FROM users WHERE username = ?");
		$stmt->bind_param("s", $username);
		$stmt->execute();
		
		$result = $stmt->get_result();
		
		$row = $result->fetch_array();
		
		if($row == NULL) {
			return 'NoUser';
		}
		
		else if($result->fetch_array() == NULL) {	// Only one result
			// Is this change requested by an admin? If it is, skipping password checking
			if ($admin == "y"){
				$new_password = crypt($new_password, ";&ss!stv");	// Hash password before storing
				$new_password = substr($new_password, 0, 10);		// Only use first 10 characters to keep hash short
					
				$stmt = $connect->prepare("UPDATE users SET password = ? WHERE username = ?");
				$stmt->bind_param("ss", $new_password, $username);
				$stmt->execute();
				return 'Success';
			// If it's not an admin, check the password to ensure valid user
			} else {
				$compare = $row["password"];
			
				if($password == $compare) {				// Only change if current password matches
					$new_password = crypt($new_password, ";&ss!stv");	// Hash password before storing
					$new_password = substr($new_password, 0, 10);		// Only use first 10 characters to keep hash short
				
					$stmt = $connect->prepare("UPDATE users SET password = ? WHERE username = ?");
					$stmt->bind_param("ss", $new_password, $username);
					$stmt->execute();
					return 'Success';
				} else {
					return 'BadPassword';
				}
			}
		}
		else {
			return false;
		}
	}
	
	function emailChange($username, $new_email) {
		// This function should only be called if username and PW are passed in
		$connect = new mysqli("127.0.0.1", "root", "A2!y123Sql", "music_db");
		
		if(mysqli_connect_error()) {
			return 'Connection Error';
		}
		
		// check for valid email. If true, change the email address
		$GoodEmail = validEmail($new_email);
		if ($GoodEmail == true){
			$stmt = $connect->prepare("SELECT * FROM users WHERE username = ?");
			$stmt->bind_param("s", $username);
			$stmt->execute();
		
			$result = $stmt->get_result();
		
			$row = $result->fetch_array();
		
			if($row == NULL) {
				return 'NoUser';
			}		
			else if($result->fetch_array() == NULL) {  // Only one result
		
				$stmt = $connect->prepare("UPDATE users SET email = ? WHERE username = ?");
				$stmt->bind_param("ss", $new_email, $username);
				$stmt->execute();
				
				return 'Success';
			}
			else {
				return false;
			}
		} else {
			return 'BadEmail';
		}
	}
	
	function groupChange($username, $new_group) {
		// This function should only be called by an admin
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
		else if($result->fetch_array() == NULL) {  // Only one result
			$stmt = $connect->prepare("UPDATE users SET groupId = ? WHERE username = ?");
			$stmt->bind_param("ss", $new_group, $username);
			$stmt->execute();
				
			return 'Success';
		} else {
			return false;
		}
	} 
	
	function shareSong($sender, $receiver, $fileName, $title, $artist, $album) {
		if($sender == $receiver) {
			return false;
		}
		else {
			createPlaylist($receiver, "Shared");
			addToPlaylist($receiver, "Shared", $fileName, $title, $artist, $album);
			return true;
		}
	}
	
	function unshareSong($sender, $receiver, $fileName) {
		if($sender == $receiver) {
			return false;
		}
		else {
			removeFromPlaylist($receiver, "Shared", $fileName);
			return true;
		}
	}
	
	function updatePlaylist($username, $playlistName) {
		$jsonFileName = "./playlists/" . $username . "Playlists.json";
		
		if(file_exists($jsonFileName)) {
			$jsonObject = json_decode(file_get_contents($jsonFileName));
			
			if(isset($jsonObject->$playlistName)) {
				$playlist = $jsonObject->Shared;
				
				foreach($playlist as $key => $songInfo) {
					if(!file_exists($songInfo->mp3)) {
						unset($playlist[$key]);
						$jsonObject->Shared = $playlist;
						file_put_contents($jsonFileName, json_encode($jsonObject));
					}
				}
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
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
		
		$stmt = $connect->prepare("SELECT `AUTO_INCREMENT` FROM  INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'music_db' AND TABLE_NAME = 'music'");
		$stmt->execute();
		
		$results = $stmt->get_result();
		$row = $results->fetch_array();
		
		$newId = $row["AUTO_INCREMENT"];	// Gets next auto increment value from db
		
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
	
	function addSongToDB($username, $fileName, $title, $artist, $album) {
		if(file_exists($fileName)) {
			$connect = new mysqli("127.0.0.1", "root", "A2!y123Sql", "music_db");
			
			$stmt = $connect->prepare("INSERT INTO music (file_name, title, artist, album, owner) VALUES (?, ?, ?, ?, ?)");
			$stmt->bind_param("sssss", $fileName, $title, $artist, $album, $username);
			$stmt->execute();
		}
	}
	
	function addToLibrary($username, $fileName, $title, $artist, $album) {
		$jsonFileName = "./playlists/" . $username . "Library.json";
		
		$newObject = new stdClass();
			
		$newObject->title = $title;
		$newObject->artist = $artist;
		$newObject->album = $album;
		$newObject->mp3 = $fileName;
		
		if(file_exists($jsonFileName)) {
			$jsonArray = json_decode(file_get_contents($jsonFileName));
			
			foreach($jsonArray as $songInfo) {
				if($songInfo->mp3 == $fileName) {
					return false;
				}
			}
		}
		else {
			$jsonArray = array();
		}
		
		$jsonArray []= $newObject;	
		file_put_contents($jsonFileName, json_encode($jsonArray));
		
		return true;
	}
	
	function checkForCopy($username, $title, $artist, $album) {
		$connect = new mysqli("127.0.0.1", "root", "A2!y123Sql", "music_db");
		
		$stmt = $connect->prepare("SELECT * FROM music WHERE title = ? AND artist = ? AND album = ?");
		$stmt->bind_param("sss", $title, $artist, $album);
		$stmt->execute();
		
		$result = $stmt->get_result();
		$row = $result->fetch_array();
		
		if($row == NULL) {	// No file exists with same tags
			return "NewFile";
		}
		else if($row["owner"] == $username) {	// Same user has uploaded this file before
			return "SameUser";
		}
		else {		// Another user has uploaded this song before
			return $row["file_name"];
		}
	}
	
	function deleteSong($username, $fileName) {
		$connect = new mysqli("127.0.0.1", "root", "A2!y123Sql", "music_db");
		
		// Delete song info from database
		
		$stmt = $connect->prepare("DELETE FROM music WHERE owner = ? AND file_name = ?");
		$stmt->bind_param("ss", $username, $fileName);
		$stmt->execute();
		
		// Delete song info from library file
		
		$jsonFileName = "./playlists/" . $username . "Library.json";
		
		if(file_exists($jsonFileName)) {
			$jsonArray = json_decode(file_get_contents($jsonFileName));
			
			foreach($jsonArray as $key => $songInfo) {
				if($songInfo->mp3 == $fileName) {
					unset($jsonArray[$key]);
					file_put_contents($jsonFileName, json_encode($jsonArray));
				}
			}
		}
		
		// Check if other users own the file
		
		$stmt = $connect->prepare("SELECT * FROM music WHERE file_name = ?");
		$stmt->bind_param("s", $fileName);
		$stmt->execute();
		
		$result = $stmt->get_result();
		$row = $result->fetch_array();
		
		// Delete file if no other users own song
		
		if($row == NULL) {
			unlink($fileName);
			
			return 'SongDeleted';
		}
		else {
			return 'Song owned by others';
		}
		
		
	}
	
	// Playlist functions
	
	function createPlaylist($username, $playlistName) {
		$jsonFileName = "./playlists/" . $username . "Playlists.json";
		
		if(file_exists($jsonFileName)) {
			$jsonObject = json_decode(file_get_contents($jsonFileName));
			
			if(isset($jsonObject->$playlistName)) {
				return false;
			}
			else {
				$jsonObject->$playlistName = array();
			}
		}
		else {
			$jsonObject = new stdClass();
			$jsonObject->$playlistName = array();
		}
			
		file_put_contents($jsonFileName, json_encode($jsonObject));
		
		return true;			
	}
	
	function deletePlaylist($username, $playlistName) {
		$jsonFileName = "./playlists/" . $username . "Playlists.json";
		
		if(file_exists($jsonFileName)) {
			$jsonObject = json_decode(file_get_contents($jsonFileName));
			
			if(isset($jsonObject->$playlistName)) {
				unset($jsonObject->$playlistName);
				file_put_contents($jsonFileName, json_encode($jsonObject));
				return true;
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}
	
function addToPlaylist($username, $playlistName, $files) {
		$jsonFileName = "./playlists/" . $username . "Playlists.json";
		
		if(file_exists($jsonFileName)) {
			$jsonObject = json_decode(file_get_contents($jsonFileName));
			
			if(isset($jsonObject->$playlistName)) {
			
			foreach($files as $info)
			{
				$flag= false;
				$newObject = new stdClass();
			
				$newObject->title = $info[0];
				$newObject->artist = $info[1];
				$newObject->album = $info[2];
				$newObject->mp3 = $info[3];
			
				$playlist = $jsonObject->$playlistName;
				
				foreach($playlist as $songInfo) {
					if($songInfo->mp3 == $info[3]) {
						$flag=true;
					}
				}
				if (!$flag) {
					$playlist []= $newObject;
					$jsonObject->$playlistName = $playlist;
					}
				}
				
				file_put_contents($jsonFileName, json_encode($jsonObject));
				
				return true;
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}	
	// Search functions

 function basicSearch($username, $query) {
		// Query database with user query across all fields (e.g. title, artist, album)
		
	
    $downQuery = strtolower ($query);
    
    $connect = new mysqli("127.0.0.1", "root", "A2!y123Sql", "music_db");	
		if(mysqli_connect_error()) {
			return 'Connection Error';
		}
		

		//split it into an array
		$searchwords = explode(" ",$downQuery);
		$jsonSearchArray = array();
		
		//loop and add the words to the query
		$queries = "SELECT * FROM music WHERE owner = ? AND (title LIKE '%$searchwords[0]%' OR
		artist LIKE '%$searchwords[0]%' OR album LIKE '%$searchwords[0]%'";

		for($i = 1;$i < count($searchwords); $i++)
		{
    		$queries .= " OR title LIKE '%$searchwords[$i]%' OR artist LIKE '%$searchwords[$i]%'
    		OR album LIKE '%$searchwords[$i]%'";
		}
		$queries .= ")"; 
		//do the query

		$stmt = $connect->prepare($queries);

		$stmt->bind_param("s", $username);
		$stmt->execute();
        
        $result = $stmt->get_result();
        $row = $result->fetch_array();
        if($row == NULL) {
		
			print 'NoMatch';
		}		
		else {
			 do{
			 	
				    $newObject = new stdClass();
			   		$newObject->title = $row["title"];
					$newObject->artist =  $row["artist"];
					$newObject->album = $row["album"];
					$newObject->mp3 = $row["file_name"];
					$jsonSearchArray []= $newObject;
						
			  } while($row = $result->fetch_array());
			 
			  return json_encode($jsonSearchArray);
		}
		
	}	
	
	function advancedSearch($username, $title, $artist, $album) {
		// If field is NULL, don't use it, otherwise query database with correct terms
		
		$jsonFileName = "./playlists/" . $username . "Library.json";

		if(file_exists($jsonFileName)) {
			$jsonArray = json_decode(file_get_contents($jsonFileName));
			$jsonSearchArray = array();
			$newObject = new stdClass();

			
			foreach($jsonArray as $songInfo) {
				if(($songInfo->title == $title || $songInfo->title == NULL) &&
				  ($songInfo->artist == $artist || $songInfo->title == NULL) &&
				  ($songInfo->album == $album || $songInfo->title == NULL )) {
							
					$newObject->title = $songInfo->title;
					$newObject->artist =  $songInfo->artist;
					$newObject->album = $songInfo->album;
					$newObject->mp3 = $songInfo->mp3;
					$jsonSearchArray []= $newObject;
				}
			}
		}

		return json_encode($jsonSearchArray);
	
	}
	
?>
