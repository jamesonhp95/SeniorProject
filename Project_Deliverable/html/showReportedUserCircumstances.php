<?php
	session_start();
	require_once("../internalIncludes/dbconfig.php");
	$entry_id = $_REQUEST['eId'];
	
	$call = "CALL GetIndividualReport('$entry_id');";
	$q = $conn->query($call);
	$r = $q->fetch_assoc();
	$reported_user_id = $r['ReportedUserID'];
	$reporter_user_id = $r['ReporterID'];
	$report_details = $r['Details'];
	$report_date = $r['ReportDate'];
	mysqli_free_result($q);
	mysqli_next_result($conn);
	
	$call = "CALL GetUser('$reported_user_id');";
	$q = $conn->query($call);
	$r = $q->fetch_assoc();
	$reported_name = $r['Name'];
	mysqli_free_result($q);
	mysqli_next_result($conn);
	
	$call = "CALL GetUser('$reporter_user_id');";
	$q = $conn->query($call);
	$r = $q->fetch_assoc();
	$reporter_name = $r['Name'];
	mysqli_free_result($q);
	mysqli_next_result($conn);
	
	$call = "CALL GetConversationExists('$reported_user_id', '$reporter_user_id');";
	$q = $conn->query($call);
	$r = $q->fetch_assoc();
	$c_id = $r['ConversationID'];
	mysqli_free_result($q);
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
	
	
	$innerHtml = "";
	$innerHtml .= "<div class=\"heading_srch\"><div class=\"recent_heading\"><h4>Reported User: $reported_name<br/>User that Sent Report: $reporter_name</h4></div><div class=\"srch_bar\"><button type=\"button\" style=\"margin-right: 3px;\" onclick=\"banSelectedUser();\">Ban Reported User</button><button type=\"button\" onclick=\"dismissSelectedUser();\">Dismiss report</button></div><div style=\"width: 100%; font-size: 18px;\"><br/><br/>Details of the Report: $report_details</div></div>";
	
	$cnt = 0;
	foreach($msg_contents as $i_msg) {
		$i_msg_time = $msg_times[$cnt];
		$i_msg_date = $msg_dates[$cnt];
		$i_owner_id = $msg_owner[$cnt];
		$cnt++;
		if($i_owner_id == $reported_user_id) {
			$innerHtml .= "<div class=\"incoming_msg\"><div class=\"incoming_msg_img\"><img src=\"http://146.187.134.42/Images/M-Icon.png\"></div><div class=\"received_msg\"><div class=\"received_withd_msg\"><p style=\"font-weight:bold; border-bottom: 1px solid #000; float: left;\">$reported_name</p><p>$i_msg</p><span class=\"time_date\">$i_msg_time  |  $i_msg_date</span></div></div></div>";
		}
		else {
			$innerHtml .= "<div class=\"outgoing_msg\"><div class=\"sent_msg\"><p style=\"font-weight:bold; border-bottom: 1px solid #000; float: right;\">$reporter_name</p><p>$i_msg</p><span class=\"time_date\">$i_msg_time  |  $i_msg_date</span></div></div>";
		}
	}
	
	echo $innerHtml;
?>