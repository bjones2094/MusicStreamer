<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<?php 
	//Include the file with all of the calling functions
	include 'clientCalls.php';
	session_start();
	$username = $_SESSION['username'];	// For functions on this page
	
	// If the user is not logged in and they try to access this page directly,
	// direct them to the login page
	if ($username == "") {
		// Close any session that may be open
		session_destroy();
		header("Location: http://music.apolymoxic.com/login.php");
	}
	else {
	    // Destroy session if user's last page view was over 30 minutes ago
	
	    $nowTime = time();
	    if($nowTime - $_SESSION["lastViewTime"] > 1800) {
	        session_destroy();
	        header("Location: http://music.apolymoxic.com/login.php");
	    }
	    else {      // Update last view time
	        $_SESSION["lastViewTime"] = $nowTime;
	    }
	}
	
	// Find the user's group - a = admin
	$group = groupType($username);
?>

<HTML XMLNS="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>music.Apolymoxic.com</title>
	
	<!-- base href (commented) and css for site as a whole -->
	<!--<base href="http://music.apolymoxic.com/" />-->
	<link rel="stylesheet" href="css/musicSite.css" type="text/css" />

</head>

<body>

<table width="90%" border="0" align="center" cellpadding="0">
	<tr width="100%" height="130">
		<td width="100%" align="center" valign="bottom">
			<img src="images/banner.jpg" width="950" height="118" border="0" alt="">
		</td>
	</tr>
	<tr width="100%">
		<td width="100%" valign="top" align="right">
			<?php
				echo "Welcome, " . $username . "!<br />\n";
				echo "\t" . $_SESSION['email'] . "<br />\n";
				if($group == "a"){
					print ("| <a href='admin.php'>Administration</a> ");
				}
			?>
			| <a href="player.php">Music Player</a> | Settings & Upload | <a href="logout.php">Logout</a> |
		</td>
	</tr>
</table>

<table width="90%" border="0" align="center">
	<tr width="100%">
		<td width="200" align="left" valign="top">

