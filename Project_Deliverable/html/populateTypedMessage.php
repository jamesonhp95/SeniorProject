<?php
	session_start();
	require_once("../internalIncludes/dbconfig.php");
	
    $is_mentor = $_SESSION['is_mentor'];
    $user = $_SESSION['user_id'];
    $view_user_id = $_REQUEST['menid'];
    $c_id = $_REQUEST["cid"];
	
	$call = "CALL GetConversationStatus('$c_id');";
    $q = $conn->query($call);
    $res = $q->fetch_assoc();
    $chat_locked = $res['status'];
    mysqli_free_result($res);
    mysqli_next_result($conn);
	
	$tempPlaceholder = "";
    $sendButtonLocked = "";
    $textareaLocked = "";
	if($chat_locked == 3) {
		$tempPlaceholder = "Chat has been denied by the Mentor.";
		$sendButtonLocked = "disabled";
		$textareaLocked = "disabled";
	}
    else if($chat_locked == 2) {
	    $tempPlaceholder = "Chat is currently locked until the Mentor accepts.";
	    $sendButtonLocked = "disabled";
	    $textareaLocked = "disabled";
    }
	else if($chat_locked == 1) {
		$tempPlaceholder = "Chat is currently pending until the Mentor has more room for new Mentees.";
	    $sendButtonLocked = "disabled";
	    $textareaLocked = "disabled";
	}
    else {
	    $tempPlaceholder = "Type a message...";
	    $sendButtonLocked = "onclick=\"sendMessage();\"";
	    $textareaLocked = "";
    }
	/*
	$innerHtml = "<div class=\"input_msg_write\"><textarea rows=\"1\" id=\"typed_message\" class=\"type_msg_textarea\" name=\"typed_message\"";
	if($chat_locked == 1) {
		$innerHtml .= "placeholder=\"Chat is currently locked until Mentor accepts.\"";
	}
	else {
		$innerHtml .= "placeholder=\"Type a message...\"";
	}
	$innerHtml .= "onkeydown=\"grow();\"";
	if($chat_locked == 1) { 
		$innerHtml .= "disabled"; 
	}
	$innerHtml .= "></textarea><button id=\"send_msg_btn\" class=\"msg_send_btn\" type=\"submit\"";
	if($chat_locked != 1) { 
		$innerHtml .= "onclick=\"sendMessage(typed_message.value);\""; 
	} 
	else { 
		$innerHtml .= "disabled"; 
	}
	$innerHtml .= "><i class=\"fa fa-paper-plane-o\" aria-hidden=\"true\"></i></button></div>";*/
	
	
	$innerHtml .= "<div class=\"type_msg\" id=\"type_msg_area\"><div class=\"input_msg_write\"><textarea rows=\"1\" id=\"typed_message\" class=\"type_msg_textarea\" name=\"typed_message\" placeholder=\"$tempPlaceholder\" onkeydown=\"grow();\" $textareaLocked></textarea><button id=\"send_msg_btn\" class=\"msg_send_btn\" type=\"submit\" $sendButtonLocked><i class=\"fa fa-paper-plane-o\" aria-hidden=\"true\"></i></button></div></div>";
	echo $innerHtml;
	
	