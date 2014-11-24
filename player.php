<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<?php 
	// Include the file with all of the calling functions
	include 'clientCalls.php';
	session_start();
	
	// If the user is not logged in (they try to access this page directly,
	// direct them to the login page
	if ($_SESSION['username'] == "") {
		// Close any session that may be open
		session_destroy();
		header("Location: login.php");
	}
	
	/* The following line has been removed because of a script conflict 
	 **************
	 * 	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
	 **************
	 * Another jquery.min.js is already in use - I left the most recent version posted. 
	 * IF THE ABOVE IS USED, IT MUST REPLACE THE ONE IN PLACE, OR BE PLACED IN THE CODE PRIOR TO THE EXISTING ONE
	 * Placement AFTER the existing version (if existing version is on line 30, the above MUST be placed before
	 * that line), results in neither version working.
	 *
	 * In Addition, the jPlayer code MUST be placed BEFORE the flexiGrid code.
	 */
?>

<html XMLNS="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>music.Apolymoxic.com</title>
	
	<link type="text/css" href="skins/blue.monday/jplayer.blue.monday.css" rel="stylesheet" />
	<script type="text/javascript" src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
	<script type="text/javascript" src="js/jplayer/jquery.jplayer.min.js"></script>
	<script type="text/javascript" src="js/jplayer/jplayer.playlist.min.js"></script>
	<script type="text/javascript" src="js/jplayer/jquery.jplayer.inspector.js"></script>
	
	<link rel="stylesheet" type="text/css" href="css/flexigrid.css">
	<script type="text/javascript" src="js/flexigrid/flexigrid.js"></script>
	
	<script type="text/javascript">
    $(document).ready(function(){
		var PL1 = [{
				title:"Cro Magnon Man",
				mp3:"http://www.jplayer.org/audio/mp3/TSP-01-Cro_magnon_man.mp3",
				oga:"http://www.jplayer.org/audio/ogg/TSP-01-Cro_magnon_man.ogg"
			},
			{
				title:"Your Face",
				mp3:"http://www.jplayer.org/audio/mp3/TSP-05-Your_face.mp3",
				oga:"http://www.jplayer.org/audio/ogg/TSP-05-Your_face.ogg"
			}];

		var PL2 = [{
				title:"Tempered Song",
				artist:"Miaow",
				mp3:"http://www.jplayer.org/audio/mp3/Miaow-01-Tempered-song.mp3",
				oga:"http://www.jplayer.org/audio/ogg/Miaow-01-Tempered-song.ogg"
			},
			{
				title:"Lentement",
				artist:"Miaow",
				mp3:"http://www.jplayer.org/audio/mp3/Miaow-03-Lentement.mp3",
				oga:"http://www.jplayer.org/audio/ogg/Miaow-03-Lentement.ogg"
			}];

		var myPlaylist = new jPlayerPlaylist(
			{
			jPlayer: "#jquery_jplayer_1",
			cssSelectorAncestor: "#jp_container_1"
			}, 
			[],
			{
				swfPath: "/js",
				supplied: "oga, mp3",
				wmode: "window",
				smoothPlayBar: true,
				keyEnabled: true
			}
		);

		//binds
		$("#jquery_jplayer_1").bind($.jPlayer.event.setmedia, function(event) {
			var info = myPlaylist.playlist[myPlaylist.current].title;
			info += " | ";
			info += myPlaylist.playlist[myPlaylist.current].artist;
			info += " | ";
			info += myPlaylist.playlist[myPlaylist.current].album;

			document.getElementById("jp-dis").innerHTML = info;
		});

		//Functions
		$("#clear").click(function() {
			myPlaylist.remove();
		});
		
		$("#setPL1").click(function() {
			myPlaylist.setPlaylist(PL1);
		});

		$("#setPL2").click(function() {
			myPlaylist.setPlaylist(PL2);
		});


    });
  </script>
</head>

<body>

<table width="90%" border="0" align="center" cellpadding="0">
	<tr width="100%" height="130">
		<td width="100%" align="center" valign="bottom">
			<IMG SRC="images/banner.jpg" WIDTH="950" HEIGHT="118" BORDER="0" ALT="">
		</td>
	</tr>
	<tr width="100%">
		<td width="100%" valign="top" align="right">
			<?php
				echo "Welcome, " . $_SESSION['username'] . "!<br />\n";
				echo "\t" . $_SESSION['email'] . "<br />\n";
			?>
			<a href="logout.php">Logout</a>
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
	<tr width="100%">
		<td width="100%" align="center" valign="top">
			<button type="button" id="clear"> Clear Playlist </button>
			<button type="button" id="setPL1"> Set Playlist 1</button>
			<button type="button" id="setPL2"> Set Playlist 2</button>
		</td>
	</tr>
	
	<tr width="100%" height="25">
		<td width="100%" height="100%" align="center">
			&nbsp;
		</td>
	</tr>
</table>

<table width="90%" border="1" align="center">
	<tr width="100%">
		<td width="200" align="left">
			
			<table id="flex1" style="display:none">
				<script type="text/javascript">
					$("#flex1").flexigrid({
						url: 'flexiTest.php',
						dataType: 'json',
						colModel : [
							{display: 'Playlist', name : 'pl', width : 185, sortable : true, align: 'left'},
							],
						sortname: "pl",
						sortorder: "asc",
						usepager: false,
						title: 'Playlists',
						showTableToggleBtn: false,
						width: 200,
						onSubmit: addFormData,
						//height: 200
					});

					//This function adds paramaters to the post of flexigrid. You can add a verification as well by return to false if you don't want flexigrid to submit					
					function addFormData(){
						//passing a form object to serializeArray will get the valid data from all the objects, but, if the you pass a non-form object, you have to specify the input elements that the data will come from
						var dt = $('#sform').serializeArray();
						$("#flex1").flexOptions({params: dt});
						return true;
					}
	
					$('#sform').submit(function (){
						$('#flex1').flexOptions({newp: 1}).flexReload();
						return false;
					});
				</script>
			</table>
		</td>
		
		
		<td align="left" valign="top">
			
			<table id="flex2" style="display:none" width="100%"></table>
				<script type="text/javascript">
					$("#flex2").flexigrid({
						url: 'flexiTest.php',
						dataType: 'json',
						colModel : [
							{display: 'Title', name : 'title', width : 350, sortable : true, align: 'left'},
							{display: 'Artist', name : 'artist', width : 200, sortable : true, align: 'left'},
							{display: 'Album', name : 'album', width : 200, sortable : true, align: 'left'},
							{display: 'Length', name : 'length', width : 50, sortable : true, align: 'left'},
							],
						searchitems : [
							{display: 'Title', name : 'title', isdefault: true},
							{display: 'Artist', name : 'artist'},
							{display: 'Album', name : 'album'},
							],
						sortname: "title",
						sortorder: "asc",
						usepager: true,
						title: 'Songs',
						useRp: true,
						rp: 15,
						showTableToggleBtn: true,
						//width: 800,
						onSubmit: addFormData,
						//height: 200
					});

					//This function adds paramaters to the post of flexigrid. You can add a verification as well by return to false if you don't want flexigrid to submit			
					function addFormData(){
						//passing a form object to serializeArray will get the valid data from all the objects, but, if the you pass a non-form object, you have to specify the input elements that the data will come from
						var dt = $('#sform').serializeArray();
						$("#flex2").flexOptions({params: dt});
						return true;
					}
	
					$('#sform').submit(function (){
						$('#flex1').flexOptions({newp: 1}).flexReload();
						return false;
					});
				</script>
		</td>
	</tr>
</table>



</body>
</html>
