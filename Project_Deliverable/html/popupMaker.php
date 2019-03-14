<?php
  session_start();
  require_once("../internalIncludes/dbconfig.php");
  $q = $_REQUEST['q'];
  $o = $_REQUEST['o'];
  
  if($o == 0) {
	$mentorId = $_SESSION['user_id'];
	$call = "CALL GetMentor('$mentorId');";
	$query = $conn->query($call);
	$res = $query->fetch_assoc();
	$curMentees = $res['CurNumMentees'];
	$maxMentees = $res['MaxNumMentees'];
	mysqli_free_result($res);
	mysqli_next_result($conn);
	
	if($curMentees < $maxMentees) {
		$echoStmt = "<div class=\"start_convo_container\"><textarea style=\"width: 100% !important\" class=\"start_convo_message\" id=\"temp_msg\" name=\"temp_msg\" placeholder=\"$q\"></textarea><button class=\"start_convo_send_btn\" type=\"submit\" onclick=\"sendPopUpMessage();\">Send</button><button class=\"start_convo_cancel_btn\" type=\"submit\" onclick=\"cancelMessage();\">Cancel</button></div>";
	}
	else {
		$echoStmt = "It looks like you already have the maximum number of Mentees you signed up for, please send this Mentee a pending message if you wish to speak with them in the future!";
	}
  }
  else {
	$echoStmt = "<div class=\"start_convo_container\"><textarea style=\"width: 100% !important\" class=\"start_convo_message\" id=\"temp_msg\" name=\"temp_msg\" placeholder=\"$q\"></textarea><button class=\"start_convo_send_btn\" type=\"submit\" onclick=\"sendPopUpMessage();\">Send</button><button class=\"start_convo_cancel_btn\" type=\"submit\" onclick=\"cancelMessage();\">Cancel</button></div>";
  }
  echo $echoStmt;
?>
