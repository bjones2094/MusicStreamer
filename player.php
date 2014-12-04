<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<?php 
	// Include the file with all of the calling functions
	include 'clientCalls.php';
	session_start();
	$username = $_SESSION['username']; 	// For functions on the page
	
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
	// We could use the session type set at login, but that doesn't refresh automatically
	$group = groupType($username);
	
	/* 
	 * NOTE for below, the jPlayer code MUST be placed BEFORE the flexiGrid code
	 */
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>music.Apolymoxic.com</title>
	<!-- base href (commented) and css for site as a whole -->
	<!--<base href="http://music.apolymoxic.com/" />-->
	<link rel="stylesheet" href="css/musicSite.css" type="text/css" />
	
	<!-- css and js for jplayer -->
	<link type="text/css" href="skins/blue.monday/jplayer.blue.monday.css" rel="stylesheet" />
	<script type="text/javascript" src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
	<script type="text/javascript" src="js/jplayer/jquery.jplayer.min.js"></script>
	<script type="text/javascript" src="js/jplayer/jplayer.playlist.min.js"></script>
	<script type="text/javascript" src="js/jplayer/jquery.jplayer.inspector.js"></script>
	
	<!-- css and js for flexigrid -->
	<link rel="stylesheet" type="text/css" href="css/flexigrid.css">
	<script type="text/javascript" src="js/flexigrid/flexigrid.js"></script>
	
	<!-- code for temp playlists for jplayer use -->
	<script type="text/javascript">
		var init = true;
		var myPlaylist;

		$(document).ready(function(){
			// JPlayer initializer
			// This initialization is primarily for correct layout / alignment of the JPlayer
			myPlaylist = new jPlayerPlaylist(
				{
				jPlayer: "#jquery_jplayer_1",
				cssSelectorAncestor: "#jp_container_1"
				}, 
				[],
				{
					swfPath: "/js",
					supplied: "mp3",
					wmode: "window",
					smoothPlayBar: false,
					keyEnabled: true,
					loop: true,
					playlistOptions: {
						loopOnPrevious: true
					},
					keyBindings: {
						volumeUp: {
							key: 107,
							fn: function(f) {
								f.volume(f.options.volume + 0.1);
							}
						},
						volumeDown: {
							key: 109,
							fn: function(f) {
								f.volume(f.options.volume - 0.1);
							}
						}
					}
				}
			);

			$("#jquery_jplayer_1").bind($.jPlayer.event.setmedia, function(event) {
				var info = myPlaylist.playlist[myPlaylist.current].title;
				info += " | ";
				info += myPlaylist.playlist[myPlaylist.current].artist;
				info += " | ";
				info += myPlaylist.playlist[myPlaylist.current].album;

				document.getElementById("jp-dis").innerHTML = info;
			});
		});
	</script>
	<!-- Remaining script removed - placed below in code, and in backup_copies documentation (jPlayer) -->

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
					print ("| <a id='basic' href='admin.php'>Administration</a> ");
				}
			?>
			| Music Player | <a href="settings.php">Settings & Upload</a> | <a href="logout.php">Logout</a> |
		</td>
	</tr>
	<tr width="100%">
		<td width="100%">

<div id="jquery_jplayer_1" class="jp-jplayer"></div>
	<div id="jp_container_1" class="jp-audio">
		<div class="jp-type-playlist">
			<div class="jp-gui jp-interface">
				<ul class="jp-controls">
					<li><a href="javascript:;" class="jp-previous" tabindex="1">previous</a></li>
					<li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>
					<li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>
					<li><a href="javascript:;" class="jp-next" tabindex="1">next</a></li>
					<li><a href="javascript:;" class="jp-stop" tabindex="1">stop</a></li>
					<li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a></li>
					<li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>
					<li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="max volume">max volume</a></li>
				</ul>
				<div class="jp-progress">
					<div class="jp-seek-bar">
						<div class="jp-play-bar"></div>
					</div>
				</div>
				<div class="jp-volume-bar">
					<div class="jp-volume-bar-value"></div>
				</div>
				<div class="jp-time-holder">
					<div class="jp-current-time"></div>
					<div class="jp-duration"></div>
				</div>
				<ul class="jp-toggles">
					<li><a href="javascript:;" class="jp-shuffle" tabindex="1" title="shuffle">shuffle</a></li>
					<li><a href="javascript:;" class="jp-shuffle-off" tabindex="1" title="shuffle off">shuffle off</a></li>
					<li><a href="javascript:;" class="jp-repeat" tabindex="1" title="repeat">repeat</a></li>
					<li><a href="javascript:;" class="jp-repeat-off" tabindex="1" title="repeat off">repeat off</a></li>
				</ul>
			</div>
			<div id="jp-dis" class="jp-dis">Song Name | Song Artist | Song Album</div>
			<div class="jp-playlist" style="display:none;">
				<ul>
					<li></li>
				</ul>
			</div>
	
			<div class="jp-no-solution">
				<span>Update Required</span>
				To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
			</div>
		</div>
	</div>

	
		</td>
	</tr>
	
	<tr width="100%" height="25">
		<td width="100%" height="100%" align="center">
			&nbsp;
		</td>
	</tr>
