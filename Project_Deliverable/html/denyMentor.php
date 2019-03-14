<?php
	session_start();
	require_once("../internalIncludes/dbconfig.php");
	include 'includes/emailFunctions.php';
	
	$i_id = $_REQUEST["id"];
	
	$call = "CALL getMentor('$i_id');";
	$q = $conn->query($call);
	$res = $q->fetch_assoc();
	$email = $res['Email'];
	mysqli_free_result($res);
	mysqli_next_result($conn);
	
	$call = "CALL DeleteMentor('$i_id');";
	$q = $conn->query($call);
	$res = $q->fetch_assoc();
	
	mysqli_free_result($res);
	mysqli_next_result($conn);
	
	
	sendMentorDenialNotification($email);
	
	echo "success";
?>