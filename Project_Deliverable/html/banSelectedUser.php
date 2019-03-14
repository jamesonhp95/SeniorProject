<?php
	session_start();
	require_once("../internalIncludes/dbconfig.php");
	
	$banned_id = $_REQUEST['id'];
	$eId = $_REQUEST['eId'];
	
	$call = "CALL SuspendUser('$banned_id');";
	$q = $conn->query($call);
	mysqli_free_result($q);
	mysqli_next_result($conn);
	
	$call = "CALL ReviewReport('$eId');";
	$q = $conn->query($call);
	mysqli_free_result($q);
	mysqli_next_result($conn);
	
	echo "success";
?>