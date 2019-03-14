<?php
	session_start();
	require_once("../internalIncludes/dbconfig.php");
	
	$u_id = $_REQUEST['id'];
	$call = "CALL SetUserEnabled('$u_id');";
	$q = $conn->query($call);
	mysqli_free_result($q);
	mysqli_next_result($conn);
	
	echo "succes";
?>