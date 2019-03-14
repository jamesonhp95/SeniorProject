<?php
	session_start();
	require_once("../internalIncludes/dbconfig.php");
	include 'includes/pageOverlay.php';
	include 'includes/header.php';
	include 'includes/site-header-loader.php';
	include 'includes/emailFunctions.php';
	
	if(isset($_SESSION['user_id'])) 
	{
		  echo "<script>location.href='Login.php';</script>";
		  exit;
	}
	if(isset($_POST['fName']) && isset($_POST['lName']) && isset($_POST['pwd']) && isset($_POST['pwdRepeat']) && isset($_POST['email']) && isset($_POST['maxMentee'])) {
		
		$fName = mysqli_real_escape_string($conn, $_POST['fName']);
		$lName = mysqli_real_escape_string($conn, $_POST['lName']);
		$pwd = $_POST['pwd'];
		$pwdRepeat = $_POST['pwdRepeat'];
		if(strcmp($pwd, $pwdRepeat) == 0) {
			$email = mysqli_real_escape_string($conn, $_POST['email']);
			$maxMentee = $_POST['maxMentee'];
			
			$call = "CALL CreateMentor('$fName', '$lName', '$email', '$maxMentee');";
			$q = $conn->query($call);
			$r = $q->fetch_assoc();
			$user_id = $r['UserID'];
			mysqli_free_result($q);
			mysqli_next_result($conn);
			
			if($user_id == 0) {
				$errMsg = "An account with that username already exists, please try logging in instead.";
			}
			else {
				$pwd_hash = password_hash($pwd, PASSWORD_DEFAULT);
				$call = "CALL ChangeUserPassword('$user_id', '$pwd_hash')";
				$q = $conn->query($call);
				$r = $q->fetch_assoc();
				$success = $r['Confirmation'];
				mysqli_free_result($q);
				mysqli_next_result($conn);
				if($success != 0) {
					$message = "An error occurred when creating your account. Please try again.";
				}
				else {
					if(isset($_POST['bio'])) {
						$bio = mysqli_real_escape_string($conn, $_POST['bio']);
						$call = "CALL SetUserBiography('$user_id', '$bio');";
						$q = $conn->query($call);
						mysqli_next_result($conn);
					}
					if(isset($_POST['com'])) {
						$com = mysqli_real_escape_string($conn, $_POST['com']);
						$call = "CALL SetUserPrimaryCommunication('$user_id','Phone/Text', '$com');";
						$q = $conn->query($call);
						mysqli_next_result($conn);
					}
					
					$x = 0;
					$expNames = array();
					$expDescs = array();
					$skillNames = array();
					$skillDescs = array();
					$degreeNames = array();
					$univNames = array();
					$eduDescs = array();
					$companyNames = array();
					$titleNames = array();
					$jobDescs = array();
					while(isset($_POST['expName'.$x]) || isset($_POST['expDesc'.$x])) {
						$expName = mysqli_real_escape_string($conn, $_POST['expName'.$x]);
						$expDesc = mysqli_real_escape_string($conn, $_POST['expDesc'.$x]);
						if(strcmp($expName, "") != 0 || strcmp($expDesc, "") != 0) {
							$expNames[] = $expName;
							$expDescs[] = $expDesc;
						}
						$x++;
					}
					$x = 0;
					
					while(isset($_POST['skillName'.$x]) || isset($_POST['skillDesc'.$x])) {
						$skillName = mysqli_real_escape_string($conn, $_POST['skillName'.$x]);
						$skillDesc = mysqli_real_escape_string($conn, $_POST['skillDesc'.$x]);
						if(strcmp($skillName, "") != 0 || strcmp($skillDesc, "") != 0) {
							$skillNames[] = $skillName;
							$skillDescs[] = $skillDesc;
						}
						$x++;
					}
					$x = 0;
					
					while(isset($_POST['degreeName'.$x]) || isset($_POST['universityName'.$x]) || isset($_POST['eduDesc'.$x])) {
						$degreeName = mysqli_real_escape_string($conn, $_POST['degreeName'.$x]);
						$univName = mysqli_real_escape_string($conn, $_POST['universityName'.$x]);
						$eduDesc = mysqli_real_escape_string($conn, $_POST['eduDesc'.$x]);
						if(strcmp($degreeName, "") != 0 || strcmp($univName, "") != 0 || strcmp($eduDesc, "") != 0) {
							$degreeNames[] = $degreeName;
							$univNames[] = $univName;
							$eduDescs[] = $eduDesc;
						}
						$x++;
					}
					$x = 0;
					
					while(isset($_POST['companyName'.$x]) || isset($_POST['titleName'.$x]) || isset($_POST['jobDesc'.$x])) {
						$companyName = mysqli_real_escape_string($conn, $_POST['companyName'.$x]);
						$titleName = mysqli_real_escape_string($conn, $_POST['titleName'.$x]);
						$jobDesc = mysqli_real_escape_string($conn, $_POST['jobDesc'.$x]);
						if(strcmp($companyName, "") != 0 || strcmp($titleName, "") != 0 || strcmp($jobDesc, "") != 0) {
							$companyNames[] = $companyName;
							$titleNames[] = $titleName;
							$jobDescs[] = $jobDesc;
						}
						$x++;
					}
					
					$errMsg = "";
					for($x=0; $x<count($expNames); $x++) {
						$expName = $expNames[$x];
						$expDesc = $expDescs[$x];
						if(strcmp($expName, "") != 0 || strcmp($expDesc, "") != 0) {
							$call = "CALL SetUserExpertise('$user_id', '$expName', '$expDesc');";
							$q = $conn->query($call);
							mysqli_next_result($conn);
						}
					}
					
					for($x=0; $x<count($skillNames); $x++) {
						$skillName = $skillNames[$x];
						$skillDesc = $skillDescs[$x];
						if(strcmp($skillName, "") != 0 || strcmp($skillDesc, "") != 0) {
							$call = "CALL SetUserSkill('$user_id', '$skillName', '$skillDesc');";
							$q = $conn->query($call);
							mysqli_next_result($conn);
						}
					}
					
					for($x=0; $x<count($degreeNames); $x++) {
						$degreeName = $degreeNames[$x];
						$univName = $univNames[$x];
						$eduDesc = $eduDescs[$x];
						if(strcmp($degreeName, "") != 0 || strcmp($univName, "") != 0 || strcmp($eduDesc, "") != 0) {
							$call = "CALL SetUserEducation('$user_id', '$degreeName', '$univName','$eduDesc');";
							$q = $conn->query($call);
							mysqli_next_result($conn);
						}
					}
					
					for($x=0; $x<count($companyNames); $x++) {
						$companyName = $companyNames[$x];
						$titleName = $titleNames[$x];
						$jobDesc = $jobDescs[$x];
						if(strcmp($companyName, "") != 0 || strcmp($titleName, "") != 0 || strcmp($jobDesc, "") != 0) {
							$call = "CALL SetUserWorkExperience('$user_id', '$companyName', '$titleName', '$jobDesc');";
							$q = $conn->query($call);
							mysqli_next_result($conn);
						}
					}
					
					if(count($expNames) == 0) {
						echo "<script>alert(Got here);</script>";
						$tempExpName = "";
						$tempExpDesc = "";
						$call = "CALL SetUserExpertise('$user_id', '$tempExpName' , '$tempExpDesc');";
						$q = $conn->query($call);
						mysqli_next_result($conn);
					}
					if(count($skillNames) == 0) {
						$tempSkillName = "";
						$tempSkillDesc = "";
						$call = "CALL SetUserSkill('$user_id', '$tempSkillName' , '$tempSkillDesc');";
						$q = $conn->query($call);
						mysqli_next_result($conn);
					}
					if(count($degreeNames) == 0) {
						$tempDegreeName = "";
						$tempUnivName = "";
						$tempEduDesc = "";
						$call = "CALL SetUserEducation('$user_id', '$tempDegreeName', '$tempUnivName','$tempEduDesc');";
						$q = $conn->query($call);
						mysqli_next_result($conn);
					}
					if(count($companyNames) == 0) {
						$tempCompanyName = "";
						$tempTitleName = "";
						$tempJobDesc = "";
						$call = "CALL SetUserWorkExperience('$user_id', '$tempCompanyName', '$tempTitleName', '$tempJobDesc');";
						$q = $conn->query($call);
						mysqli_next_result($conn);
					}
					
					$EmailHash = md5( rand(0,1337) );
					$call = "CALL CreateEmailVerification('$email','$EmailHash');";
					$q = $conn->query($call);
					mysqli_next_result($conn);
					sendEmailVerification($email,$EmailHash);
				  
					//Log new Mentor in
					$sql = "CALL GetUser('$user_id')";
					$q = $conn->query($sql);
					$r = $q->fetch_assoc();
					$IsEnabled = $r['IsEnabled'];
					$IsMentor = $r['IsMentor'];
					$FullName = $r['Name'];
					mysqli_free_result($q);
					mysqli_next_result($conn);
					$_SESSION['user_id'] = $user_id;
					$_SESSION['IsEnabled'] = $IsEnabled;
					$_SESSION['IsMentor'] = $IsMentor;
					$_SESSION['full_name'] = $FullName;
					
					echo "<script> location.href='Login.php'; </script>";
					exit;
				}
			}
		}
	}
