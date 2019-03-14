<?php
    session_start();
    require_once("../internalIncludes/dbconfig.php");
	
	$user = $_SESSION['user_id'];
	$view_id = $_SESSION['view_user_id'];
	$mess = $_REQUEST["q"];
	$mess = mysqli_real_escape_string($conn, $mess);
	
	$call = "CALL GetConversationExists('$user', '$view_id');";
	$q = $conn->query($call);
	$r = $q->fetch_assoc();
	$c_id = $r['ConversationID'];
	mysqli_free_result($r);
	mysqli_next_result($conn);
	if($c_id != -1) {
		$call = "CALL GetConversationStatus('$c_id');";
		$q = $conn->query($call);
		$r = $q->fetch_assoc();
		$isLocked = $r['status'];
		mysqli_free_result($r);
		mysqli_next_result($conn);
	    
		if($isLocked == 3) {
			$call = "CALL SetConversationUnreviewed('$c_id');";
			$q = $conn->query($call);
			$res = $q->fetch_assoc();
			$succ = $res['Confirmation'];
			mysqli_free_result($res);
			mysqli_next_result($conn);
			if($succ == 0) {
				$call = "CALL GetConversationPeek('$c_id');";
				$q = $conn->query($call);
				$res = $q->fetch_assoc();
				$p_m_id = $res['MessageID'];
				mysqli_free_result($res);
				mysqli_next_result($conn);
				
				$call = "CALL CreateMessage('$c_id', '$user', '$p_m_id', '$mess');";
				$q = $conn->query($call);
				$res = $q->fetch_assoc();
				mysqli_free_result($res);
				mysqli_next_result($conn);
			}
		}
		else {
		  echo "An error has occured, please reload and try again.";
		}
	}
	else {
		$call = "CALL CreateConversation();";
		$q = $conn->query($call);
		$res = $q->fetch_assoc();
		$c_id = $res['ConversationID'];
		mysqli_free_result($res);
		mysqli_next_result($conn);

		$call = "CALL CreateConversationUser('$user', '$c_id');";
		$q = $conn->query($call);
		$res = $q->fetch_assoc();
		$validation = $res['Confirmation'];
		//do validation checking
		mysqli_free_result($res);
		mysqli_next_result($conn);

		$call = "CALL CreateConversationUser('$view_id', '$c_id');";
		$q = $conn->query($call);
		$res = $q->fetch_assoc();
		$validation = $res['Confirmation'];
		//do validation checking
		mysqli_free_result($res);
		mysqli_next_result($conn);

		$call = "CALL CreateMessage('$c_id', '$user', 0, '$mess');";
		$q = $conn->query($call);
		$res = $q->fetch_assoc();
		mysqli_free_result($res);
		mysqli_next_result($conn);
		echo "Message has been sent: $mess";
	}
?>
