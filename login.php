<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<?php 

	include 'clientCalls.php';
	
?>

<HTML XMLNS="http://www.w3.org/1999/xhtml">
<head>
	<TITLE>music.Apolymoxic.com</TITLE>
</head>

<Body BGCOLOR="WHITE">

<center><IMG SRC="images/banner.jpg" WIDTH="950" HEIGHT="200" BORDER="0" ALT=""><BR /><BR />

<font color="BLACK">
music.Apolymoxic.com is under construction. Please check back later. 

</center>

<h1>Login</h1>

<table width="100%" border="1">
	<tr width="100%">
		<td width="48%" valign="top">
			<b>Returning User?</b> Please log in below...<br /><br />
			<?php
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			//If something was posted, check to see if the section was the login
				if (isset($_POST["login"])) {
					//Login... 
					if(logIn($_REQUEST["username"], $_REQUEST["password"])) {
						print("Logged in");
					}
					else {
						print("Failed");
					}
				}
			}
			?>
			
			<form action="<?php $_SERVER['SCRIPT_NAME']?>" method="POST">
				Username: <input type="text" name="username"><br />
				Password: <input type="password" name="password"><br />
				<input type="submit" name="login" value="Log In">
			</form><br />
		</td>
		<td width="52%" valign="top">
			<b>New User?</b> Please sign up. It's Free!<br /><br />
			<?php
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			//If something was posted, check to see if the section was the "create an account"
				if (isset($_POST["create"])) {
					//Call function to create an account. 
					createUser($_REQUEST["reqUsername"], $_REQUEST["reqPassword"]);
				}	
			}
			?>
			
			<form action="<?php $_SERVER['SCRIPT_NAME']?>" method="POST">
				Username: <input type="text" name="reqUsername"><br />
				Password: <input type="password" name="reqPassword"><br />
				Confirm Password: <input type="password" name="verPassword"><br />
				Email Address: <input type="text" name="email"><br />
				<input type="submit" name="create" value="Create Account">
			</form><br />
		</td>
	</tr>
</table>


</body>
</html>