?>

<html>
<head>
<script>
	var expCnt = 0;
	var skillCnt = 0;
	var eduCnt = 0;
	var jobCnt = 0;
	var intervalId;
	
	if(window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	};
	
	function pwdIntegrity() {
		var pwd = document.getElementById("pwd").value;
		var pwdRep = document.getElementById("pwdRepeat").value;
		if(pwd != "" && pwdRep != "") {
			if(pwd != pwdRep) {
				document.getElementById("pwdError").innerHTML = "<p style=\"color: #FF0000;\">Passwords do not match.</p>";
			}
			else {
				document.getElementById("pwdError").innerHTML = "";
			}
		}
		else {
			document.getElementById("pwdError").innerHTML = "";
		}
	}
	
	function resetPwdMsg() {
		document.getElementById("pwdError").innerHTML = "";
	}
	
	function addRow(area) {
		var curInnerHtml = "";
		var tempHtml = "";
		var x;
		if(area == 0) {
			for(x=0; x <= expCnt; x++) {
				tempHtml = "<tr id = \"exp"+x+"\"><td style=\"width: 50%;\"><textarea name=\"expName"+x+"\" id=\"expName"+x+"\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"Mathematics.\">"+document.getElementById("expName"+x).value+"</textarea></td><td style=\"width: 50%;\"><textarea name=\"expDesc"+x+"\" id=\"expDesc"+x+"\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"I am proficient in mathematics and achieved my minor in college.\">"+document.getElementById("expDesc"+x).value+"</textarea></td></tr>";
				curInnerHtml += tempHtml;
			}
			expCnt++;
			tempHtml = "<tr id = \"exp"+expCnt+"\"><td style=\"width: 50%;\"><textarea name=\"expName"+expCnt+"\" id=\"expName"+expCnt+"\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"Mathematics.\"></textarea></td><td style=\"width: 50%;\"><textarea name=\"expDesc"+expCnt+"\" id=\"expDesc"+expCnt+"\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"I am proficient in mathematics and achieved my minor in college.\"></textarea></td></tr>";
			curInnerHtml += tempHtml;
			document.getElementById("expDynamicRows").innerHTML = curInnerHtml;
		}
		else if(area == 1) {
			for(x=0; x <= skillCnt; x++) {
				tempHtml = "<tr id = \"skill"+x+"\"><td style=\"width: 50%;\"><textarea name=\"skillName"+x+"\" id=\"skillName"+x+"\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"Resume Building.\">"+document.getElementById("skillName"+x).value+"</textarea></td><td style=\"width: 50%;\"><textarea name=\"skillDesc"+x+"\" id=\"skillDesc"+x+"\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"I can assist you in building your resume or editting it as necessary.\">"+document.getElementById("skillDesc"+x).value+"</textarea></td></tr>";
				curInnerHtml += tempHtml;
			}
			skillCnt++;
			tempHtml = "<tr id = \"skill"+skillCnt+"\"><td style=\"width: 50%;\"><textarea name=\"skillName"+skillCnt+"\" id=\"skillName"+skillCnt+"\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"Resume Building.\"></textarea></td><td style=\"width: 50%;\"><textarea name=\"skillDesc"+skillCnt+"\" id=\"skillDesc"+skillCnt+"\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"I can assist you in building your resume or editting it as necessary.\"></textarea></td></tr>";
			curInnerHtml += tempHtml;
			document.getElementById("skillDynamicRows").innerHTML = curInnerHtml;
		}
		else if(area == 2) {
			for(x=0; x <= eduCnt; x++) {
				tempHtml = "<tr id=\"edu"+x+"\"><td style=\"width: 33%;\"><textarea name=\"degreeName"+x+"\" id=\"degreeName"+x+"\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"Bachelor of Computer Science\">"+document.getElementById("degreeName"+x).value+"</textarea></td><td style=\"width: 33%;\"><textarea name=\"universityName"+x+"\" id=\"universityName"+x+"\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"Eastern Washington University\">"+document.getElementById("universityName"+x).value+"</textarea></td><td><textarea name=\"eduDesc"+x+"\" id=\"eduDesc"+x+"\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"I achieved my bachelors of computer science at EWU in 2015.\">"+document.getElementById("eduDesc"+x).value+"</textarea></td></tr>";
				curInnerHtml += tempHtml;
			}
			eduCnt++;
			tempHtml = "<tr id=\"edu"+eduCnt+"\"><td style=\"width: 33%;\"><textarea name=\"degreeName"+eduCnt+"\" id=\"degreeName"+eduCnt+"\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"Bachelor of Computer Science\"></textarea></td><td style=\"width: 33%;\"><textarea name=\"universityName"+eduCnt+"\" id=\"universityName"+eduCnt+"\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"Eastern Washington University\"></textarea></td><td><textarea name=\"eduDesc"+eduCnt+"\" id=\"eduDesc"+eduCnt+"\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"I achieved my bachelors of computer science at EWU in 2015.\"></textarea></td></tr>";
			curInnerHtml += tempHtml;
			document.getElementById("eduDynamicRows").innerHTML = curInnerHtml;
		}
		else if(area == 3) {
			for(x=0; x <= jobCnt; x++) {
				tempHtml = "<tr id=\"job"+x+"\"><td style=\"width: 33%;\"><textarea name=\"companyName"+x+"\" id=\"companyName"+x+"\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"Eastern Washington University\">"+document.getElementById("companyName"+x).value+"</textarea></td><td style=\"width: 33%;\"><textarea name=\"titleName"+x+"\" id=\"titleName"+x+"\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"Human Resources Supervisor\">"+document.getElementById("titleName"+x).value+"</textarea></td><td><textarea name=\"jobDesc"+x+"\" id=\"jobDesc"+x+"\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"I am a supervisor in the human resources department at Eastern Washington University.\">"+document.getElementById("jobDesc"+x).value+"</textarea></td></tr>";
				curInnerHtml += tempHtml;
			}
			jobCnt++;
			tempHtml = "<tr id=\"job"+jobCnt+"\"><td style=\"width: 33%;\"><textarea name=\"companyName"+jobCnt+"\" id=\"companyName"+jobCnt+"\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"Eastern Washington University\"></textarea></td><td style=\"width: 33%;\"><textarea name=\"titleName"+jobCnt+"\" id=\"titleName"+jobCnt+"\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"Human Resources Supervisor\"></textarea></td><td><textarea name=\"jobDesc"+jobCnt+"\" id=\"jobDesc"+jobCnt+"\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"I am a supervisor in the human resources department at Eastern Washington University.\"></textarea></td></tr>";
			curInnerHtml += tempHtml;
			document.getElementById("jobDynamicRows").innerHTML = curInnerHtml;
		}
		else {
		}
	}
	function deleteRow(area) {
		var curInnerHtml = "";
		var tempHtml = "";
		var x;
		if(area == 0) {
			for(x=0; x < expCnt; x++) {
				tempHtml = "<tr id = \"exp"+x+"\"><td style=\"width: 50%;\"><textarea name=\"expName"+x+"\" id=\"expName"+x+"\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"Mathematics.\">"+document.getElementById("expName"+x).value+"</textarea></td><td style=\"width: 50%;\"><textarea name=\"expDesc"+x+"\" id=\"expDesc"+x+"\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"I am proficient in mathematics and achieved my minor in college.\">"+document.getElementById("expDesc"+x).value+"</textarea></td></tr>";
				curInnerHtml += tempHtml;
			}
			expCnt--;
			document.getElementById("expDynamicRows").innerHTML = curInnerHtml;
		}
		else if(area == 1) {
			for(x=0; x < skillCnt; x++) {
				tempHtml = "<tr id = \"skill"+x+"\"><td style=\"width: 50%;\"><textarea name=\"skillName"+x+"\" id=\"skillName"+x+"\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"Resume Building.\">"+document.getElementById("skillName"+x).value+"</textarea></td><td style=\"width: 50%;\"><textarea name=\"skillDesc"+x+"\" id=\"skillDesc"+x+"\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"I can assist you in building your resume or editting it as necessary.\">"+document.getElementById("skillDesc"+x).value+"</textarea></td></tr>";
				curInnerHtml += tempHtml;
			}
			skillCnt--;
			document.getElementById("skillDynamicRows").innerHTML = curInnerHtml;
		}
		else if(area == 2) {
			for(x=0; x < eduCnt; x++) {
				tempHtml = "<tr id=\"edu"+x+"\"><td style=\"width: 33%;\"><textarea name=\"degreeName"+x+"\" id=\"degreeName"+x+"\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"Bachelor of Computer Science\">"+document.getElementById("degreeName"+x).value+"</textarea></td><td style=\"width: 33%;\"><textarea name=\"universityName"+x+"\" id=\"universityName"+x+"\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"Eastern Washington University\">"+document.getElementById("universityName"+x).value+"</textarea></td><td><textarea name=\"eduDesc"+x+"\" id=\"eduDesc"+x+"\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"I achieved my bachelors of computer science at EWU in 2015.\">"+document.getElementById("eduDesc"+x).value+"</textarea></td></tr>";
				curInnerHtml += tempHtml;
			}
			eduCnt--;
			document.getElementById("eduDynamicRows").innerHTML = curInnerHtml;
		}
		else if(area == 3) {
			for(x=0; x < jobCnt; x++) {
				tempHtml = "<tr id=\"job"+x+"\"><td style=\"width: 33%;\"><textarea name=\"companyName"+x+"\" id=\"companyName"+x+"\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"Eastern Washington University\">"+document.getElementById("companyName"+x).value+"</textarea></td><td style=\"width: 33%;\"><textarea name=\"titleName"+x+"\" id=\"titleName"+x+"\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"Human Resources Supervisor\">"+document.getElementById("titleName"+x).value+"</textarea></td><td><textarea name=\"jobDesc"+x+"\" id=\"jobDesc"+x+"\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"I am a supervisor in the human resources department at Eastern Washington University.\">"+document.getElementById("jobDesc"+x).value+"</textarea></td></tr>";
				curInnerHtml += tempHtml;
			}
			jobCnt--;
			document.getElementById("jobDynamicRows").innerHTML = curInnerHtml;
		}
		else {
		}
	}