</table>

<table width="90%" border="0" align="center">
	<tr width="100%">
		<td width="200" align="left" valign="top">

<!-- table containing playlist -->
<table width="20%" border="0" cellpadding="0" cellspacing="0" align="left">
	
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
		
		<!-- Playlist container -->
		<!-- This section will contain the same class as the rest of the page -->
		<td class="content" colspan="3">
			<table id="flexLIST" style="display:none" width="100%" align="left">

			</table>
			<script type="text/javascript">
				var selectedPL;
				var PLid;
				
				$("#flexLIST").flexigrid({
					url: 'playListNames.php',
					dataType: 'json',
					singleSelect: true,
					colResize: false,
					colModel: [
						{display: 'Playlist', name: 'playlistName', width: '150', sortable: true, align: 'left', process: changePL},
						],
					height: 'auto'
				});
					
				// This function adds parameters to the post of flexigrid			
				function addFormData(){
					// Passing a form object to serializeArray will get the valid data from all the objects, but, if the you pass a non-form object, 
					// you have to specify the input elements that the data will come from
					var dt = $('#sformLIST').serializeArray();
					$("#flexLIST").flex
					Options({params: dt});
					return true;
				}
				
				function changePL(celDiv, id){
					$(celDiv).click(function(){
						selectedPL = this.innerHTML;

						if(id)
							PLid = parseInt(id);
						else
							PLid = 0;

						var newURL = "songList.php?playlistName=" + selectedPL;
						jQuery('#flexSONG').flexOptions({url: newURL}).flexReload();
						
						// Create a variable inside the body of the site which will hold the name of 
						// the current playlist selected. This will help for adding / removing songs,
						// or deleting the playlist
						$("body").data("selPL", selectedPL);
					});
				}
	
				function selPL(){
					// Return the name of the selected playlist
					
				}
				
				function add_to_playlist(celDiv, id) {
					var target;
					$(celDiv).click(function() {

						var i = 0;
						if(id == false)
							id = 0;
						else
							id = parseInt(id);

						$('#flexLIST tr').each(function() {
							if(i <= id)
								target = $('td[abbr="playlistName"] >div', this).text();
							i += 1;
						});

						//for each selected song, do a call to the server to add to target
					});
				}

				function remove_from_playlist() {
					//using selectedPL, PLid, and selected (song info array), use client call
					getSelected();

					console.log(selected[0]);
					console.log(selected[1]);
				}

				function delete_songs() {
					//same idea as remove
				}

				// I'm not sure what this does, but it doesn't hurt to leave it here. 
				$('#sformLIST').submit(function (){
					$('#flexLIST').flexOptions({newp: 1}).flexReload();
					return false;
				});
			</script>

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
			
			<!--Separator table -->
			<table width="90%">
				<tr width="100%" height="20">
					<td width="30" align="left" valign="top" background="images/sepfill.jpg">
						<img src="images/sepleft.jpg" width="30" height="20" valign="top">
					</td>
					<td align="center" valign="top" background="images/sepfill.jpg"></td>
					<td width="30" align="right" valign="top" background="images/sepfill.jpg">
						<img src="images/sepright.jpg" width="30" height="20" valign="top">
					</td>
				</tr>
				<tr width="100%" height="5"><td>&nbsp;</td></tr>
			</table>
			<!-- End of separator table -->
			
			<!-- Create a playlist section -->
			<?php
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				// If something was posted...
				
				if (isset($_POST["createPL"])) {
					$pl_name = $_REQUEST['new_PL'];
					if (trim($pl_name) == "") {
						print ("<b>Please enter a name for your new playlist.</b><br /><br />");
					} else {
						$plCreate = createPlaylist($username, $pl_name);
						
						if ($plCreate == false) {
							print ("<b>Error creating playlist. Please try again later.</b><br /><br />");
						}
					}
				} 
			}
			?>
			
			<form action="<?php $_SERVER['SCRIPT_NAME']?>" method="POST">
				<table width="100%" border="0" align="left">
					<tr width="100%"><td>Playlist Name:</td></tr>
					<tr width="100%"><td><input type="text" name="new_PL"></td></tr>
					<tr width="100%"><td><br /><input type="submit" name="createPL" value="Create Playlist"></td></tr>
					<tr width="100%" height="5"><td>&nbsp;</td></tr>
				</table>
			</form>
			<!-- End create a playlist section -->
			
			<!--Separator table -->
			<table width="90%">
				<tr width="100%" height="20">
					<td width="30" align="left" valign="top" background="images/sepfill.jpg">
						<img src="images/sepleft.jpg" width="30" height="20" valign="top">
					</td>
					<td align="center" valign="top" background="images/sepfill.jpg"></td>
					<td width="30" align="right" valign="top" background="images/sepfill.jpg">
						<img src="images/sepright.jpg" width="30" height="20" valign="top">
					</td>
				</tr>
				<tr width="100%" height="5"><td>&nbsp;</td></tr>
			</table>
			<!-- End of separator table -->

			<!-- Delete a playlist section -->
			<?php
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				// If something was posted...
				
				if (isset($_POST["deletePL"])) {
					// No need to do any checks here, there user has alerady been asked
					// if they really want to delete the playlist - just send the function call
					$plToDel = $_REQUEST['del_PL'];
					
					if ($plToDel == "Library") {
						print ("<b>You cannot delete the library playlist.</b><br /><br />");
					} else if ($plToDel == "") {
						print ("<b>Please select a playlist to delete.</b><br /><br />");
					} else {
						$delPL = deletePlaylist($username, $plToDel);
						
						if ($delPL == false) {
							print ("<b>Error creating playlist. Please try again later.</b><br /><br />");
						}
					}
				}
			}
			?>
			
			 <script type="text/javascript">
				// Wait for the window to load - force functionality once window has loaded
				window.onload = function() {
				
					// Assign a function to run when link ID'd correctly is clicked
					var a = document.getElementById("delPL");
					
					a.onclick = function() {
					
						// Define a variable for the name of the playlist selected
						$plName = ($("body").data("selPL"));
					
						/* If the name of the list is undefined, blank, or Library, we could make a 
						* message box that simply says:  
						* alert ("Please select a playlist to delete");
						* But, in trying to keep the website the same all over, we
						* will pass a PHP call with the playlist name being blank and
						* let the PHP function print directly to the page
						*/
						if (!$plName) {
							$plName = "";			// If no playlist is selected, set the name to blank
							$confirmDel = true;
						} else if ($plName == "Library") {
							$confirmDel = true;
						} else {
							$confirmDel = confirm("Are you sure you want to delete " + $plName + "?");
						}
					
						if ($confirmDel == true){
							var form = document.createElement("form");
							form.action = "<?php $_SERVER['SCRIPT_NAME']?>";
							form.method = "POST"
					
							inputPL = document.createElement("input");
							inputPL.name = "del_PL";
							inputPL.value = ($plName);
	
							inputSub = document.createElement("input");
							inputSub.name = "deletePL";
							inputSub.value = "Delete Playlist";
							
							form.appendChild(inputPL);
							form.appendChild(inputSub);
						
							document.body.appendChild(form);
							form.submit();
						}
					}
				}
			</script>
			
			<a id="delPL" style="text-decoration: underline; color: Red;"} >Delete Playlist</a>
		
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
		
		<td align="left" valign="top">
		
