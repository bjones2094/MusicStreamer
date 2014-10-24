<?
	if(!isset($_SESSION["username"])) {
		// Username not set/no one logged in
	}
	else {
		$username = $_SESSION["username"];	// $username can be used to identify who is logged in
	}

	// getLibrary function is used to get the users music library to display to the user

	function getLibrary($username) {
		$connect = mysqli_connect("server", "username", "password", "music");
		
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

?>
