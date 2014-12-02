<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<?php 
	//Include the file with all of the calling functions
	include 'clientCalls.php';
	session_start();
	$username = $_SESSION['username'];
	//$username = "Administrator";		// For testing - uncomment to use
	
	// If the user is not logged in and they try to access this page directly,
	// direct them to the login page
	if ($username == "") {
		// Close any session that may be open
		session_destroy();
		header("Location: login.php");
	}
	
	$group = groupType($username);
	if ($group != "a"){
		header("Location: login.php");
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
					print ("| Administration ");
				}
			?>
			| <a href="player.php">Music Player</a> | <a href="settings.php">Settings & Upload</a> | <a href="logout.php">Logout</a> |
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
			
			<!-- Change codes -->
			<?php
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				// If something was posted...
				
				// Find user code
				if (isset($_POST["findUser"])) {
					if (trim($_REQUEST['user_find']) == "") {
						print ("<b>Please enter a username to find.</b><br /><br />");
					} else {
						// Set session variables... will be cleared later
						list($_SESSION['user'], $_SESSION['curr_email'], $_SESSION['curr_group']) = userInfo($_REQUEST['user_find']);
						
						// Because the function returns an array, "NoUser" would actually be returned 
						// as N, o, U... one character per array section. So we search for N and o.
						if ($_SESSION['user'] === 'N' && $_SESSION['curr_email'] === 'o') {
							print ("<b>No user with that username was found. Please try again.</b><br /><br />");
							unset($_SESSION['user']);
							unset($_SESSION['curr_email']);
							unset($_SESSION['curr_group']);
						} 
					}
				}
				
				// Change password code
				else if (isset($_POST["pw_change"])) {
					// Verification before changing password  		
					// Make sure a user has been found (like selecting a user)
					if (isset($_SESSION['user'])) {
						print ("<b>You must find a user first!</b><br /><br />");
					}	
					// Check to see if the password fields are blank
					else if (trim($_POST["new_password"]) == "" || trim($_POST["conf_password"] == "")) {
						print ("<b>New password and Confirm password cannot be blank</b><br /><br />");
					}	
					// Check for password match
					else if (trim($_POST["new_password"]) != trim($_POST["conf_password"])) {
						print ("<b>Password and Confirm Password do not match</b><br /><br />");
					} else {
						// If passwords are filled and match criteria, continue
						$newPW = $_REQUEST["new_password"];
						// This calls the change password function - the second value is the current
						// password, but not required for admins. The fourth value is if the requesting
						// party is an admin or not - y = yes, admin
						$changePW = pwChange($_SESSION['user'], "", $newPW, "y");
							
						// If error on connection to the database  
						if ($changePW === 'Connection Error') {
							print ("<b>There was an error connecting to the database. Please try again later.</b><br />");
						}
							
						// If no user is found
						else if ($changePW === 'NoUser') {
							print ("<b><u>No user by that username was found.</u></b><br /><br />");
						} 
						
						// User is found, but incorrect password 
						// Won't happen in admin state
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
					
				// Change email code
				} else if (isset($_POST["email_change"])) {
					// Verification before changing email address 
					// Check to see if the new email field is blank
					
					if (trim($_POST["new_email"]) == "") {
						print ("<b>New email address cannot be blank</b><br /><br />");	
					
					} else {
						// Send email address for verification / change
						$newEmail = $_REQUEST["new_email"];
						$changeEmail = emailChange($_SESSION['user'], $newEmail);
						
						// If error on connection to the database  
						if ($changeEmail === 'Connection Error') {
							print ("<b>There was an error connecting to the database. Please try again later.</b><br /><br />");
						}
						
						// If no user is found
						else if ($changeEmail === 'NoUser') {
							print ("<b><u>Invalid User</u> - Please try again.</b><br /><br />");
						} 
						
						// User is found, but invalid email 
						else if($changeEmail === 'BadEmail'){
							print("<b><u>Invalid email address</u>. Please try again.</b><br /><br />");
						}
						
						else if($changeEmail === 'Success'){
							// This won't print because of the refresh (header), so it's commented out
							//print("<b>Email address has successfully been changed!</b><br /><br />");
							$_SESSION['curr_email'] = $newEmail;
							// New email has been set - refresh the page
							header("Location: admin.php");
						}
						
						// Default error
						else {
							print("<b>An error occurred. Please try again later.</b><br /><br />");
						}
					}
				
				// Change group	code
				} else if (isset($_POST["demote"]) || isset($_POST["promote"])){
					
					// Do not demote the administrator account
					if ($_SESSION['user'] == "Administrator") {
						print ("<b>You cannot demote the Administrator account / username.</b><br /><br />");
					} else {
						// Promote or demote?
						if (isset($_POST["demote"])) {
							$newGroup = "d";
							$changeGroup = groupChange($_SESSION['user'], $newGroup);
						}
						else if (isset($_POST["promote"])) {
							$newGroup = "a";
							$changeGroup = groupChange($_SESSION['user'], $newGroup);
						}
						
						// If error on connection to the database  
						if ($changeGroup === 'Connection Error') {
							print ("<b>There was an error connecting to the database. Please try again later.</b><br /><br />");
						}	
							
						// If no user is found
						else if ($changeGroup === 'NoUser') {
							print ("<b><u>Invalid User</u> - Please try again.</b><br /><br />");
						} 
						
						else if($changeGroup === 'Success'){
							// This won't print because of the refresh (header), so it's commented out
							//print("<b>Email address has successfully been changed!</b><br /><br />");
							$_SESSION['curr_group'] = $newGroup;
							// New email has been set - refresh the page
							header("Location: admin.php");
						}
						
						// Default error
						else {
							print("<b>An error occurred. Please try again later.</b><br /><br />");
						}
					}
					
				// Delete user section
				} else if (isset($_POST["delUser"])) {
						// Do NOT allow deletion of "Administrator" account
						if(isset($_SESSION["user"])) {
						    if ($_SESSION['user'] == "Administrator") {
							    print ("<b>You cannot delete the Administator account / username.</b><br /><br />");
					        } else {
					        
						            $deletion = deleteUser($_SESSION['user']);
						            
						            // Do NOT allow deletion of "Administrator" account
					
						            // If error on connection to the database  
						            if ($deletion === 'Connection Error') {
							            print ("<b>There was an error connecting to the database. Please try again later.</b><br /><br />");
						            }
						
						            // If no user is found
						            else if ($deletion === 'NoUser') {
							            print ("<b><u>Invalid User</u> - Please try again.</b><br /><br />");
						            } 
							
						            else if($deletion === 'UserDeleted'){
							            // Inform the user to be deleted has been deleted. Unset session variables.
							            print ("<b>The user has been deleted.</b><br /><br />");
							            unset ($_SESSION['user']);
							            unset ($_SESSION['curr_email']);
							            unset ($_SESSION['curr_group']);
						            }
						
						            // Default error
						            else {
							            print("<b>An error occurred. Please try again later.</b><br /><br />");
						            }
					        }
					    }
					    else {
						    print("<b>The user to delete was not specified.</b>");
						}
				}
			}
			?>
			
			<!-- START Password change section -->
			<form action="<?php $_SERVER['SCRIPT_NAME']?>" method="POST">
				<table width="50%" border="0" align="left">
					<tr width="100%">
						<td with="150">Username: </td>
						<td><input type="text" name="user_find"></td>
					</tr>
					<tr width="100%">
						<td width="150">&nbsp;</td>
						<td><br /><input type="submit" name="findUser" value="Find User"></td>
					</tr>
					<tr width="100%" height="10">
						<td colspan="2">
							<!-- Separator table -->
							<table width="80%">
								<tr width="100%" height="20">
									<td width="30" align="left" valign="top" background="images/sepfill.jpg">
										<img src="images/sepleft.jpg" width="30" height="20" valign="top">
									</td>
									<td align="center" valign="top" background="images/sepfill.jpg"></td>
									<td width="30" align="right" valign="top" background="images/sepfill.jpg">
										<img src="images/sepright.jpg" width="30" height="20" valign="top">
									</td>
								</tr>
							</table>
							<!-- End of separator table -->
						</td>
					</tr>
					<tr width="100%">
						<td width="150">Username: </td>
						<td><?php if (isset($_SESSION['user'])) print("<b>" . $_SESSION['user'] . "</b>"); ?></td>
					</tr>
					<tr width="100%" height="5">
						<!-- Spacer -->
						<td colspan="2">&nbsp;</td>
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
						<td><br /><input type="submit" name="pw_change" value="Change Password"></td>
					</tr>
				</table>
			</form>
			<!-- END Password change section -->
			
			<!-- START Email and Group change section -->
			<form action="<?php $_SERVER['SCRIPT_NAME']?>" method="POST">
				<table width="50%" border="0" align="left">
					<!-- Email change -->
					<tr width="100%">
						<td with="150">User's Email Address: </td>
						<td><?php if (isset($_SESSION['curr_email'])) print("<b>" . $_SESSION['curr_email'] . "</b>"); ?></td>
					</tr>
					<tr width="100%"><td width="150">&nbsp;</td></tr>
					<tr width="100%">
						<td width="150">New Email Address: </td>
						<td><input type="text" name="new_email"></td>
					</tr>
					<tr width="100%">
						<td width="150">&nbsp;</td>
						<td><br /><input type="submit" name="email_change" value="Change Email"></td>
					</tr>
					<tr width="80%">
						<td width="100%" colspan="2"
							<!-- Separator table -->
							<table width="80%">
								<tr width="100%" height="20">
									<td width="30" align="left" valign="top" background="images/sepfill.jpg">
										<img src="images/sepleft.jpg" width="30" height="20" valign="top">
									</td>
									<td align="center" valign="top" background="images/sepfill.jpg"></td>
									<td width="30" align="right" valign="top" background="images/sepfill.jpg">
										<img src="images/sepright.jpg" width="30" height="20" valign="top">
									</td>
								</tr>
							</table>
							<!-- End of separator table -->
						</td>
					</tr>
					<tr width="100%">
						<td width="150">User's Group: </td>
						<td><?php if (isset($_SESSION['curr_group'])) {
								if ($_SESSION['curr_group'] === "a") {
									print ("<b>Administrator</b>");
								} else {
									print ("<b>Default</b>");
								}
							} ?>
						</td>
					</tr>
					<tr width="100%">
						<td width="150">&nbsp;</td>
						<td>
							<?php if (isset($_SESSION['curr_group'])) {
								if ($_SESSION['curr_group'] === "a") {
									print ("<br /><input type='submit' name='demote' value='Demote User'>");
								} else {
									print ("<br /><input type='submit' name='promote' value='Promote User'>");
								}
							} ?>
						</td>
					</tr>
				</table>
			</form>
			<!-- END Email and Group change section -->
			
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
					<td width="100%" colspan="3" align="center">
					
					
			<!-- START Delete user Section -->
			<!-- There should be some kind of warning before sending this, but in this case, there is not -->
			<h1>WARNING</h1><br />
			Do NOT click the button below unless you are sure that you want to delete the user!<br /> 
			<u>You will NOT be asked for a confirmation!</u><br /><br />
			<form action="<?php $_SERVER['SCRIPT_NAME']?>" method="POST">
				<input type="submit" name="delUser" value="Delete User">
			</form>
			<!-- END Delete user Section -->
			
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