<!-- table containing song list -->
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
			<img src="images/ltbar.jpg" width="27" height="101" valign="top"><br />
			<img src="images/lbbar.jpg" width="27" height="101" valign="bottom"><br />
		</td>
		
		<!-- Song list container -->
		<!-- This section will NOT contain the same class as the rest of the page -->
		<td colspan="3" align="left">
			<table id="flexSONG" style="display:none" width="100%" align="left"></table>
			<?php
				// Create a session check that will allow the following page to run
				// The check allows the page to be run by ONLY the system
				// The session variable will be turned off (set to 0) by the page ran
				$_SESSION['systemCheck'] = "1";
			?>
				<script type="text/javascript">
					var activeSong;
					var selected = [];

					$("#flexSONG").flexigrid({
						url: 'songList.php',
						dataType: 'json',
						colModel: [
							{display: 'Title', name: 'title', width: 400, sortable: true, align: 'left', process: getActive},
							{display: 'Artist', name: 'artist', width: 250, sortable: true, align: 'left'},
							{display: 'Album', name: 'album', width: 250, sortable: true, align: 'left'},
							{display: 'File', name: 'mp3', width: 1, sortable: true, hide: true, align: 'left'},
							],
						onSuccess: function(){init_playlist()},
					});


					function getActive(celDiv, id) {
						$(celDiv).click(function() {

							//set the song to be played on double click
							if(id)
								activeSong = id;
							else
								activeSong = 0;
						});
					}

					function getSelected() {
						//set the list of currently selected songs
						selected = [];
						$('#flexSONG .trSelected').each(function() {
							var col = [];
							$(this).find('div').each(function() {
								col.push($(this).text());
							});
							selected.push(col);
						});
					}

					function init_playlist() {
						if(init) {
							var currentPL = [];

							//get the song list on flexigrid
							$('#flexSONG tr').each(function() {
								var newSong = {
									title: $('td[abbr="title"] >div', this).text(),
									artist: $('td[abbr="artist"] >div', this).text(),
									album: $('td[abbr="album"] >div', this).text(),
									mp3: $('td[abbr="mp3"] >div', this).text()
								};

								currentPL.push(newSong);
							});
							
							myPlaylist.setPlaylist(currentPL);

							init = false;
						}
					}

					// This function adds parameters to the post of flexigrid		
					function addFormData(){
						// Passing a form object to serializeArray will get the valid data from all the objects, but, if the you pass a non-form object, 
						// you have to specify the input elements that the data will come from
						var dt = $('#sformSONG').serializeArray();
						$("#flexSONG").flexOptions({params: dt});
						return true;
					};
	
					// I'm not sure what this does, but it doesn't hurt to leave it here. 
					$('#sformSONG').submit(function (){
						$('#flexSONG').flexOptions({newp: 1}).flexReload();
						return false;
					});
					
					function addToList(com,grid) { 
						// This actually is to remove a line, but we won't be using this 
						// code for that - modify to add to playlists
						if (com == 'Add') { 
							if($('.trSelected').length > 0){ 
								if(confirm('Remove ' + $('.trSelected').length + ' rows?')){ 
                                
									var items = $('.trSelected'); 
									var itemlist = ''; 
									for(i = 0; i < items.length; i++){
										alert("Item count = " + i + " | Value = " + items[i]);
									//    itemlist += items[i].id.substr(3)+"_"; 
									}
								
									return itemlist;		//Can to redirect to the remove page script 
								} 
							} else{ 
							alert('You have to select a row.'); 
							} 
						}	 
					}

					$('#flexSONG').dblclick(function(e) { 
						var currentPL = [];

						//get the song list on flexigrid
						$('#flexSONG tr').each(function() {
							var newSong = {
								title: $('td[abbr="title"] >div', this).text(),
								artist: $('td[abbr="artist"] >div', this).text(),
								album: $('td[abbr="album"] >div', this).text(),
								mp3: $('td[abbr="mp3"] >div', this).text()
							};

							currentPL.push(newSong);
						});
						
						myPlaylist.setPlaylist(currentPL);
						myPlaylist.select(activeSong);
						myPlaylist.option("autoPlay", true);
					});
					
				</script>
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
			<button type="button" id="remove" onclick="remove_from_playlist()">remove</button> 
			<button type="button" id="delete" onclick="delete_songs">delete</button> 
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
