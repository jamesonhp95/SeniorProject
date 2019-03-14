<?php
  session_start();
  require_once("../internalIncludes/dbconfig.php");
  include 'includes/header.php';
  include 'includes/site-header-loader.php';
  include 'includes/pageOverlay.php';
  include 'includes/messageStyle.php';
		
  //ensures user must be logged in
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
    
    if($IsMentor != 1 && $IsMentor != 0)
    {
    echo "<script> location.href='Login.php'; </script>";
    exit;
    }
  }
  else
  {
    echo "<script> location.href='LandingPage.php'; </script>";
    exit;
  }
  
  if(isset($_POST['selected_conversation'])) {
	  $isSearchForMentor = $_POST['selected_conversation'];
	  if(strcmp($isSearchForMentor, "default") == 0) {
		  echo "<script>location.href='Search.php';</script>";
		  exit;
	  }
  }

  $user_id = $_SESSION['user_id'];
  $is_mentor = $_SESSION['IsMentor'];
  $c_ids = array();
  $call = "CALL GetUsersConversations('$user_id');";
  $q = $conn->query($call);
  while($res = $q->fetch_assoc()) {
	$c_ids[] = $res['ConversationID'];
  }
  mysqli_free_result($q);
  mysqli_next_result($conn);
  
  $chat_ids = array();
  $chat_names = array();
  $chat_locked = array();
  foreach($c_ids as $id) {
	  $call = "CALL GetConversationUsers('$id');";
	  $q = $conn->query($call);
	  while($res = $q->fetch_assoc()) {
		  if($res['UserID'] != $user_id) {
			  $chat_ids[] = $res['UserID'];
			  }
		}
		mysqli_free_result($q);
		mysqli_next_result($conn);
	}
	foreach($chat_ids as $person) {
		$call = "CALL GetUserFullName('$person');";
		$q = $conn->query($call);
		while($res = $q->fetch_assoc()) {
			$chat_names[] = $res['Name'];
		}
		mysqli_free_result($q);
		mysqli_next_result($conn);
	}
  foreach($c_ids as $id) {
	  $call = "CALL GetConversationStatus('$id');";
	  $q = $conn->query($call);
	  $res = $q->fetch_assoc();
	  $chat_locked[] = $res['status'];
	  mysqli_free_result($q);
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
	  mysqli_free_result($q);
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
  
  $msg_contents = array();
  $msg_times = array();
  $msg_dates = array();
  $msg_owner = array();
  $msg_date = "";

  if(count($conversationInformation[0])>0) {
	  $first_id = $conversationInformation[1][0];
	  $call = "CALL GetConversation('$first_id');";
	  $q = $conn->query($call);
	  while($res = $q->fetch_assoc()) {
		  $msg_owner[] = $res['OwnerID'];
		  $temporaryMsg = $res['Content'];
		  $msg_contents[] = $res['Content'];
		  $msg_date = $res['ModifiedDateTime'];
		  $msg_date = strtotime($msg_date);
		  $msg_dates[] = date("M jS", $msg_date);
		  $msg_times[] = date("g:ia", $msg_date);
	  }
	  mysqli_free_result($q);
	  mysqli_next_result($conn);
	  //$selected_conversation = $c_ids[0];
	  
  }
  
  if(count($conversationInformation[0]) == 0) {
	  $today = getdate();
	  $conversationInformation[0][0] = date("M jS", $today);
	  $conversationInformation[1][0] = "default";
	  $conversationInformation[2][0] = "0";
	  $conversationInformation[3][0] = "Start a Conversation!";
	  $conversationInformation[4][0] = -1;
	  $conversationInformation[5][0] = "Go to search for a mentor!";
	  
	  if($is_mentor == 0) {
		  $conversationInformation[3][0] = "Start a Conversation!";
		  $conversationInformation[5][0] = "Go to search for a mentor!";
	  }
	  else {
		  $conversationInformation[3][0] = "Wait for a Mentee Request";
		  $conversationInformation[5][0] = "Wait for a Mentee Request";
	  }
  }
  
  
?>
<script>
	var intervalId;
	var convoSelectedChoice;
    if(window.history.replaceState) {
	    window.history.replaceState(null, null, window.location.href);
    };
	function messageInterval() {
		intervalId = setInterval(messageIntegrity, 5000);
	};
	
	function conversationIntegrity() {
		var prev = document.getElementById("prevId").value;
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				document.getElementById("convo_area").innerHTML = this.responseText;
				if(prev != "default") {
					document.getElementById(prev).style['background'] = "#c6c6c6";
				}
			}
		};
		xmlhttp.open("GET", "populateConversations.php?q="+prev, true);
		xmlhttp.send();
	};
	
	function messageIntegrity() {
		var id = document.getElementById("prevId").value;
		var selectedMentorId = document.getElementById("curMentorId").value;
		if(id != "default") {
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
				if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					var isBottom = "false";
					var area = document.getElementById("msg_area");
					if(area.scrollTop == (area.scrollHeight - area.offsetHeight)) {
						isBottom = "true";
					}						
					document.getElementById("msg_area").innerHTML = this.responseText;
					conversationIntegrity();
					if(isBottom == "true") {
						area.scrollTop = area.scrollHeight;
					}
				}
			};
			xmlhttp.open("GET", "populateMessages.php?cid="+id+"&menid="+selectedMentorId, true);
			xmlhttp.send();
		}
		else {
			conversationIntegrity();
		}
	};
	
	function populateMessages(id, selectedMentorId) {
		if(id != "default") {
			var prev = document.getElementById("prevId").value;
			document.getElementById("curMentorId").value = selectedMentorId;
			if(prev != "default") {
				document.getElementById(prev).style['background'] = "#fff";
				document.getElementById(prev).style['background-color'] = "buttonface";
			}
			document.getElementById("prevId").value = id;
			document.getElementById(id).style['background'] = "#c6c6c6";
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
				if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					document.getElementById("msg_area").innerHTML = this.responseText;
					typedMessageIntegrity(id, selectedMentorId);
					headerIntegrity(id);
					var area = document.getElementById("msg_area");
					area.scrollTop = area.scrollHeight;
				}
			};
			xmlhttp.open("GET", "populateMessages.php?cid="+id+"&menid="+selectedMentorId, true);
			xmlhttp.send();
		}
		else if(id == "default") {
			location.href='Search.php';
		}
	};
	
	function typedMessageIntegrity(id, mentorId) {
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				document.getElementById("type_msg_area").innerHTML = this.responseText;
			}
		};
		xmlhttp.open("GET", "populateTypedMessage.php?cid="+id+"&menid="+mentorId, true);
		xmlhttp.send();
	};
	
	function headerIntegrity(id) {
		var curId = document.getElementById("prevId").value;
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				document.getElementById("head_area").innerHTML = this.responseText;
			}
		};
		xmlhttp.open("GET", "populateHeadArea.php?cid="+id, true);
		xmlhttp.send();
	};
	
	function sendMessage() {
		var prevStr = document.getElementById("typed_message").value;
		var str = prevStr.replace(/&/g, "%26amp;").replace(/>/g, "%26gt;").replace(/</g, "%26lt;").replace(/\+/g, "%2B").replace(/#/gi, "%23");
		prevStr = str;
		str = prevStr.replace(/\n/g, '<br/>');
		if(str != "") {
			document.getElementById("typed_message").value = "";
			var curId = document.getElementById("prevId").value;
			var current = document.getElementById("msg_area").innerHTML;
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
				if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					document.getElementById("msg_area").innerHTML = current + this.responseText;
					document.getElementById("typed_message").style.height = "36px";
					var area = document.getElementById("msg_area");
					area.scrollTop = area.scrollHeight;
				}
			};
			xmlhttp.open("GET", "sendMessage.php?msg="+str+"&id="+curId, true);
			xmlhttp.send();
		}
	};
	
	function grow() {
		document.getElementById("typed_message").style.height = document.getElementById("typed_message").scrollHeight +"px";
	};
	
	function goToProfile() {
		var viewID = document.getElementById("curMentorId").value;
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				document.getElementById("curMentorId").innerHTML = this.responseText;
				location.href = 'ViewProfile.php';
			}
		};
		xmlhttp.open("GET", "setVariable.php?id="+viewID, true);
		xmlhttp.send();
	};
	
	function acceptMakePopUp() {
	  convoSelectedChoice = 0;
	  var textPrompt = "Of course, I definitely think I can give you some insight into my field!";
      var xmlhttp = new XMLHttpRequest();
      xmlhttp.onreadystatechange = function() {
        if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
          document.getElementById("PopUp").innerHTML = this.responseText;
		}
      };
      xmlhttp.open("GET", "popupMaker.php?q="+textPrompt+"&o="+convoSelectedChoice, true);
      xmlhttp.send();
    };
	
	function pendingMakePopUp() {
	  convoSelectedChoice = 1;
	  var textPrompt = "I would definitely like to talk with you more, unfortunately I have many other students to assist. I will message you again when I have an opening!";
      var xmlhttp = new XMLHttpRequest();
      xmlhttp.onreadystatechange = function() {
        if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
          document.getElementById("PopUp").innerHTML = this.responseText;
		}
      };
      xmlhttp.open("GET", "popupMaker.php?q="+textPrompt+"&o="+convoSelectedChoice, true);
      xmlhttp.send();
    };
	
	function denyMakePopUp() {
	  convoSelectedChoice = 2;
	  var textPrompt = "I don't quite think I am a good fit for the information you are looking for. Sorry.";
      var xmlhttp = new XMLHttpRequest();
      xmlhttp.onreadystatechange = function() {
        if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
          document.getElementById("PopUp").innerHTML = this.responseText;
		}
      };
      xmlhttp.open("GET", "popupMaker.php?q="+textPrompt+"&o="+convoSelectedChoice, true);
      xmlhttp.send();
    };
	
	function reportMakePopUp() {
		convoSelectedChoice = 3;
		var textPrompt = "Please explain the nature of your report.";
		var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("PopUp").innerHTML = this.responseText;
		    }
        };
      xmlhttp.open("GET", "popupMaker.php?q="+textPrompt+"&o="+convoSelectedChoice, true);
      xmlhttp.send();
	};
	
	function sendPopUpMessage() {
	  var prevStr = document.getElementById("temp_msg").value;
		var str = prevStr.replace(/&/g, "%26amp;").replace(/>/g, "%26gt;").replace(/</g, "%26lt;").replace(/\+/g, "%2B").replace(/#/gi, "%23");
		prevStr = str;
		var str = prevStr.replace(/\n/g, '<br/>');
	  if(str != "") {
		  var curId = document.getElementById("prevId").value;
		  var curMentorNum = document.getElementById("curMentorId").value;
		  var currentHtml = document.getElementById("msg_area").innerHTML;
		  var xmlhttp = new XMLHttpRequest();
		  xmlhttp.onreadystatechange = function() {
			  if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				if(convoSelectedChoice != 3) {
					document.getElementById("msg_area").innerHTML = currentHtml + this.responseText;
				}
				headerIntegrity(curId);
				typedMessageIntegrity(curId, curMentorNum);
			  }
		  };
		  xmlhttp.open("GET", "mentorSendPopUp.php?q=" +str+"&c="+curId+"&o="+convoSelectedChoice+"&m="+curMentorNum, true);
		  xmlhttp.send();
	  }
	  else {
		document.getElementById("PopUp").innerHTML = "";
	  }
    };
	
	function cancelMessage() {
		document.getElementById("PopUp").innerHTML = "";
		//messageInterval();
	};
	
	function endPartnership() {
		var curId = document.getElementById("curMentorId").value;
		var curC_id = document.getElementById("prevId").value;
		var sendData = "cid=";
		sendData += curC_id;
		sendData += "&otherId=";
		sendData += curId;
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				document.getElementById("typed_message").value = this.responseText;
				headerIntegrity(curC_id);
				typedMessageIntegrity(curC_id, curId);
			}
		};
		xmlhttp.open("POST", "endPartnership.php", true);
		xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		xmlhttp.send(sendData);
	};
		
