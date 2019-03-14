<?php
	session_start();
	require_once("../internalIncludes/dbconfig.php");
	
	if(!isset($_SESSION['user_id'])) {
		echo "<script> window.location='LandingPage.php'; </script>";
		exit;
	}
	if(!isset($_SESSION['IsMentor'])) {
		echo "<script> window.location='LandingPage.php';</script>";
		exit;
	}
	if(!isset($_SESSION['IsEnabled'])) {
		echo "<script>window.location='LandingPage.php'; </script>";
		exit;
	}
	if($_SESSION['IsEnabled'] != 1) {
		echo "<script>location.href='AccountDisabled.php';</script>";
		exit;
	}
	
	if(isset($_POST['cid']) && isset($_POST['otherId'])) {
		if($_SESSION['IsMentor'] == 1) {
			$mentor_id = $_SESSION['user_id'];
			$mentee_id = $_POST['otherId'];
		}
		else {
			$mentee_id = $_SESSION['user_id'];
			$mentor_id = $_POST['otherId'];
		}
		$c_id = $_POST['cid'];
		
		$call = "CALL SetConversationDenied('$c_id');";
		$q = $conn->query($call);
		$res = $q->fetch_assoc();
		mysqli_free_result($res);
		mysqli_next_result($conn);
		
		$call = "CALL DeleteMentorPair('$mentor_id', '$mentee_id');";
		$q = $conn->query($call);
		$res = $q->fetch_assoc();
		mysqli_free_result($res);
		mysqli_next_result($conn);
		echo "success";
	}
	else {
		echo "failure";
	}
?>