<?php
  session_start();
  require_once("../internalIncludes/dbconfig.php");
  $option = $_REQUEST["o"];
  $preMsg = $_REQUEST["q"];
  $c_id = $_REQUEST["c"];
  $mentee_id = $_REQUEST["m"];
  $user_id = $_SESSION['user_id'];
  
  $mess = mysqli_real_escape_string($conn, $preMsg);
  
  if($option != 3) {
	  $call = "CALL GetConversationPeek('$c_id');";
	  $q = $conn->query($call);
	  $res = $q->fetch_assoc();
	  $p_msg_id = $res['MessageID'];
	  mysqli_free_result($res);
	  mysqli_next_result($conn);
		  
	  $call = "CALL CreateMessage('$c_id', '$user_id', '$p_msg_id', '$mess');";
	  $q = $conn->query($call);
	  $res = $q->fetch_assoc();
	  mysqli_free_result($res);
	  mysqli_next_result($conn);
	  
	  if($option == 0) {
		$call = "CALL SetConversationAccepted('$c_id');";
		$q = $conn->query($call);
		$res = $q->fetch_assoc();
		
		mysqli_free_result($res);
		mysqli_next_result($conn);
		
		$call = "CALL CreateMentorPair('$user_id', '$mentee_id');";
		$q = $conn->query($call);
		$res = $q->fetch_assoc();
		$success = $res['Confirmation'];
		mysqli_free_result($res);
		mysqli_next_result($conn);
	  }
	  else if($option == 1) {
		$call = "CALL SetConversationPending('$c_id');";
		$q = $conn->query($call);
		$res = $q->fetch_assoc();
		
		mysqli_free_result($res);
		mysqli_next_result($conn);
	  }
	  else if($option == 2) {
		$call = "CALL SetConversationDenied('$c_id');";
		$q = $conn->query($call);
		$res = $q->fetch_assoc();
		
		mysqli_free_result($res);
		mysqli_next_result($conn);
	  }
  }
  else {
	  $call = "CALL ReportUser('$user_id', '$mentee_id', '$mess');";
	  $q = $conn->query($call);
	  $res = $q->fetch_assoc();
	  mysqli_free_result($res);
	  mysqli_next_result($conn);
  }
  $now = new DateTime();
  $now_time = date("g:ia", $now->getTimestamp());
  $now_date = date("M jS", $now->getTimestamp());
  
  echo "<div class=\"outgoing_msg\"><div class=\"sent_msg\"><p>$preMsg</p><span class=\"time_date\">$now_time  |  $now_date</span></div></div>";
?>