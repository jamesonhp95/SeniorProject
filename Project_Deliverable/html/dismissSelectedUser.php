<?php
	session_start();
	require_once("../internalIncludes/dbconfig.php");
	
	$eId = $_REQUEST['eId'];
	
	$call = "CALL ReviewReport('$eId');";
	$q = $conn->query($call);
	mysqli_free_result($q);
	mysqli_next_result($conn);
	
	echo "success";
?>