<!-- table containing playlist -->
<table width="100%" border="0" cellpadding="0" cellspacing="0" align="left">
	
	<tr width="100%">
		<td width="27" align="right"><img src="images/tlcorner.jpg" width="27" height="27" valign="top"></td>
		<td width="100" align="left" background="images/tbar.jpg"><img src="images/tlbar.jpg" width="100" height="27" valign="top"></td>
		<td width="100%" align="center" background="images/tbar.jpg"><img src="images/tbar.jpg" width="4" height="27" valign="top"></td>
		<td width="110" align="right" background="images/tbar.jpg"><img src="images/trbar.jpg" width="110" height="27" valign="top"></td>
		<td width="29" align="left"><img src="images/trcorner.jpg" width="29" height="27" valign="top"></td>
	</tr>

	<tr width="100%">
		<td width="27" align="right" valign="top" background="images/lbar.jpg">
			<img src="images/ltbar.jpg" width="27" height="101" valign="top">
		</td>
		
		<!-- User information -->
		<!-- This section will contain the same class as the rest of the page -->
		<td class="content" colspan="3">
			
			<!-- START Password change section -->
			<?php
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				// If something was posted, check if it was to change password
				if (isset($_POST["pw_change"])) {
					// Verification before changing password  
					// Check to see if the password fields are blank
					
					if (trim($_POST["new_password"]) == "" || trim($_POST["conf_password"] == "")) {
						print ("<b>New password and Confirm password cannot be blank</b><br /><br />");
					}	
					// Check for password match
					else if (trim($_POST["new_password"]) != trim($_POST["conf_password"])) {
						print ("<b>Password and Confirm Password do not match</b><br /><br />");

					} else {
						// If passwords are filled and match criteria, continue
						$thePW = $_REQUEST["password"];
						$newPW = $_REQUEST["new_password"];
						$changePW = pwChange($username, $thePW, $newPW, "n");
						
						// If error on connection to the database  
						if ($changePW === 'Connection Error') {
							print ("There was an error connecting to the database. Please try again later.");
						}
						
						// If no user is found
						else if ($changePW === 'NoUser') {
							print ("<b><u>Invalid User</u> - Please try again.</b><br /><br />");
						} 
						
						// User is found, but incorrect password 
						else if($changePW === 'BadPassword'){
							print("<b><u>Invalid password</u>. Please try again.</b><br /><br />");
						}
						
						else if($changePW === 'Success'){
							print("<b>Password has successfully been changed!</b><br /><br />");
						}
						
						// Default error
						else {
							print("<b>An error occurred. Please try again later.</b><br /><br />");
						}
					}
				}
			}
			?>
			<form action="<?php $_SERVER['SCRIPT_NAME']?>" method="POST">
				<table width="50%" border="0" align="left">
					<tr width="100%">
						<td with="150">Username: </td>
						<td><?php echo $_SESSION['username']?></td>
					</tr>
					<tr width="100%"><td width="150">&nbsp;</td></tr>
					<tr width="100%">
						<td width="150">OLD Password: </td>
						<td><input type="password" name="password"></td>
					</tr>
					<tr width="100%">
						<td width="150">New Password: </td>
						<td><input type="password" name="new_password"></td>
					</tr>
					<tr width="100%">
						<td width="150">Confirm Password: </td>
						<td><input type="password" name="conf_password"></td>
					</tr>
					<tr width="100%">
						<td width="150">&nbsp;</td>
						<td>
							<br /><input type="submit" name="pw_change" value="Change Password">
						</td>
					</tr>
				</table>
			</form>
			<!-- END Password change section -->
			
			<!-- START Email change section -->
			<?php
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				// If something was posted, check if it as to change email
				if (isset($_POST["email_change"])) {
					// Verification before changing email address 
					// Check to see if the new email field is blank
					
					if (trim($_POST["new_email"]) == "") {
						print ("<b>New email address cannot be blank</b><br /><br />");	
					
					} else {
						// Send email address for verification / change
						$newEmail = $_REQUEST["new_email"];
						$changeEmail = emailChange($username, $newEmail);
						
						// If error on connection to the database  
						if ($changeEmail === 'Connection Error') {
							print ("There was an error connecting to the database. Please try again later.");
						}
						
						// If no user is found
						else if ($changeEmail === 'NoUser') {
							print ("<b><u>Invalid User</u> - Please try again.</b><br /><br />");
						} 
						
						// User is found, but incorrect password 
						else if($changeEmail === 'BadEmail'){
							print("<b><u>Invalid email address</u>. Please try again.</b><br /><br />");
						}
						
						else if($changeEmail === 'Success'){
							print("<b>Email address has successfully been changed!</b><br /><br />");
							$_SESSION['email'] = $newEmail;
							// New email has been set - refresh the page
							header("Location: settings.php");
						}
						
						// Default error
						else {
							print("<b>An error occurred. Please try again later.</b><br /><br />");
						}
					}
				}
			}
			?>
			<form action="<?php $_SERVER['SCRIPT_NAME']?>" method="POST">
				<table width="50%" border="0" align="left">
					<tr width="100%">
						<td with="150">Email Address: </td>
						<td><?php echo $_SESSION['email']?></td>
					</tr>
					<tr width="100%"><td width="150">&nbsp;</td></tr>
					<tr width="100%">
						<td width="150">New Email Address: </td>
						<td><input type="text" name="new_email"></td>
					</tr>
					<tr width="100%">
						<td width="150">&nbsp;</td>
						<td>
							<br /><input type="submit" name="email_change" value="Change Email">
						</td>
					</tr>
				</table>
			</form>
			<!-- END Email change section -->
			
		</td>
		
		<td width="29" align="left" valign="top" background="images/rbar.jpg">
			<img src="images/rtbar.jpg" width="29" height="101" valign="top">
		</td>
	</tr>
	
	<tr width="100%">
		<td width="27" align="right" valign="bottom" background="images/lbar.jpg">
			<img src="images/lbbar.jpg" width="27" height="101" valign="bottom">
		</td>
	
		<td class="content" width="100%" colspan="3">
			<!-- separator table -->
			<table width="80%">
				<tr width="100%" height="25"></tr>
				<tr width="100%" height="20">
					<td width="30" align="left" valign="top" background="images/sepfill.jpg">
						<img src="images/sepleft.jpg" width="30" height="20" valign="top">
					</td>
					<td align="center" valign="top" background="images/sepfill.jpg"></td>
					<td width="30" align="right" valign="top" background="images/sepfill.jpg">
						<img src="images/sepright.jpg" width="30" height="20" valign="top">
					</td>
				</tr>
				<tr width="100%" height="5"></tr>
			
				<tr width="100%">
					<td width="100%" colspan="3">
					
					
			<!-- START Upload Section -->
			<form action="<?php print($_SERVER['SCRIPT_NAME']); ?>" method="post" enctype="multipart/form-data">
				Select file to upload:<br /><br />
				<input type="file" name="uploadedFile" id="uploadedFile"><br />
				<input type="submit" value="Upload File" name="submit">
			</form>
	
			<?php
				if(isset($_POST["submit"])) {
					$fileName = uploadSong($username);
		
					if($fileName != false) {
						$reader = new ID3TagsReader();
						$tags = $reader->getTagsInfo($fileName);

                        $fileId = pathinfo($fileName, PATHINFO_BASENAME);
                        $fileId = substr($fileId, 0, strlen($fileId) - 4);

                        if(!isset($tags["Title"])) {
                            $tags["Title"] = "Song" . $fileId;
                        }
                        if(!isset($tags["Author"])) {
                            $tags["Author"] = "Song" . $fileId . "(Artist)";
                        }
                        if(!isset($tags["Album"])) {
                            $tags["Album"] = "Song" . $fileId . "(Album)";
                        }
				
						// Might want to check if any tags are empty before adding to database?
						$copyCheck = checkForCopy($username, $tags["Title"], $tags["Author"], $tags["Album"]);
				
						// File hasn't been uploaded yet
						if($copyCheck == "NewFile") {
							print ("<br /><b>File has been uploaded and added to your library!\n<b>");
							addSongToDB($username, $fileName, $tags["Title"], $tags["Author"], $tags["Album"]);
							addToLibrary($username, $fileName, $tags["Title"], $tags["Author"], $tags["Album"]);
						}
				
						// This user has already uploaded this file before
						else if($copyCheck == "SameUser") {
							print("<br /><b>You have already uploaded this file\n</b>");
							unlink($fileName);
						}
				
						// Another user has uploaded this file before
						else {
							print("<br /><b>This file has already been uploaded by another user. But Don't worry, we'll make sure you can still access it in your library.\n</b>");
							addSongToDB($username, $copyCheck, $tags["Title"], $tags["Author"], $tags["Album"]);
							addToLibrary($username, $copyCheck, $tags["Title"], $tags["Author"], $tags["Album"]);
                            unlink($fileName);
						}
					}
				}
			?>
			<!-- END Upload Section -->
			
					</td>
				</tr>
			</table>
		</td>
			
		<td width="29" align="left" valign="bottom" background="images/rbar.jpg">
			<img src="images/rbbar.jpg" width="29" height="101" valign="bottom">
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

