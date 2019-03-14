<?php
  session_start();
  require_once("../internalIncludes/dbconfig.php");
  
  $user_id = $_SESSION['user_id'];
  $selected = $_REQUEST['q'];
  $c_ids = array();
  $call = "CALL GetUsersConversations('$user_id');";
  $q = $conn->query($call);
  while($res = $q->fetch_assoc()) {
	$c_ids[] = $res['ConversationID'];
  }
  mysqli_free_result($res);
  mysqli_next_result($conn);
  
  $chat_ids = array();
  $chat_names = array();
  foreach($c_ids as $id) {
	  $call = "CALL GetConversationUsers('$id');";
	  $q = $conn->query($call);
	  while($res = $q->fetch_assoc()) {
		  if($res['UserID'] != $user_id) {
			  $chat_ids[] = $res['UserID'];
			  }
		}
		mysqli_free_result($res);
		mysqli_next_result($conn);
	}
	
	$chat_locked = array();
	foreach($c_ids as $id) {
	  $call = "CALL GetConversationStatus('$id');";
	  $q = $conn->query($call);
	  $res = $q->fetch_assoc();
	  $chat_locked[] = $res['status'];
	  mysqli_free_result($res);
	  mysqli_next_result($conn);
    }
	
	foreach($chat_ids as $person) {
		$call = "CALL GetUserFullName('$person');";
		$q = $conn->query($call);
		while($res = $q->fetch_assoc()) {
			$chat_names[] = $res['Name'];
		}
		mysqli_free_result($res);
		mysqli_next_result($conn);
	}
  
  $chat_dates = array();
  $date = "";
  $chat_prev = array();
  foreach($c_ids as $id) {
	  $call = "CALL GetConversationPeek('$id');";
	  $q = $conn->query($call);
	  $res = $q->fetch_assoc();
	  $_SESSION['p_message_id'] = $res['MessageID'];
	  $chat_prev[] = $res['Content'];
	  $date = $res['ModifiedDateTime'];
	  $date = strtotime($date);
	  $chat_dates[] = $date;//date("M jS", $date);
	  mysqli_free_result($res);
	  mysqli_next_result($conn);
  }
  
  $conversationInformation = array();
  
  if(count($c_ids)>0) {
		$conversationInformation = array($chat_dates, $c_ids, $chat_ids, $chat_names, $chat_locked, $chat_prev);
		array_multisort($conversationInformation[4], SORT_NUMERIC, $conversationInformation[0], SORT_NUMERIC, SORT_DESC, $conversationInformation[1], $conversationInformation[2], $conversationInformation[3], $conversationInformation[5]);
  
		for($x = 0; $x<count($conversationInformation[0]); $x++) {
			$d = $conversationInformation[0][$x];
			$conversationInformation[0][$x] = date("M jS", $d);
		}
  }
  
  $innerHtml = "";
  for($cnt = 0; $cnt < count($conversationInformation[0]); $cnt++) {
	$i_chat_date = $conversationInformation[0][$cnt];
	$id = $conversationInformation[1][$cnt];
	$i_chat_id = $conversationInformation[2][$cnt];
	$i_chat_name = $conversationInformation[3][$cnt];
	$i_chat_prev = $conversationInformation[5][$cnt];
	$val_id = "selected_id";
	$val_id .= $i_chat_id;
	if(strlen($i_chat_prev) > 26) {
		$i_chat_prev = substr($i_chat_prev, 0, 26)."...";
	}
	if($id == $selected) {
		$innerHtml .= "<button type=\"submit\" id=\"$id\" value=\"$id\" style=\"background: #c6c6c6;\" onclick=\"populateMessages(this.value, $val_id.value);\"><input type=\"hidden\" id=\"$val_id\" value=\"$i_chat_id\"></input><div class=\"chat_list\"><div class=\"chat_people\"><div class=\"chat_img\"><img src=\"http://146.187.134.42/Images/M-Icon.png\"></div><div class=\"chat_ib\"><h5>$i_chat_name<span class=\"chat_date\">$i_chat_date</span></h5><p>$i_chat_prev</p></div></div></div></button>";
	}
	else {
		$innerHtml .= "<button type=\"submit\" id=\"$id\" value=\"$id\" onclick=\"populateMessages(this.value, $val_id.value);\"><input type=\"hidden\" id=\"$val_id\" value=\"$i_chat_id\"></input><div class=\"chat_list\"><div class=\"chat_people\"><div class=\"chat_img\"><img src=\"http://146.187.134.42/Images/M-Icon.png\"></div><div class=\"chat_ib\"><h5>$i_chat_name<span class=\"chat_date\">$i_chat_date</span></h5><p>$i_chat_prev</p></div></div></div></button>";
	}
  }
	if($innerHtml == "" && $_SESSION['IsMentor'] == 0) {
	  $id = "default";
	  $val_id = "selected_id";
	  $val_id .= $id;
	  $i_chat_name = "Start a Conversation!";
	  $i_chat_prev = "Go to search for a mentor!";
	  $innerHtml = "<button type=\"submit\" id=\"$id\" value=\"$id\" style=\"background: #c6c6c6;\" onclick=\"populateMessages(this.value, $val_id.value);\"><input type=\"hidden\" id=\"$val_id\" value=\"$i_chat_id\"></input><div class=\"chat_list\"><div class=\"chat_people\"><div class=\"chat_img\"><img src=\"Images/M-Icon.png\"></div><div class=\"chat_ib\"><h5>$i_chat_name</h5><p>$i_chat_prev</p></div></div></div></button>";
	}
	echo $innerHtml;
?>