</script>
</head>
<body>
	<div id="content" style="padding: 10px 10px 10px 10px">
		<h4 style="font-size: 24px;">Create Your Account</h4><br/>
		<form action="AccountCreationSecondEdition.php" method="post">
			<div class="form-group">
				<label for="fName" style="font-size: 16px;">First Name:</label>
				<input name="fName" style="width: 45%; margin-left:5%;" type="text" class="form-control" id="fName" placeholder="Enter First Name." required>
			</div>
			<div class="form-group">
				<label for="lName" style="font-size: 16px;">Last Name:</label>
				<input name="lName" style="width: 45%; margin-left:5%;" type="text" class="form-control" id="lName" placeholder="Enter Last Name." required>
			</div>
			<div class="form-group">
				<label for="pwd" style="font-size: 16px;">Password:</label>
				<input name="pwd" style="width: 45%; margin-left:5%;" type="password" class="form-control" id="pwd" placeholder="Enter Password." onfocus="resetPwdMsg();" required>
			</div>
			<div class="form-group">
				<label for="pwdRepeat" style="font-size: 16px;">Repeat Password:</label>
				<input name="pwdRepeat" style="width: 45%; margin-left:5%;" type="password" class="form-control" id="pwdRepeat" placeholder="Enter Password Again." onfocus="resetPwdMsg();" required>
			</div>
			<div id="pwdError"></div>
			<div class="form-group">
				<label for="email" style="font-size: 16px;">Email:</label>
				<input name="email" style="width: 45%; margin-left:5%;" type="email" class="form-control" id="email" placeholder="Enter Email." onfocus="pwdIntegrity();" required>
			</div>
			<div class="form-group">
				<label for="maxMentee" style="font-size: 16px;">Maximum Number of Mentees:</label>
				<input name="maxMentee" style="width: 45%; margin-left:5%;" type="number" min="1" max="100" class="form-control" id="maxMentee" placeholder=">=1" onfocus="pwdIntegrity();" required>
			</div>
			<div class="form-group">
				<label for="bio" style="font-size: 16px;">Biography:</label>
				<textarea name="bio" type="text" rows="6" style="resize:none; width: 95%; margin-left:5%;" class="form-control" id="bio" placeholder="I am an alumni, and I would enjoy giving back to the community." onfocus="pwdIntegrity();" ></textarea>
			</div>
			<div class="form-group">
				<label for="com" style="font-size: 16px;">Communication Methods:</label>
				<textarea name="com" type="text" rows="6" style="resize:none; width: 95%; margin-left:5%;" class="form-control" id="com" placeholder="Preferable to contact me via email or through this program." onfocus="pwdIntegrity();" ></textarea>
			</div>
			<div class="form-group">
				<label for="expTable" style="font-size: 16px;">Expertise:</label>
				<div id="expTable" style="width: 95%; margin-left:5%;">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th hidden="true"></th>
								<th style="width: 50%;">Name</th>
								<th style="width: 50%;">Description</th>
							</tr>
						</thead>
						<tbody id="expDynamicRows">
							<tr id = "exp0">
								<td style="width: 50%;"><textarea name="expName0" id="expName0" rows="4" style="resize:none;width: 100%;" placeholder="Mathematics."></textarea></td>
								<td style="width: 50%;"><textarea name="expDesc0" id="expDesc0" rows="4" style="resize:none;width: 100%;" placeholder="I am proficient in mathematics and achieved my minor in college."></textarea></td>
							</tr>
						</tbody>
					</table>
					<button type="button" onclick="addRow(0);">Add Expertise</button>
					<button type="button" onclick="deleteRow(0);" style="float: right;">Remove Previous Expertise</button>
				</div>
			</div>
			<div class="form-group">
				<label for="skillTable" style="font-size: 16px;">Skills:</label>
				<div id="skillTable" style="width: 95%; margin-left:5%;">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th style="width: 50%;">Name</th>
								<th style="width: 50%;">Description</th>
							</tr>
						</thead>
						<tbody id="skillDynamicRows">
							<tr id="skill0">
								<td style="width: 50%;"><textarea name="skillName0" id="skillName0" rows="4" style="resize:none;width: 100%;" placeholder="Resume Building."></textarea></td>
								<td style="width: 50%;"><textarea name="skillDesc0" id="skillDesc0"rows="4" style="resize:none;width: 100%;" placeholder="I can assist you in building your resume or editting it as necessary."></textarea></td>
							</tr>
						</tbody>
					</table>
					<button type="button" onclick="addRow(1);">Add Skill</button>
					<button type="button" onclick="deleteRow(1);" style="float: right;">Remove Previous Skill</button>
				</div>
			</div>
			
			<div class="form-group">
				<label for="eduTable" style="font-size: 16px;">Education:</label>
				<div id="eduTable" style="width: 95%; margin-left:5%;">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th style="width: 33%;">Degree</th>
								<th style="width: 33%;">University</th>
								<th style="width: 33%;">Description</th>
							</tr>
						</thead>
						<tbody id="eduDynamicRows">
							<tr id="edu0">
								<td style="width: 33%;"><textarea name="degreeName0" id="degreeName0" rows="4" style="resize:none;width: 100%;" placeholder="Bachelor of Computer Science"></textarea></td>
								<td style="width: 33%;"><textarea name="universityName0" id="universityName0" rows="4" style="resize:none;width: 100%;" placeholder="Eastern Washington University"></textarea></td>
								<td><textarea name="eduDesc0" id="eduDesc0" rows="4" style="resize:none;width: 100%;" placeholder="I achieved my bachelors of computer science at EWU in 2015."></textarea></td>
							</tr>
						</tbody>
					</table>
					<button type="button" onclick="addRow(2);">Add Education</button>
					<button type="button" onclick="deleteRow(2);" style="float: right;">Remove Previous Education</button>
				</div>
			</div>
			
			<div class="form-group">
				<label for="jobTable" style="font-size: 16px;">Job Experience:</label>
				<div id="jobTable" style="width: 95%; margin-left:5%;">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th style="width: 33%;">Company</th>
								<th style="width: 33%;">Job Title</th>
								<th style="width: 33%;">Description</th>
							</tr>
						</thead>
						<tbody id="jobDynamicRows">
							<tr id="job0">
								<td style="width: 33%;"><textarea name="companyName0" id="companyName0" rows="4" style="resize:none;width: 100%;" placeholder="Eastern Washington University"></textarea></td>
								<td style="width: 33%;"><textarea name="titleName0" id="titleName0" rows="4" style="resize:none;width: 100%;" placeholder="Human Resources Supervisor"></textarea></td>
								<td><textarea name="jobDesc0" id="jobDesc0" rows="4" style="resize:none;width: 100%;" placeholder="I am a supervisor in the human resources department at Eastern Washington University."></textarea></td>
							</tr>
						</tbody>
					</table>
					<button type="button" onclick="addRow(3);">Add Job Experience</button>
					<button type="button" onclick="deleteRow(3);" style="float: right;">Remove Previous Job Experience</button>
				</div>
			</div>
			
			<br/>
			<div class="form-group">
				<button type="submit" class="btn btn-primary">Create Account</button>
				<p name="errorMsg" style="color: #FF0000;"><?php echo $errMsg;?></p>
				<div class="container signin">
					<p>Already have an account? <a href="Login.php">Sign in</a>.</p>
				</div>
			</div>
		</form>
	</div>

</body>
</html>

<?php
	include 'includes/footer.php';
?>