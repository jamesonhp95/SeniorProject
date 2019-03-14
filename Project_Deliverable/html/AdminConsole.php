<?php
  session_start();
  require_once("../internalIncludes/dbconfig.php");
  include 'includes/header.php';
  include 'includes/site-header-loader.php';
  include 'includes/pageOverlay.php';
  include 'includes/adminStyle.php'; 
  include 'includes/messageStyle.php';
  
  if(isset($_SESSION['user_id']))
  {
    $user_id = $_SESSION['user_id'];
    $sql = "CALL GetUser('$user_id')";
    $q = $conn->query($sql);
    $r = $q->fetch_assoc();
    $IsEnabled = $r['IsEnabled'];
    $IsMentor = $r['IsMentor'];
    $FullName = $r['Name'];
    mysqli_free_result($q);
    mysqli_next_result($conn);
    $_SESSION['IsEnabled'] = $IsEnabled;
    $_SESSION['IsMentor'] = $IsMentor;
    $_SESSION['full_name'] = $FullName;
    
    if($IsEnabled != 1)
    {
      echo "<script> location.href='AccountDisabled.php'; </script>";
      exit;
    }
    
    if($IsMentor != 2)
    {
      echo "<script> location.href='MentorLogin.php'; </script>";
      exit;
    }
  }
  else
  {
      echo "<script> location.href='LandingPage.php'; </script>";
      exit;
  }
  
    $usernames = array();
	$id_nums = array();
	$call = "CALL GetPendingMentors();";
	$q = $conn->query($call);
	while($r = $q->fetch_assoc()) {
		$pending_usernames[] = $r['UserName'];
		$pending_id_nums[] = $r['UserID'];
	}
	mysqli_free_result($q);
	mysqli_next_result($conn);
	
	$reported_id_nums = array();
	$report_entry_id = array();
	$call = "CALL GetReportsNew();";
	$q = $conn->query($call);
	while($r = $q->fetch_assoc()) {
		$reported_id_nums[] = $r['ReportedUserID'];
		$report_entry_id[] = $r['EntryID'];
	}
	mysqli_free_result($q);
	mysqli_next_result($conn);
	
	$banned_id_nums = array();
	$banned_usernames = array();
	$call = "CALL GetBannedUsers();";
	$q = $conn->query($call);
	while($r = $q->fetch_assoc()) {
		$banned_id_nums[] = $r['UserID'];
		$banned_usernames[] = $r['UserName'];
	}
	mysqli_free_result($q);
	mysqli_next_result($conn);
	
	$cnt = 0;
	$pendingMentorsInnerHtml = "";
	foreach($pending_id_nums as $i_id) {
		$userEmail = $pending_usernames[$cnt];
		$tag_id = $i_id;
		$tag_id .= " ";
		$tag_id .= $userEmail;
		$pendingMentorsInnerHtml .= "<div class=\"row\" style=\"float: right; margin: 0 !important; padding: 0 !important; width: 95%; height: inherit; border: 1px solid #ccc;\"><ul style=\"margin: 0 !important; padding: 0 !important; width: 100%; height: inherit;\"><button style=\"padding: 0 !important; margin: 0 !important; width: 100%; height: inherit;\" value=\"$tag_id\" onclick=\"showSelectedMentorProfile(this.value, 0);\">";
		$pendingMentorsInnerHtml .= $userEmail;
		$pendingMentorsInnerHtml .= "</button></ul></div>";
		$cnt++;
	}
	
	$cnt = 0;
	$reportedUsersInnerHtml = "";
	foreach($reported_id_nums as $reported_id) {
		$call = "CALL GetUser('$reported_id');";
		$q = $conn->query($call);
		$r = $q->fetch_assoc();
		$reported_userName = $r['UserName'];
		mysqli_free_result($q);
		mysqli_next_result($conn);
		$report_tag_id = $reported_id;
		$report_tag_id .= " ";
		$report_tag_id .= $report_entry_id[$cnt];
		$report_tag_id .= " ";
		$report_tag_id .= $reported_userName;
		$reportedUsersInnerHtml .= "<div class=\"row\" style=\"float: right; margin: 0 !important; padding: 0 !important; width: 95%; height: inherit; border: 1px solid #ccc;\"><ul style=\"margin: 0 !important; padding: 0 !important; width: 100%; height: inherit;\"><button style=\"padding: 0 !important; margin: 0 !important; width: 100%; height: inherit;\" value=\"$report_tag_id\" onclick=\"showReportedUserCircumstances(this.value);\">";
		$reportedUsersInnerHtml .= $reported_userName;
		$reportedUsersInnerHtml .= "</button></ul></div>";
		$cnt++;
	}
	
	$cnt = 0;
	$bannedUsersInnerHtml = "";
	foreach($banned_id_nums as $banned_id) {
		$banned_username = $banned_usernames[$cnt];
		$tag_id = $banned_id;
		$tag_id .= " ";
		$tag_id .= $banned_username;
		$bannedUsersInnerHtml .= "<div class=\"row\" style=\"float: right; margin: 0 !important; padding: 0 !important; width: 95%; height: inherit; border: 1px solid #ccc;\"><ul style=\"margin: 0 !important; padding: 0 !important; width: 100%; height: inherit;\"><button style=\"padding: 0 !important; margin: 0 !important; width: 100%; height: inherit;\" value=\"$tag_id\" onclick=\"showSelectedMentorProfile(this.value, 2);\">";
		$bannedUsersInnerHtml .= $banned_username;
		$bannedUsersInnerHtml .= "</button></ul></div>";
		$cnt++;
	}
	
	
	if(count($pending_id_nums) == 0) {
		$pendingMentorsInnerHtml = "<div class=\"row\" style=\"float: right; margin: 0 !important; padding: 0 !important; width: 95%; height: inherit; border: 1px solid #ccc;\"><ul style=\"margin: 0 !important; padding: 0 !important; width: 100%; height: inherit;\"><button style=\"padding: 0 !important; margin: 0 !important; width: 100%; height: inherit;\" value=\"default\" disabled>No Pending Mentors</button></ul></div>";
	}
	if(count($reported_id_nums) == 0) {
		$reportedUsersInnerHtml = "<div class=\"row\" style=\"float: right; margin: 0 !important; padding: 0 !important; width: 95%; height: inherit; border: 1px solid #ccc;\"><ul style=\"margin: 0 !important; padding: 0 !important; width: 100%; height: inherit;\"><button style=\"padding: 0 !important; margin: 0 !important; width: 100%; height: inherit;\" value=\"default\" disabled>No Reported Users</button></ul></div>";
	}
	if(count($banned_id_nums) == 0) {
		$bannedUsersInnerHtml = "<div class=\"row\" style=\"float: right; margin: 0 !important; padding: 0 !important; width: 95%; height: inherit; border: 1px solid #ccc;\"><ul style=\"margin: 0 !important; padding: 0 !important; width: 100%; height: inherit;\"><button style=\"padding: 0 !important; margin: 0 !important; width: 100%; height: inherit;\" value=\"default\" disabled>No Banned Users</button></ul></div>";
	}
