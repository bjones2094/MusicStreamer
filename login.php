<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<?php 
	//Include the file with all of the calling functions
	include 'clientCalls.php';
?>

<HTML XMLNS="http://www.w3.org/1999/xhtml">
<head>
	<TITLE>music.Apolymoxic.com</TITLE>
</head>

<Body BGCOLOR="WHITE">

<center><IMG SRC="images/banner.jpg" WIDTH="950" HEIGHT="200" BORDER="0" ALT=""><BR /><BR />

<font color="BLACK">
music.Apolymoxic.com is under construction!

</center>

<table width="90%" border="0" align="center">
	<tr width="100%">
		<td width="100%" valign="top" colspan="2">
			<h1>Login</h1>
		</td>
	</tr>
</table>

<table width="90%" border="1" align="center">

	<tr width="100%">
		<td width="48%" valign="top">
			<b>Returning User?</b> Please log in below...<br /><br />
			<?php
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				// If something was posted, check to see if the section was the login
				if (isset($_POST["login"])) {
					// Login... 
					// Check to see if the username and / or password are blank
					if ((trim($_REQUEST["username"]) == "") || (trim($_REQUEST["password"])) == "") {
						print ("Username and / or password cannot be blank.");
						if (!(trim($_REQUEST["username"])) == "") {
							//username = $_REQUEST["username"];
						}
					} else {
						// If username and password are filled, continue
						$logUserIn = login($_REQUEST["username"], $_REQUEST["password"]);
						
						// If error on connection to the database  
						if ($logUserIn === 'Connection Error') {
							print ("There was an error connecting to the database. Please try back later.");
						}
						
						// If no user is found
						else if ($logUserIn === 'NoUser') {
							print ("Invalid Credentials (No User Found)- Please try again");
						} 
					
						// If user is found (successful login) - start session and forward to new page
						else if ($logUserIn === 'UserFound') {
							print ("User Found... Redirecting to site...");
							header("Location: player.php");
						}
						
						// User is found, but incorrect password 
						else if($logUserIn == false){
							print("Incorrect Login");
						}
					}
				}
			}
			?>
			<form action="<?php $_SERVER['SCRIPT_NAME']?>" method="POST">
				<table width="100%" border="0" align="left">
					<tr width="100%">
						<td with="90">Username: </td>
						<td><input type="text" name="username"></td>
					</tr>
					<tr width="100%">
						<td width="90">Password: </td>
						<td><input type="password" name="password"></td>
					</tr>
				</table>
				<input type="submit" name="login" value="Log In">
			</form>
		</td>
		<td width="52%" valign="top">
			<b>New User?</b> Please sign up. It's Free!<br /><br />
			<?php
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			//If something was posted, check to see if the section was the "create an account"
				if (isset($_POST["create"])) {
					// Need to check if fields are empty
					if ($_POST["reqUsername"] == "") {
						print ("Please enter the user name you would like...<br />");
					}
					
					if ($_POST["reqPassword"] == "") {
						print ("Password cannot be blank <br />");
					}
					// Check for password match
					if ($_POST["reqPassword"] == $_POST["verPassword"]) {
						
						// check for valid email address
						$goodEmail = validEmail($_REQUEST["email"]);
						if ($goodEmail == true) {
							$userCreation = createUser($_REQUEST["reqUsername"], $_REQUEST["reqPassword"], $_REQUEST["email"]);
							
							// what happened on the server? Post it
							if ($userCreation === 'UserExists') {
								print ("Sorry, but that username is already taken. Please choose another and try again...");
							} else {
								print ("User has been created");
							}
							
						} else {
							print ("Invalid email address");
						}
					}
					else {
						print ("<b>Password and Confirm Password do not match</b>");
					}
				}	
			}
			?>
			
			<form action="<?php $_SERVER['SCRIPT_NAME']?>" method="POST">
				<table width="100%" border="0" align="left">
					<tr width="100%">
						<td with="140">Username: </td>
						<td><input type="text" name="reqUsername"></td>
					</tr>
					<tr width="100%">
						<td width="140">Password: </td>
						<td><input type="password" name="reqPassword"></td>
					</tr>
					<tr width="100%">
						<td width="140">Confirm Password: </td>
						<td><input type="password" name="verPassword"></td>
					</tr>
					<tr width="100%">
						<td width="140">Email Address: </td>
						<td><input type="text" name="email"></td>
					</tr>
				</table>
				<input type="submit" name="create" value="Create Account">
			</form><br />
		</td>
	</tr>
</table>


</body>
</html>

