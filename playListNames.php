<?php
	session_start();
	
	// Check if the system is requesting the page. If it is, allow to run
	// If not, direct to login page
	//if ($_SESSION['systemCheck'] != "1"){
	//	header("Location: login.php");
	//}

    $page = isset($_POST['page']) ? $_POST['page'] : 1;
    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
    $sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'name';
    $sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'desc';
    $query = isset($_POST['query']) ? $_POST['query'] : false;
    $qtype = isset($_POST['qtype']) ? $_POST['qtype'] : false;


    $object = json_decode(file_get_contents("./playlists/" . $_SESSION["username"] . "Playlists.json"));
    $rows = array();

    $libEntry = new stdClass();
    $libEntry->playlistName = "Library";
    $rows []= $libEntry;

    foreach($object as $key => $member) {
        $playlistName = new stdClass();
        $playlistName->playlistName = $key;
        $rows []= $playlistName;
    }

    header("Content-type: application/json");
    $jsonData = array('page'=>$page,'total'=>0,'rows'=>array());
    
	foreach($rows AS $rowNum => $row){
		$entry = array("id" => $rowNum, "cell" => $row);
        $jsonData['rows'][] = $entry;
    }
    $jsonData['total'] = count($rows);
		
    echo json_encode($jsonData);
	
	// Allow page to be displayed for 10 seconds, then redirects
	header("Refresh:10; url=login.php");
	
	// Because the page SHOULD be requested by the system, turn the
	// system check variable off
	$_SESSION['systemCheck'] = "0";
?>