</script>
<html>
<body onload="messageInterval();">
<input type="hidden" id="prevId" value="<?php if(count($conversationInformation[0])>0) { echo $conversationInformation[1][0]; }?>">
<input type="hidden" id="curMentorId" value="<?php if(count($conversationInformation[0])>0) { echo $conversationInformation[2][0]; }?>">
<div id='content'>
  <div class="messaging">
    <div class="inbox_msg">
      <div class="inbox_people">
        <div class="heading_srch">
          <div class="recent_heading">
	  <h4>Conversations</h4>
          </div>
	  </div>
	  <div id="convo_area">
	  <?php
	    if($is_mentor == 1) {
			if($conversationInformation[4][0] != -1) {
				for($cnt = 0; $cnt < count($conversationInformation[0]); $cnt++) {
					$i_chat_date = $conversationInformation[0][$cnt];
					$id = $conversationInformation[1][$cnt];
					$i_chat_id = $conversationInformation[2][$cnt];
					$i_chat_name = $conversationInformation[3][$cnt];
					$i_chat_isLocked = $conversationInformation[4][$cnt];
					$i_chat_prev = $conversationInformation[5][$cnt];
					$val_id = "selected_id";
					$val_id .= $i_chat_id;
					if(strlen($i_chat_prev) > 26) {
						$i_chat_prev = substr($i_chat_prev, 0, 26)."...";
					}
					if($cnt == 0) {
						echo "<button type=\"submit\" id=\"$id\" value=\"$id\" style=\"background: #c6c6c6;\" onclick=\"populateMessages(this.value, $val_id.value);\"><input type=\"hidden\" id=\"$val_id\" value=\"$i_chat_id\"></input><div class=\"chat_list\"><div class=\"chat_people\"><div class=\"chat_img\"><img src=\"Images/M-Icon.png\"></div><div class=\"chat_ib\"><h5>$i_chat_name<span class=\"chat_date\">$i_chat_date</span></h5><p>$i_chat_prev</p></div></div></div></button>";
					}
					else {
						echo "<button type=\"submit\" id=\"$id\" value=\"$id\" onclick=\"populateMessages(this.value, $val_id.value);\"><input type=\"hidden\" id=\"$val_id\" value=\"$i_chat_id\"></input><div class=\"chat_list\"><div class=\"chat_people\"><div class=\"chat_img\"><img src=\"Images/M-Icon.png\"></div><div class=\"chat_ib\"><h5>$i_chat_name<span class=\"chat_date\">$i_chat_date</span></h5><p>$i_chat_prev</p></div></div></div></button>";
					}
				}
			}
		}
		else {
			for($cnt = 0; $cnt < count($conversationInformation[0]); $cnt++) {
				$i_chat_date = $conversationInformation[0][$cnt];
				$id = $conversationInformation[1][$cnt];
				$i_chat_id = $conversationInformation[2][$cnt];
				$i_chat_name = $conversationInformation[3][$cnt];
				$i_chat_isLocked = $conversationInformation[4][$cnt];
				$i_chat_prev = $conversationInformation[5][$cnt];
				$val_id = "selected_id";
				$val_id .= $i_chat_id;
				if(strlen($i_chat_prev) > 26) {
					$i_chat_prev = substr($i_chat_prev, 0, 26)."...";
				}
				if($cnt == 0) {
					echo "<button type=\"submit\" id=\"$id\" value=\"$id\" style=\"background: #c6c6c6;\" onclick=\"populateMessages(this.value, $val_id.value);\"><input type=\"hidden\" id=\"$val_id\" value=\"$i_chat_id\"></input><div class=\"chat_list\"><div class=\"chat_people\"><div class=\"chat_img\"><img src=\"Images/M-Icon.png\"></div><div class=\"chat_ib\"><h5>$i_chat_name<span class=\"chat_date\">$i_chat_date</span></h5><p>$i_chat_prev</p></div></div></div></button>";
				}
				else {
					echo "<button type=\"submit\" id=\"$id\" value=\"$id\" onclick=\"populateMessages(this.value, $val_id.value);\"><input type=\"hidden\" id=\"$val_id\" value=\"$i_chat_id\"></input><div class=\"chat_list\"><div class=\"chat_people\"><div class=\"chat_img\"><img src=\"Images/M-Icon.png\"></div><div class=\"chat_ib\"><h5>$i_chat_name<span class=\"chat_date\">$i_chat_date</span></h5><p>$i_chat_prev</p></div></div></div></button>";
				}
			}
		}
	   ?>
	   </div>
          </div>
        </div>

    <div class="mesgs">
	    <div class="msg_history">
			<div class="heading_srch" id="head_area">
				<div class="recent_heading">
					<h4>
						<?php
							if($is_mentor == 1) {
								if(strcmp($conversationInformation[1][0], "default") != 0) { 
									$i_name = $conversationInformation[3][0];
									echo $i_name; 
								}
								else { 
									echo "You don't have any Mentees."; 
								}
							}
							else {
								if(strcmp($conversationInformation[1][0], "default") != 0) { 
									$i_name = $conversationInformation[3][0];
									echo "<button type=\"submit\" onclick=\"goToProfile();\">$i_name</button>"; 
								} 
								else { 
									echo "You don't have any Mentors."; 
								}
							}
						?>
					</h4>
				</div>
				<div class="srch_bar">
					<?php
					  if(count($conversationInformation[0])>0) {
						if($is_mentor == 1) {
							if($conversationInformation[4][0] == 2) {
								echo "<span><button type=\"submit\" onclick=\"acceptMakePopUp();\" style=\"margin-right: 3px;\">Accept</button><button type=\"submit\" onclick=\"pendingMakePopUp();\" style=\"margin-right: 3px;\">Pending</button><button type=\"submit\" onclick=\"denyMakePopUp();\" style=\"margin-right: 3px;\">Deny</button></span>";
							}
							if($conversationInformation[4][0] == 1) {
								echo "<span><button type=\"submit\" onclick=\"acceptMakePopUp();\" style=\"margin-right: 3px;\">Accept</button><button type=\"submit\" onclick=\"denyMakePopUp();\" style=\"margin-right: 3px;\">Deny</button></span>";
							}
							if($conversationInformation[4][0] == 0) {
								echo "<span><button type=\"submit\" onclick=\"endPartnership();\" style=\"margin-right: 3px;\">End Mentorship</button></span>";
							}
						}
						else {
							if($conversationInformation[4][0] == 2) {
								echo "<span style=\"margin-right: 3px;\">Pending Mentor Approval</span>";
							}
							if($conversationInformation[4][0] == 1) {
								echo "<span style=\"margin-right: 3px;\">Pending Mentor Availability</span>";
							}
							if($conversationInformation[4][0] == 0) {
								echo "<span><button type=\"submit\" onclick=\"endPartnership();\" style=\"margin-right: 3px;\">End Mentorship</button></span>";
							}
						}
					  }
					  if(strcmp($conversationInformation[1][0], "default") != 0) {
						  echo "<button id=\"dropDown\" type=\"button\" data-toggle=\"collapse\" data-target=\"#personaBarCollapse\" aria-expanded=\"false\" aria-controls=\"personaBarCollapse\" style=\"position:absolute; float: right;\"><i class=\"fa fa-ellipsis-v\" area-hidden=\"true\"></i></button><br/><div class=\"collapse\" id=\"personaBarCollapse\" style=\"margin-right: 14px; float: right;\"><div class=\"row\" style=\"border: 1px solid #ccc;\"><ul style=\"padding: 0 !important; margin: 0 !important;\"><button onclick=\"reportMakePopUp();\">Report User</button></ul></div></div>";
					  }
					?>
					
					<!--<button id="dropDown" type="button" data-toggle="collapse" data-target="#personaBarCollapse" aria-expanded="false" aria-controls="personaBarCollapse" style="float: right;"><i class="fa fa-ellipsis-v" area-hidden="true"></i></button>
					<br/>
					<div class="collapse" id="personaBarCollapse" style="margin-right: 14px; float: right;">
						<div class="row" style="border: 1px solid #ccc;">
							<ul style="padding: 0 !important; margin: 0 !important;">
								<button onclick="reportMakePopUp();">Report User</button>
							</ul>
						</div>
					</div>-->
				</div>
				<div id="PopUp"></div>
			</div>
			<div class="msg_area" id="msg_area">
				<?php
				  $cnt = 0;
				  foreach($msg_contents as $i_msg) {
					$i_msg_time = $msg_times[$cnt];
					$i_msg_date = $msg_dates[$cnt];
					$i_owner_id = $msg_owner[$cnt];
					$cnt++;
					if($i_owner_id != $user_id) {
						echo "<div class=\"incoming_msg\"><div class=\"incoming_msg_img\"><img src=\"Images/M-Icon.png\"> </div><div class=\"received_msg\"><div class=\"received_withd_msg\"><p>$i_msg</p><span class=\"time_date\">$i_msg_time  |  $i_msg_date</span></div></div></div>";
					}
					else {
						echo "<div class=\"outgoing_msg\"><div class=\"sent_msg\"><p>$i_msg</p><span class=\"time_date\">$i_msg_time  |  $i_msg_date</span></div></div>";
					}
				  }
				  echo '<script> document.getElementById("msg_area").scrollTop = document.getElementById("msg_area").scrollHeight; </script>';
				?>
			</div>
		</div>
		<div class="type_msg" id="type_msg_area">
			<div class="input_msg_write">
			<?php		
				$tempPlaceholder = "";
				$sendButtonLocked = "";
				$textareaLocked = "";
				if(strcmp($conversationInformation[1][0], "default") != 0) {
					if($conversationInformation[4][0] == 3) {
						$tempPlaceholder = "Chat has been denied by the Mentor.";
						$sendButtonLocked = "disabled";
						$textareaLocked = "disabled";
					}
					else if($conversationInformation[4][0] == 2) {
						$tempPlaceholder = "Chat is currently locked until the Mentor accepts.";
						$sendButtonLocked = "disabled";
						$textareaLocked = "disabled";
					}
					else if($conversationInformation[4][0] == 1) {
						$tempPlaceholder = "Chat is currently pending until the Mentor has more room for new Mentees.";
						$sendButtonLocked = "disabled";
						$textareaLocked = "disabled";
					}
					else {
						$tempPlaceholder = "Type a message...";
						$sendButtonLocked = "onclick=\"sendMessage();\"";
						$textareaLocked = "";
					}
				}
				else {
					$tempPlaceholder = "You don't have any conversations.";
					$sendButtonLocked = "disabled";
					$textareaLocked = "disabled";
				}
			?>
			  <textarea rows="1" id="typed_message" class="type_msg_textarea" name="typed_message" placeholder="<?php echo $tempPlaceholder; ?>" onkeydown="grow();" <?php echo $textareaLocked; ?> ></textarea>
			  <button id="send_msg_btn" class="msg_send_btn" type="submit" <?php echo $sendButtonLocked; ?> ><i class="fa fa-paper-plane-o" aria-hidden="true"></i></button>
			</div>
		</div>
    </div>
  </div>
</div>
</body>
</html>
<?php
  include 'includes/footer.php';
?>
