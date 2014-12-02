<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<?php 
	//Include the file with all of the calling functions
	include 'clientCalls.php';
	
	/* Eventually - include a statement to see if the session is already started. If it is
	 * forward the user on to the player. If not, let the user sign in
	 * May need to start the session early to make this work. */
	 
    session_start();
    if(isset($_SESSION["username"])) {
        header("Location: http://music.apolymoxic.com/player.php");
    }

?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>music.Apolymoxic.com</title>
	
	<!-- base href (commented) and css for site as a whole -->
	<!--<base href="http://music.apolymoxic.com/" />-->
	<link rel="stylesheet" href="css/musicSite.css" type="text/css" />

</head>
<body>

<br />
<center>
	<img src="images/banner.jpg" width="950" height="118" border="0" alt=""><br /><br />
</center>

<table width="90%" border="0" align="center">
	<tr width="100%">
		<td width="100%" valign="top" colspan="2">
			<h1>Login</h1>
		</td>
	</tr>
</table>

<table width="90%" border="0">
	<tr>
		<td>
	
<!-- table for returning users login -->
<table width="50%" border="0" cellpadding="0" cellspacing="0" align="left">
	<tr width="100%">
		<td width="27" align="right"><img src="images/tlcorner.jpg" width="27" height="27" valign="top"></td>
		<td width="100" align="left" background="images/tbar.jpg"><img src="images/tlbar.jpg" width="100" height="27" valign="top"></td>
		<td width="100%" align="center" background="images/tbar.jpg"><img src="images/tbar.jpg" width="4" height="27" valign="top"></td>
		<td width="110" align="right" background="images/tbar.jpg"><img src="images/trbar.jpg" width="110" height="27" valign="top"></td>
		<td width="29" align="left"><img src="images/trcorner.jpg" width="29" height="27" valign="top"></td>
	</tr>

	<tr width="100%">
		<td width="27" align="right" valign="top" background="images/lbar.jpg">
			<img src="images/ltbar.jpg" width="27" height="101" valign="top"><br />
			<img src="images/lbbar.jpg" width="27" height="101" valign="bottom"><br />
		</td>
		
		<!-- User login container -->		
		<td class="content" colspan="3">
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
						$username = $_REQUEST["username"];
						$thePW = $_REQUEST["password"];
						$logUserIn = login($username, $thePW);
						
						// If error on connection to the database  
						if ($logUserIn === 'Connection Error') {
							print ("<b>There was an error connecting to the database. Please try again later.</b><br /><br />");
						}
						
						// If no user is found
						else if ($logUserIn === 'NoUser') {
							print ("<b><u>Invalid User</u> - Please try again.</b><br /><br />");
						} 
						
						// User is found, but incorrect password 
						else if($logUserIn == false){
							print("<b><u>Invalid Login</u> - Please try again.</b><br /><br />");
						}
						
						// If user is found (successful login) - start session and forward to new page
						else {
							print ("User Found... Redirecting to site...");
							session_start();
							$_SESSION['username'] = $username;
							$_SESSION['email'] = $logUserIn;
							$_SESSION["lastViewTime"] = time();
							header("Location: http://music.apolymoxic.com/player.php");
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
		
		<td width="29" align="left" valign="bottom" background="images/rbar.jpg">
			<img src="images/rtbar.jpg" width="29" height="101" valign="top"><br />
			<img src="images/rbbar.jpg" width="29" height="101" valign="bottom"><br />
		</td>
	</tr>
	
	<tr width="100%" height="29" valign="bottom">
		<td width="27" align="right"><img src="images/blcorner.jpg" width="27" height="29" valign="top"></td>
		<td width="100" align="left" background="images/bbar.jpg"><img src="images/blbar.jpg" width="100" height="29" valign="top"></td>
		<td width="100%" align="center" background="images/bbar.jpg"><img src="images/bbar.jpg" width="4" height="29" valign="top"></td>
		<td width="110" align="right" background="images/bbar.jpg"><img src="images/brbar.jpg" width="110" height="29" valign="top"></td>
		<td width="29" align="left"><img src="images/brcorner.jpg" width="29" height="29" valign="top"></td>
	</tr>
</table>

<!-- table for new user creation -->
<table width="50%" border="0" cellpadding="0" cellspacing="0" align="right">
	<tr width="100%">
		<td width="27" align="right"><img src="images/tlcorner.jpg" width="27" height="27" valign="top"></td>
		<td width="100" align="left" background="images/tbar.jpg"><img src="images/tlbar.jpg" width="100" height="27" valign="top"></td>
		<td width="100%" align="center" background="images/tbar.jpg"><img src="images/tbar.jpg" width="4" height="27" valign="top"></td>
		<td width="110" align="right" background="images/tbar.jpg"><img src="images/trbar.jpg" width="110" height="27" valign="top"></td>
		<td width="29" align="left"><img src="images/trcorner.jpg" width="29" height="27" valign="top"></td>
	</tr>

	<tr width="100%">
		<td width="27" align="right" valign="top" background="images/lbar.jpg">
			<img src="images/ltbar.jpg" width="27" height="101" valign="top"><br />
			<img src="images/lbbar.jpg" width="27" height="101" valign="bottom"><br />
		</td>
		
		<!-- User creation container -->
		<td class="content" colspan="3">
			<b>New User?</b> Please sign up. It's Free!<br /><br />
			
			<?php
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			//If something was posted, check to see if the section was the "create an account"
				if (isset($_POST["create"])) {
					// Need to check if fields are empty
					if ($_POST["reqUsername"] == "") {
						print ("<b>Please enter the user name you would like...</b><br /><br />");
					}
					
					else if ($_POST["reqPassword"] == "") {
						print ("<b>Password cannot be blank.</b><br /><br />");
					}
					// Check for password match
					else if ($_POST["reqPassword"] == $_POST["verPassword"]) {
						
						// check for valid email address
						$goodEmail = validEmail($_REQUEST["email"]);
						if ($goodEmail == true) {
							$userCreation = createUser($_REQUEST["reqUsername"], $_REQUEST["reqPassword"], $_REQUEST["email"]);
							
							// what happened on the server? Post it
							if ($userCreation === 'UserExists') {
								print ("<b>Sorry, but that username is already taken. Please choose another and try again.</b><br /><br />");
							} else {
								print ("<b>User has been created!</b><br /><br />");
							}
							
						} else {
							print ("<b>Invalid email address.</b><br /><br />");
						}
					}
					else {
						print ("<b>Password and Confirm Password do not match</b><br /><br />");
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
		
		<td width="29" align="left" valign="bottom" background="images/rbar.jpg">
			<img src="images/rtbar.jpg" width="29" height="101" valign="top"><br />
			<img src="images/rbbar.jpg" width="29" height="101" valign="bottom"><br />
		</td>
	</tr>
	
	<tr width="100%" height="29" valign="bottom">
		<td width="27" align="right"><img src="images/blcorner.jpg" width="27" height="29" valign="top"></td>
		<td width="100" align="left" background="images/bbar.jpg"><img src="images/blbar.jpg" width="100" height="29" valign="top"></td>
		<td width="100%" align="center" background="images/bbar.jpg"><img src="images/bbar.jpg" width="4" height="29" valign="top"></td>
		<td width="110" align="right" background="images/bbar.jpg"><img src="images/brbar.jpg" width="110" height="29" valign="top"></td>
		<td width="29" align="left"><img src="images/brcorner.jpg" width="29" height="29" valign="top"></td>
	</tr>
</table>

		</td>
	</tr>
</table>


</body>
</html>

