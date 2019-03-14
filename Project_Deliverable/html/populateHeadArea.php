<?php
	session_start();
	require_once("../internalIncludes/dbconfig.php");
	$is_mentor = $_SESSION['IsMentor'];
	$user_id = $_SESSION['user_id'];
	$c_id = $_REQUEST["cid"];
	
	$reportBtn = "<button id=\"dropDown\" type=\"button\" data-toggle=\"collapse\" data-target=\"#personaBarCollapse\" aria-expanded=\"false\" aria-controls=\"personaBarCollapse\" style=\"position:absolute; float: right;\"><i class=\"fa fa-ellipsis-v\" area-hidden=\"true\"></i></button><br/><br/><div class=\"collapse\" id=\"personaBarCollapse\" style=\"margin-right: 14px; float: right;\"><div class=\"row\" style=\"border: 1px solid #ccc;\"><ul style=\"padding: 0 !important; margin: 0 !important;\"><button onclick=\"reportMakePopUp();\">Report User</button></ul></div></div>";
	
	$innerHtml = "<div class=\"recent_heading\"><h4>";
	if($is_mentor == 1) {
		if(strcmp($c_id, "default") != 0) {
			$call = "CALL GetConversationUsers('$c_id');";
			$q = $conn->query($call);
			while($res = $q->fetch_assoc()) {
				if($res['UserID'] != $user_id) {
					$mentee_id = $res['UserID'];
				}
			}
			mysqli_free_result($res);
			mysqli_next_result($conn);
			
			$call = "CALL GetUserFullName('$mentee_id');";
			$q = $conn->query($call);
			$res = $q->fetch_assoc();
			$i_name = $res['Name'];
			mysqli_free_result($res);
			mysqli_next_result($conn);
			
			$call = "CALL GetConversationStatus('$c_id');";
			$q = $conn->query($call);
			$res = $q->fetch_assoc();
			$statusRet = $res['status'];
			mysqli_free_result($res);
			mysqli_next_result($conn);
			
			$innerHtml .= $i_name;
			$innerHtml .= "</h4></div><div class=\"srch_bar\">";
			
			if($statusRet == 2) {
				$innerHtml .= "<span><button type=\"submit\" onclick=\"acceptMakePopUp();\" style=\"margin-right: 3px;\">Accept</button><button type=\"submit\" onclick=\"pendingMakePopUp();\" style=\"margin-right: 3px;\">Pending</button><button type=\"submit\" onclick=\"denyMakePopUp();\" style=\"margin-right: 3px;\">Deny</button></span>";
			}
			else if($statusRet == 1) {
				$innerHtml .= "<span><button type=\"submit\" onclick=\"acceptMakePopUp();\" style=\"margin-right: 3px;\">Accept</button><button type=\"submit\" onclick=\"denyMakePopUp();\" style=\"margin-right: 3px;\">Deny</button></span>";
			}
			else if($statusRet == 0) {
				$innerHtml .= "<span><button type=\"submit\" onclick=\"endPartnership();\" style=\"margin-right: 3px;\">End Mentorship</button></span>";
			}
			$innerHtml .= "</div>";
			$innerHtml .= $reportBtn;
			$innerHtml .= "<div id=\"PopUp\"></div>";
		}
		else {
			$innerHtml .= "You don't have any Mentees.</h4></div><div class=\"srch_bar\"></div><div id=\"PopUp\"></div>";
		}
	}
	else {
		if(strcmp($c_id, "default") != 0) {
			$call = "CALL GetConversationUsers('$c_id');";
			$q = $conn->query($call);
			while($res = $q->fetch_assoc()) {
				if($res['UserID'] != $user_id) {
					$mentor_id = $res['UserID'];
				}
			}
			mysqli_free_result($res);
			mysqli_next_result($conn);
			
			$call = "CALL GetUserFullName('$mentor_id');";
			$q = $conn->query($call);
			$res = $q->fetch_assoc();
			$i_name = $res['Name'];
			mysqli_free_result($res);
			mysqli_next_result($conn);
			
			$call = "CALL GetConversationStatus('$c_id');";
			$q = $conn->query($call);
			$res = $q->fetch_assoc();
			$statusRet = $res['status'];
			mysqli_free_result($res);
			mysqli_next_result($conn);
			
			$innerHtml .= "<button type=\"submit\" onclick=\"goToProfile();\">$i_name</button></h4></div><div class=\"srch_bar\">";
			if($statusRet == 2) {
				$innerHtml .= "<span style=\"margin-right: 3px;\">Pending Mentor Approval</span>";
			}
			else if($statusRet == 1) {
				$innerHtml .= "<span style=\"margin-right: 3px;\">Pending Mentor Availability</span>";
			}
			else if($statusRet == 0) {
				$innerHtml .= "<span><button type=\"submit\" onclick=\"endPartnership();\" style=\"margin-right: 3px;\">End Mentorship</button></span>";
			}
			$innerHtml .= "</div>";
			$innerHtml .= $reportBtn;
			$innerHtml .= "<div id=\"PopUp\"></div>";
		}
		else {
			$innerHtml .= "You don't have any Mentors.</h4></div><div class=\"srch_bar\"></div><div id=\"PopUp\"></div>";
		}
	}
	echo $innerHtml;
?>