<?php
  session_start();
  require_once("../internalIncludes/dbconfig.php");
  $user = $_SESSION['user_id'];
  $con_id = $_REQUEST["id"];
  $preMsg = $_REQUEST["msg"];
  $message = mysqli_real_escape_string($conn, $preMsg);
  $call = "CALL GetConversationPeek('$con_id');";
  $q = $conn->query($call);
  $res = $q->fetch_assoc();
  $p_msg_id = $res['MessageID'];
  mysqli_free_result($res);
  mysqli_next_result($conn);
  
  $call = "CALL CreateMessage('$con_id', '$user', '$p_msg_id', '$message');";
  $q = $conn->query($call);
  $res = $q->fetch_assoc();
  $sent_msg_id = $res['MessageID'];
  mysqli_free_result($res);
  mysqli_next_result($conn);
  
  $now = new DateTime();
  $now_time = date("g:ia", $now->getTimestamp());
  $now_date = date("M jS", $now->getTimestamp());
  
  echo "<div class=\"outgoing_msg\"><div class=\"sent_msg\"><p>$preMsg</p><span class=\"time_date\">$now_time  |  $now_date</span></div></div>";
?>