?>

<html>
<head>
  <title>Admin Login</title>
  <script>
    function showReportedUserCircumstances(str) {
		var xmlhttp = new XMLHttpRequest();
		var arr = str.split(" ");
		var id = arr[0];
		var entryId = arr[1];
		var userName = arr[2];
		document.getElementById("fullId").value = str;
		xmlhttp.onreadystatechange = function() {
			if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				document.getElementById("cmd_area").innerHTML = this.responseText;
			}
		};
		xmlhttp.open("GET", "showReportedUserCircumstances.php?eId="+entryId, true);
		xmlhttp.send();
	};
	
	function banSelectedUser() {
		var xmlhttp = new XMLHttpRequest();
		var fId = document.getElementById("fullId").value;
		var arr = fId.split(" ");
		var id = arr[0];
		var entryId = arr[1];
		var userName = arr[2];
		var cur = document.getElementById("reportedUsers").innerHTML;
		var removeStr = "<div class=\"row\" style=\"float: right; margin: 0 !important; padding: 0 !important; width: 95%; height: inherit; border: 1px solid #ccc;\"><ul style=\"margin: 0 !important; padding: 0 !important; width: 100%; height: inherit;\"><button style=\"padding: 0 !important; margin: 0 !important; width: 100%; height: inherit;\" value=\"";
		removeStr += fId;
		removeStr += "\" onclick=\"showReportedUserCircumstances(this.value);\">";
		removeStr += userName;
		removeStr += "</button></ul></div>";
		
		var newInner = "";
		xmlhttp.onreadystatechange = function() {
			if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				newInner = cur.replace(removeStr, "");
				var len = newInner.length;
				if(len == 6) {
					newInner = "<div class=\"row\" style=\"float: right; margin: 0 !important; padding: 0 !important; width: 95%; height: inherit; border: 1px solid #ccc;\"><ul style=\"margin: 0 !important; padding: 0 !important; width: 100%; height: inherit;\"><button style=\"padding: 0 !important; margin: 0 !important; width: 100%; height: inherit;\" value=\"default\" disabled>No Reported Users</button></ul></div>";
				}
				document.getElementById("reportedUsers").innerHTML = newInner;
				document.getElementById("cmd_area").innerHTML = "";
				document.getElementById("fullId").value = this.responseText;
			}
		};
		xmlhttp.open("GET", "banSelectedUser.php?id="+id+"&eId="+entryId, true);
		xmlhttp.send();
	};
	
	function unbanUser() {
		var xmlhttp = new XMLHttpRequest();
		var fId = document.getElementById("fullId").value;
		var arr = fId.split(" ");
		var id = arr[0];
		var mName = arr[1];
		var cur = document.getElementById("bannedUsers").innerHTML;
		var removeStr = "<div class=\"row\" style=\"float: right; margin: 0 !important; padding: 0 !important; width: 95%; height: inherit; border: 1px solid #ccc;\"><ul style=\"margin: 0 !important; padding: 0 !important; width: 100%; height: inherit;\"><button style=\"padding: 0 !important; margin: 0 !important; width: 100%; height: inherit;\" value=\"";
		removeStr += fId;
		removeStr += "\" onclick=\"showSelectedMentorProfile(this.value, 2);\">";
		removeStr += mName;
		removeStr += "</button></ul></div>";
		
		var newInner = "";
		xmlhttp.onreadystatechange = function() {
			if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				newInner = cur.replace(removeStr, "");
				var len = newInner.length;
				if(len == 6) {
					newInner = "<div class=\"row\" style=\"float: right; margin: 0 !important; padding: 0 !important; width: 95%; height: inherit; border: 1px solid #ccc;\"><ul style=\"margin: 0 !important; padding: 0 !important; width: 100%; height: inherit;\"><button style=\"padding: 0 !important; margin: 0 !important; width: 100%; height: inherit;\" value=\"default\" disabled>No Banned Users</button></ul></div>";
				}
				document.getElementById("bannedUsers").innerHTML = newInner;
				document.getElementById("cmd_area").innerHTML = "";
				document.getElementById("mentorId").value = this.responseText;
			}
		};
		xmlhttp.open("GET", "unbanUser.php?id="+id, true);
		xmlhttp.send();
	};
	
	function dismissSelectedUser() {
		var xmlhttp = new XMLHttpRequest();
		var fId = document.getElementById("fullId").value;
		var arr = fId.split(" ");
		var entryId = arr[1];
		var userName = arr[2];
		var cur = document.getElementById("reportedUsers").innerHTML;
		var removeStr = "<div class=\"row\" style=\"float: right; margin: 0 !important; padding: 0 !important; width: 95%; height: inherit; border: 1px solid #ccc;\"><ul style=\"margin: 0 !important; padding: 0 !important; width: 100%; height: inherit;\"><button style=\"padding: 0 !important; margin: 0 !important; width: 100%; height: inherit;\" value=\"";
		removeStr += fId;
		removeStr += "\" onclick=\"showReportedUserCircumstances(this.value);\">";
		removeStr += userName;
		removeStr += "</button></ul></div>";
		
		var newInner = "";
		xmlhttp.onreadystatechange = function() {
			if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				newInner = cur.replace(removeStr, "");
				var len = newInner.length;
				if(len == 6) {
					newInner = "<div class=\"row\" style=\"float: right; margin: 0 !important; padding: 0 !important; width: 95%; height: inherit; border: 1px solid #ccc;\"><ul style=\"margin: 0 !important; padding: 0 !important; width: 100%; height: inherit;\"><button style=\"padding: 0 !important; margin: 0 !important; width: 100%; height: inherit;\" value=\"default\" disabled>No Reported Users</button></ul></div>";
				}
				document.getElementById("reportedUsers").innerHTML = newInner;
				document.getElementById("cmd_area").innerHTML = "";
				document.getElementById("fullId").value = this.responseText;
			}
		};
		xmlhttp.open("GET", "dismissSelectedUser.php?eId="+entryId, true);
		xmlhttp.send();
	};
	
	function showSelectedMentorProfile(str, opt) {
		var xmlhttp = new XMLHttpRequest();
		var arr = str.split(" ");
		var id = arr[0];
		var name = arr[1];
		document.getElementById("fullId").value = str;
		document.getElementById("mentorId").value = id;
		document.getElementById("mentorName").value = name;
		
		xmlhttp.onreadystatechange = function() {
			if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				document.getElementById("cmd_area").innerHTML = this.responseText;
			}
		};
		xmlhttp.open("GET", "showSelectedMentorProfile.php?id="+id+"&o="+opt, true);
		xmlhttp.send();
	};
	
	function approveMentor() {
		var xmlhttp = new XMLHttpRequest();
		var fId = document.getElementById("fullId").value;
		var mId = document.getElementById("mentorId").value;
		var mName = document.getElementById("mentorName").value;
		var cur = document.getElementById("pendingMentors").innerHTML;
		var removeStr = "<div class=\"row\" style=\"float: right; margin: 0 !important; padding: 0 !important; width: 95%; height: inherit; border: 1px solid #ccc;\"><ul style=\"margin: 0 !important; padding: 0 !important; width: 100%; height: inherit;\"><button style=\"padding: 0 !important; margin: 0 !important; width: 100%; height: inherit;\" value=\"";
		removeStr += fId;
		removeStr += "\" onclick=\"showSelectedMentorProfile(this.value, 0);\">";
		removeStr += mName;
		removeStr += "</button></ul></div>";
		
		var newInner = "";
		xmlhttp.onreadystatechange = function() {
			if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				newInner = cur.replace(removeStr, "");
				var len = newInner.length;
				if(len == 6) {
					newInner = "<div class=\"row\" style=\"float: right; margin: 0 !important; padding: 0 !important; width: 95%; height: inherit; border: 1px solid #ccc;\"><ul style=\"margin: 0 !important; padding: 0 !important; width: 100%; height: inherit;\"><button style=\"padding: 0 !important; margin: 0 !important; width: 100%; height: inherit;\" value=\"default\" disabled>No Pending Mentors</button></ul></div>";
				}
				document.getElementById("pendingMentors").innerHTML = newInner;
				document.getElementById("cmd_area").innerHTML = "";
				document.getElementById("mentorId").value = this.responseText;
			}
		};
		xmlhttp.open("GET", "approveMentor.php?id="+mId, true);
		xmlhttp.send();
	};
	
	function denyMentor() {
		var xmlhttp = new XMLHttpRequest();
		var fId = document.getElementById("fullId").value;
		var mId = document.getElementById("mentorId").value;
		var mName = document.getElementById("mentorName").value;
		var cur = document.getElementById("pendingMentors").innerHTML;
		var removeStr = "<div class=\"row\" style=\"float: right; margin: 0 !important; padding: 0 !important; width: 95%; height: inherit; border: 1px solid #ccc;\"><ul style=\"margin: 0 !important; padding: 0 !important; width: 100%; height: inherit;\"><button style=\"padding: 0 !important; margin: 0 !important; width: 100%; height: inherit;\" value=\"";
		removeStr += fId;
		removeStr += "\" onclick=\"showSelectedMentorProfile(this.value, 0);\">";
		removeStr += mName;
		removeStr += "</button></ul></div>";
		
		var newInner = "";
		xmlhttp.onreadystatechange = function() {
			if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				newInner = cur.replace(removeStr, "");
				var len = newInner.length;
				if(len == 6) {
					newInner = "<div class=\"row\" style=\"float: right; margin: 0 !important; padding: 0 !important; width: 95%; height: inherit; border: 1px solid #ccc;\"><ul style=\"margin: 0 !important; padding: 0 !important; width: 100%; height: inherit;\"><button style=\"padding: 0 !important; margin: 0 !important; width: 100%; height: inherit;\" value=\"default\" disabled>No Pending Mentors</button></ul></div>";
				}
				document.getElementById("pendingMentors").innerHTML = newInner;
				document.getElementById("cmd_area").innerHTML = "";
				document.getElementById("mentorId").value = this.responseText;
			}
		};
		xmlhttp.open("GET", "denyMentor.php?id="+mId, true);
		xmlhttp.send();
	};
	
	function searchMentors(str) {
		if(str != "") {
		  document.getElementById("search_constraint").value = "";
		  var xmlhttp = new XMLHttpRequest();
		  xmlhttp.onreadystatechange = function() {
			  if(this.readyState == 4 && this.status == 200) {
				  document.getElementById("searchMentorsResults").innerHTML = this.responseText;
			  }
		  };
		  xmlhttp.open("GET", "adminSearchMentors.php?q="+str, true);
		  xmlhttp.send();
	  }
	};
  
  //Jordans Scripts (Seperate for readability)
  //
  //
  function redirectLogout() {
    window.location = 'Logout.php';
  }
  
  function showAdminEmailCredentialsPage() {
		var xmlhttp = new XMLHttpRequest();
		
		xmlhttp.onreadystatechange = function() {
			if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				document.getElementById("cmd_area").innerHTML = this.responseText;
			}
		};
		xmlhttp.open("GET", "showAdminEmailCredentialInput.php", true);
		xmlhttp.send();
	};
  
  function postAdminEmailCredentialsPage() {
		var xmlhttp = new XMLHttpRequest();
		var userStr = document.getElementById("username").value;
		var pass1Str = document.getElementById("password").value;
		var pass2Str = document.getElementById("password2").value;
		userStr = userStr.replace(/&/g, "%26amp;").replace(/>/g, "%26gt;").replace(/</g, "%26lt;").replace(/\+/g, "%2B").replace(/#/gi, "%23");
		pass1Str = pass1Str.replace(/&/g, "%26amp;").replace(/>/g, "%26gt;").replace(/</g, "%26lt;").replace(/\+/g, "%2B").replace(/#/gi, "%23");
		pass2Str = pass2Str.replace(/&/g, "%26amp;").replace(/>/g, "%26gt;").replace(/</g, "%26lt;").replace(/\+/g, "%2B").replace(/#/gi, "%23");
		var sendData = "username=";
		sendData += userStr;
		sendData += "&password=";
		sendData += pass1Str;
		sendData += "&password2=";
		sendData += pass2Str;
    
		xmlhttp.onreadystatechange = function() {
			if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				document.getElementById("cmd_area").innerHTML = this.responseText;
			}
		};
		xmlhttp.open("POST", "showAdminEmailCredentialInput.php", true);
    xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		xmlhttp.send(sendData);
	};
  
  function sendTestEmail()
  {
   var xmlhttp = new XMLHttpRequest();   
   xmlhttp.onreadystatechange = function() {
			if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        document.getElementById("message2").innerHTML = "Please check your email.";
			}
		};
		xmlhttp.open("GET", "sendAdminTestEmail.php", true);
    xmlhttp.send();
  };
  </script>
</head>
<body>
<input type="hidden" id="fullId" value=""></input><input type="hidden" id="mentorId" value=""></input><input type="hidden" id="mentorName" value=""></input>
  <div id='content' style="margin-top: 25px;">
	<div class="admin_commands">
		<button id="reviewPendingMentors" type="button" style="height: auto; padding: 10px 7px 10px 7px; width: 100%; font-size: 18.5px;" data-toggle="collapse" data-target="#pendingMentors" aria-expanded="false" aria-controls="pendingMentors" style="width: 100%;">
			Review Pending Mentors
		</button>
		<br/>
		<div class="collapse" id="pendingMentors" style="width: 100%; float: right;">
			<?php
				echo $pendingMentorsInnerHtml;
			?>
		</div>
		
		<button id="reviewReportedUsers" type="button" style="height: auto; padding: 10px 7px 10px 7px; width: 100%; font-size: 18.5px;" data-toggle="collapse" data-target="#reportedUsers" aria-expanded="false" aria-controls="reportedUsers" style="width: 100%;">
				Review Reported Users
		</button>
		<br/>
		<div class="collapse" id="reportedUsers" style="width: 100%; float: right;">
			<?php
				echo $reportedUsersInnerHtml;
			?>
		</div>
		
		<button id="reviewBannedUsers" type="button" style="height: auto; padding: 10px 7px 10px 7px; width: 100%; font-size: 18.5px;" data-toggle="collapse" data-target="#bannedUsers" aria-expanded="false" aria-controls="bannedUsers" style="width: 100%;">
			Review Banned Users
		</button>
		<br/>
		<div class="collapse" id="bannedUsers" style="width: 100%; float: right;">
			<?php
				echo $bannedUsersInnerHtml;
			?>
		</div>
		
		<button id="searchMentor" type="button" style="height: auto; padding: 10px 7px 10px 7px; width: 100%; font-size: 18.5px;" data-toggle="collapse" data-target="#mentorSearchBar" aria-expanded="false" aria-controls="mentorSearchBar" style="width: 100%;">
				Search for Mentor
		</button>
		<br/>
		<div class="collapse" id="mentorSearchBar" style="width: 100%; float: right;">
			<button type="submit" style="float:right;" onclick="searchMentors(search_constraint.value);">
				<i style="margin-bottom: 6px;" class="fa fa-search" aria-hidden="true"></i>
			</button>
			
		    <input type="text" id="search_constraint" name="search_constraint" style="width: 80%; float: right;" placeholder="Search for Mentor">
			</input>
			
			<div id="searchMentorsResults"></div>
			
		</div>
    
    <button id="changeAdminEmailCredentials" type="button" style="height: auto; padding: 10px 7px 10px 7px; width: 100%; font-size: 18.5px;" onclick="showAdminEmailCredentialsPage();"> <!--data-target="#emailCredentials" aria-expanded="false" aria-controls="emailCredentials" style="width: 100%;">-->
			Change Automated Email Credentials
		</button>
		<br/>
    
	</div>
	<div class="admin_view_command" id="cmd_area">
	</div>
  </div>
</body>
</html>
<?php
  include 'includes/footer.php';
?>