<?

	/*
	 * Use these functions in the login page to either create a user or log a user in
	 */

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
	
	function logIn($username, $password) {
		$connect = mysqli_connect("server", "username", "password", "users");
		
		$password = crypt($password);
		$username = mysqli_real_escape_string($connect, $username);
		
		$result = mysqli_query($connect, "SELECT * FROM users WHERE username='$username'");
		$row = mysqli_fetch_array($result);
		
		if(mysqli_fetch_array($result) == NULL) {	// Only one result
			$compare = $row["password"];
			return ($password == $compare);
		}
		else {
			return false;
		}
	}
	
	/* 
	 * Login: Get username and password from form
	 * if(logIn($username, $password)) {
	 * 	session_start();
	 * 	$_SESSION['username'] = $username;	// Session variable can be used to get user info after logging in
	 * 	header("location: homepage.php");	// Redirect user to homepage, or wherever they should go after logging in
	 * }
	 *
	 * Create User: Get username and password from form
	 * createUser($username, $password);
	 * // Log user in using steps above
	 */

?>
