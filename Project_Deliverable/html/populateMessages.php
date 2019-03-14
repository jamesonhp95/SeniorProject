<?php
  session_start();
  require_once("../internalIncludes/dbconfig.php");
  $is_mentor = $_SESSION['IsMentor'];
  $user = $_SESSION['user_id'];
  $view_user_id = $_REQUEST['menid'];
  $c_id = $_REQUEST["cid"];
  
  $call = "CALL GetUserFullName('$view_user_id');";
  $query = $conn->query($call);
  $res = $query->fetch_assoc();
  $mentor_name = $res['Name'];
  mysqli_free_result($res);
  mysqli_next_result($conn);
  
  $call = "CALL GetConversationStatus('$c_id');";
  $q = $conn->query($call);
  $res = $q->fetch_assoc();
  $chat_locked = $res['status'];
  mysqli_free_result($res);
  mysqli_next_result($conn);
  
  $call = "CALL GetConversation('$c_id');";
  $query = $conn->query($call);
  while($res = $query->fetch_assoc()) {
	  $msg_owner[] = $res['OwnerID'];
      $msg_contents[] = $res['Content'];
      $msg_date = $res['ModifiedDateTime'];
      $msg_date = strtotime($msg_date);
      $msg_dates[] = date("M jS", $msg_date);
      $msg_times[] = date("g:ia", $msg_date);
  }
  mysqli_free_result($res);
  mysqli_next_result($conn);
  $buttonLayout = "";
  $headingButton = "";
  
  if($is_mentor == 1) {
	  $headingButton = $mentor_name;
	  if($chat_locked == 2) {
		  $buttonLayout = "<span><button type=\"submit\" onclick=\"acceptMakePopUp();\">Accept</button><button type=\"submit\" onclick=\"pendingMakePopUp();\">Pending</button><button type=\"submit\" onclick=\"denyMakePopUp();\">Deny</button></span>";
	  }
	  if($chat_locked == 1) {
		  $buttonLayout = "<span><button type=\"submit\" onclick=\"acceptMentee();\">Accept</button><button type=\"submit\" onclick=\"denyMentee();\">Deny</button></span>";
	  }
  }
  else {
	  $headingButton = "<button type=\"submit\" onclick=\"goToProfile();\">$mentor_name</button>";
	  if($chat_locked == 2) {
		  $buttonLayout = "<span>Pending Mentor Approval</span>";
	  }
	  if($chat_locked == 1) {
		  $buttonLayout = "<span>Pending Mentor Availability</span>";
	  }
  }
  $innerHtml = "";
  $cnt = 0;
  /*$innerHtml .= "<div class=\"heading_srch\"><div class=\"recent_heading\"><h4>$headingButton</h4></div><div class=\"srch_bar\"> $buttonLayout</div></div>";*/
  foreach($msg_contents as $i_msg) {
	$i_msg_time = $msg_times[$cnt];
	$i_msg_date = $msg_dates[$cnt];
	$i_owner_id = $msg_owner[$cnt];
	$cnt++;
	if($i_owner_id != $user) {
		$innerHtml .= "<div class=\"incoming_msg\"><div class=\"incoming_msg_img\"><img src=\"http://146.187.134.42/Images/M-Icon.png\"></div><div class=\"received_msg\"><div class=\"received_withd_msg\"><p>$i_msg</p><span class=\"time_date\">$i_msg_time  |  $i_msg_date</span></div></div></div>";
	}
	else {
		$innerHtml .= "<div class=\"outgoing_msg\"><div class=\"sent_msg\"><p>$i_msg</p><span class=\"time_date\">$i_msg_time  |  $i_msg_date</span></div></div>";
	}
  }
  echo $innerHtml;
?>
