<?php
	session_start();
	require_once("../internalIncludes/dbconfig.php");
	include 'includes/pageOverlay.php';
	include 'includes/header.php';
	include 'includes/site-header-loader.php';
	include 'includes/emailFunctions.php';
	
	if(isset($_SESSION['user_id'])) {
		$user_id = $_SESSION['user_id'];
		$call = "CALL GetUser('$user_id')";
		$q = $conn->query($call);
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

		if($IsMentor != 1)
		{
			echo "<script> location.href='Login.php'; </script>";
			exit;
		}
		
		$call = "CALL GetMentor('$user_id')";
        $q = $conn->query($call);
        $r = $q->fetch_assoc();
        $fName = $r['FirstName'];
        $lName = $r['LastName'];
        $email = $r['Email'];
        $maxMentees = $r['MaxNumMentees'];
        mysqli_free_result($q);
        mysqli_next_result($conn);
		
		if(isset($_POST['fName'])) {
			$newFname = mysqli_real_escape_string($conn, $_POST['fName']);
			$newLname = mysqli_real_escape_string($conn, $_POST['lName']);
			$newEmail = $_POST['email'];
			$newMaxMentees = $_POST['maxMentee'];
			$newBio = mysqli_real_escape_string($conn, $_POST['bio']);
			$newCom = mysqli_real_escape_string($conn, $_POST['com']);
			if(strcmp($newFname, "") != 0) {
				$call = "CALL EditUserFirstName('$user_id', '$newFname');";
				$q = $conn->query($call);
				$r = $q->fetch_assoc();
				mysqli_free_result($q);
				mysqli_next_result($conn);
			}
			if(strcmp($newLname, "") != 0) {
				$call = "CALL EditUserLastName('$user_id', '$newLname');";
				$q = $conn->query($call);
				mysqli_next_result($conn);
			}
			if(strcmp($newEmail, "") != 0) {
				//edit call
			}
			if(strcmp($newMaxMentees, "") != 0) {
				if($newMaxMentees > 0 && $newMaxMentees < 101) {
					$call = "CALL EditMentorMaxNumMentee('$user_id', '$newMaxMentees');";
					$q = $conn->query($call);
					mysqli_next_result($conn);
				}
			}
			
			$entryId;
			
			$call = "CALL GetUserBiography('$user_id');";
			$q = $conn->query($call);
			$r = $q->fetch_assoc();
			$entryId = $r['EntryID'];
			mysqli_free_result($q);
			mysqli_next_result($conn);
			$call = "CALL EditUserBiography('$entryId', '$newBio');";
			$q = $conn->query($call);
			mysqli_next_result($conn);
			
			$call = "CALL GetUserPrimaryCommunication('$user_id');";
			$q = $conn->query($call);
			$r = $q->fetch_assoc();
			$entryId = $r['EntryID'];
			mysqli_free_result($q);
			mysqli_next_result($conn);
			$call = "CALL EditUserPrimaryCommunication('$entryId', 'Phone/Text', '$newCom');";
			$q = $conn->query($call);
			mysqli_next_result($conn);
			
			$highestRowExp = $_POST['highestNewExp'];
			$highestRowSkill = $_POST['highestNewSkill'];
			$highestRowEdu = $_POST['highestNewEdu'];
			$highestRowJob = $_POST['highestNewJob'];
			
			$newExpNames = array();
			$newExpDescs = array();
			$newSkillNames = array();
			$newSkillDescs = array();
			$newDegreeNames = array();
			$newUnivNames = array();
			$newEduDescs = array();
			$newCompanyNames = array();
			$newTitleNames = array();
			$newJobDescs = array();
			$x = 0;
			for($x=0; $x <$highestRowExp; $x++) {
				if(isset($_POST['expNameNew'.$x]) || isset($_POST['expDescNew'.$x])) {
					$newExpNames[] = mysqli_real_escape_string($conn, $_POST['expNameNew'.$x]);
					$newExpDescs[] = mysqli_real_escape_string($conn, $_POST['expDescNew'.$x]);
				}
			}
			for($x=0; $x <$highestRowSkill; $x++) {
				if(isset($_POST['skillNameNew'.$x]) || isset($_POST['skillDescNew'.$x])) {
					$newSkillNames[] = mysqli_real_escape_string($conn, $_POST['skillNameNew'.$x]);
					$newSkillDescs[] = mysqli_real_escape_string($conn, $_POST['skillDescNew'.$x]);
				}
			}
			for($x=0; $x <$highestRowEdu; $x++) {
				if(isset($_POST['degreeNameNew'.$x]) || isset($_POST['universityNameNew'.$x]) || isset($_POST['eduDescNew'.$x])) {
					$newDegreeNames[] = mysqli_real_escape_string($conn, $_POST['degreeNameNew'.$x]);
					$newUnivNames[] = mysqli_real_escape_string($conn, $_POST['universityNameNew'.$x]);
					$newEduDescs[] = mysqli_real_escape_string($conn, $_POST['eduDescNew'.$x]);
				}
			}
			for($x=0; $x <$highestRowJob; $x++) {
				if(isset($_POST['companyNameNew'.$x]) || isset($_POST['titleNameNew'.$x]) || isset($_POST['jobDescNew'.$x])) {
					$newCompanyNames[] = mysqli_real_escape_string($conn, $_POST['companyNameNew'.$x]);
					$newTitleNames[] = mysqli_real_escape_string($conn, $_POST['titleNameNew'.$x]);
					$newJobDescs[] = mysqli_real_escape_string($conn, $_POST['jobDescNew'.$x]);
				}
			}
			
			$expEditEntryIds = array();
			$expEditNames = array();
			$expEditDescs = array();
			$expDeleteEntryIds = array();
			$skillEditEntryIds = array();
			$skillEditNames = array();
			$skillEditDescs = array();
			$skillDeleteEntryIds = array();
			$eduEditEntryIds = array();
			$eduEditDegrees = array();
			$eduEditUnivs = array();
			$eduEditDescs = array();
			$eduDeleteEntryIds = array();
			$jobEditEntryIds = array();
			$jobEditCompanies = array();
			$jobEditTitles = array();
			$jobEditDescs = array();
			$jobDeleteEntryIds = array();
			
			$call = "CALL GetUserExpertise('$user_id')";
			$q = $conn->query($call);
			while($r = $q->fetch_assoc()) {
				$entryId = $r['EntryID'];
				if(isset($_POST['expName'.$entryId]) || isset($_POST['expDesc'.$entryId])) {
					if(strcmp($_POST['expName'.$entryId], "") != 0 || strcmp($_POST['expDesc'.$entryId], "") != 0) {
						$expEditEntryIds[] = $entryId;
						$expEditNames[] = $_POST['expName'.$entryId];
						$expEditDescs[] = $_POST['expDesc'.$entryId];
					}
					else {
						$expDeleteEntryIds[] = $entryId;
					}
				}
				else {
					$expDeleteEntryIds[] = $entryId;
				}
			}
			mysqli_free_result($q);
			mysqli_next_result($conn);
			
			
			$call = "CALL GetUserSkills('$user_id')";
			$q = $conn->query($call);
			while($r = $q->fetch_assoc()) {
				$entryId = $r['EntryID'];
				if(isset($_POST['skillName'.$entryId]) || isset($_POST['skillDesc'.$entryId])) {
					if(strcmp($_POST['skillName'.$entryId], "") != 0 || strcmp($_POST['skillDesc'.$entryId], "") != 0) {
						$skillEditEntryIds[] = $entryId;
						$skillEditNames[] = $_POST['skillName'.$entryId];
						$skillEditDescs[] = $_POST['skillDesc'.$entryId];
					}
					else {
						$skillDeleteEntryIds[] = $entryId;
					}
				}
				else {
					$skillDeleteEntryIds[] = $entryId;
				}
			}
			mysqli_free_result($q);
			mysqli_next_result($conn);
			
			$call = "CALL GetUserEducation('$user_id')";
			$q = $conn->query($call);
			while($r = $q->fetch_assoc()) {
				$entryId = $r['EntryID'];
				if(isset($_POST['degreeName'.$entryId]) || isset($_POST['universityName'.$entryId]) || isset($_POST['eduDesc'.$entryId])) {
					if(strcmp($_POST['degreeName'.$entryId], "") != 0 || strcmp($_POST['universityName'.$entryId], "") != 0 || strcmp($_POST['eduDesc'.$entryId], "") != 0) {
						$eduEditEntryIds[] = $entryId;
						$eduEditDegrees[] = $_POST['degreeName'.$entryId];
						$eduEditUnivs[] = $_POST['universityName'.$entryId];
						$eduEditDescs[] = $_POST['eduDesc'.$entryId];
					}
					else {
						$eduDeleteEntryIds = $entryId;
					}
				}
				else {
					$eduDeleteEntryIds = $entryId;
				}
			}
			mysqli_free_result($q);
			mysqli_next_result($conn);
			
			$call = "CALL GetUserWorkExperience('$user_id')";
			$q = $conn->query($call);
			while($r = $q->fetch_assoc()) {
				$entryId = $r['EntryID'];
				if(isset($_POST['companyName'.$entryId]) || isset($_POST['titleName'.$entryId]) || isset($_POST['jobDesc'.$entryId])) {
					if(strcmp($_POST['companyName'.$entryId], "") != 0 || strcmp($_POST['titleName'.$entryId], "") != 0 || strcmp($_POST['jobDesc'.$entryId], "") != 0) {
						$jobEditEntryIds[] = $entryId;
						$jobEditCompanies[] = $_POST['companyName'.$entryId];
						$jobEditTitles[] = $_POST['titleName'.$entryId];
						$jobEditDescs[] = $_POST['jobDesc'.$entryId];
					}
					else {
						$jobDeleteEntryIds[] = $entryId;
					}
				}
				else {
					$jobDeleteEntryIds[] = $entryId;
				}
			}
			mysqli_free_result($q);
			mysqli_next_result($conn);
			
			
			for($x=0; $x<count($newExpNames); $x++) {
				$newExpName = $newExpNames[$x];
				$newExpDesc = $newExpDescs[$x];
				if(strcmp($newExpName, "") != 0 || strcmp($newExpDesc, "") != 0) {
					$call = "CALL SetUserExpertise('$user_id', '$newExpName', '$newExpDesc');";
					$q = $conn->query($call);
					mysqli_next_result($conn);
				}
			}
			
			for($x=0; $x<count($newSkillNames); $x++) {
				$newSkillName = $newSkillNames[$x];
				$newSkillDesc = $newSkillDescs[$x];
				if(strcmp($newSkillName, "") != 0 || strcmp($newSkillDesc, "") != 0) {
					$call = "CALL SetUserSkill('$user_id', '$newSkillName', '$newSkillDesc');";
					$q = $conn->query($call);
					mysqli_next_result($conn);
				}
			}
			
			for($x=0; $x<count($newDegreeNames); $x++) {
				$newDegreeName = $newDegreeNames[$x];
				$newUnivName = $newUnivNames[$x];
				$newEduDesc = $newEduDescs[$x];
				if(strcmp($newDegreeName, "") != 0 || strcmp($newUnivName, "") != 0 || strcmp($newEduDesc, "") != 0) {
					$call = "CALL SetUserEducation('$user_id', '$newDegreeName', '$newUnivName','$newEduDesc');";
					$q = $conn->query($call);
					mysqli_next_result($conn);
				}
			}
			
			for($x=0; $x<count($newCompanyNames); $x++) {
				$newCompanyName = $newCompanyNames[$x];
				$newTitleName = $newTitleNames[$x];
				$newJobDesc = $newJobDescs[$x];
				if(strcmp($newCompanyName, "") != 0 || strcmp($newTitleName, "") != 0 || strcmp($newJobDesc, "") != 0) {
					$call = "CALL SetUserWorkExperience('$user_id', '$newCompanyName', '$newTitleName', '$newJobDesc');";
					$q = $conn->query($call);
					mysqli_next_result($conn);
				}
			}
			
			
			foreach($expDeleteEntryIds as $e_id) {
				$call = "CALL DeleteUserExpertise('$e_id');";
				$q = $conn->query($call);
				mysqli_next_result($conn);
			}
			foreach($skillDeleteEntryIds as $e_id) {
				$call = "CALL DeleteUserSkill('$e_id');";
				$q = $conn->query($call);
				mysqli_next_result($conn);
			}
			foreach($eduDeleteEntryIds as $e_id) {
				$call = "CALL DeleteUserEducation('$e_id');";
				$q = $conn->query($call);
				mysqli_next_result($conn);
			}
			foreach($jobDeleteEntryIds as $e_id) {
				$call = "CALL DeleteUserWorkExperience('$e_id');";
				$q = $conn->query($call);
				mysqli_next_result($conn);
			}
			
			for($x=0; $x<count($expEditEntryIds); $x++) {
				$entryId = $expEditEntryIds[$x];
				$tempExpName = $expEditNames[$x];
				$tempExpDesc = $expEditDescs[$x];
				$call = "CALL EditUserExpertise('$entryId', '$tempExpName', '$tempExpDesc');";
				$q = $conn->query($call);
				mysqli_next_result($conn);
			}
			
			for($x=0; $x<count($skillEditEntryIds); $x++) {
				$entryId = $skillEditEntryIds[$x];
				$tempSkillName = $skillEditNames[$x];
				$tempSkillDesc = $skillEditDescs[$x];
				$call = "CALL EditUserSkill('$entryId', '$tempSkillName', '$tempSkillDesc');";
				$q = $conn->query($call);
				mysqli_next_result($conn);
			}
			
			for($x=0; $x<count($eduEditEntryIds); $x++) {
				$entryId = $eduEditEntryIds[$x];
				$tempdegreeName = $eduEditDegrees[$x];
				$tempUnivName = $eduEditUnivs[$x];
				$tempEduDesc = $eduEditDescs[$x];
				$call = "CALL EditUserEducation('$entryId', '$tempdegreeName', '$tempUnivName', '$tempEduDesc');";
				$q = $conn->query($call);
				mysqli_next_result($conn);
			}
			
			for($x=0; $x<count($jobEditEntryIds); $x++) {
				$entryId = $jobEditEntryIds[$x];
				$tempCompanyName = $jobEditCompanies[$x];
				$tempTitleDesc = $jobEditTitles[$x];
				$tempJobDesc = $jobEditDescs[$x];
				$call = "CALL EditUserWorkExperience('$entryId', '$tempCompanyName', '$tempTitleDesc', '$tempJobDesc');";
				$q = $conn->query($call);
				mysqli_next_result($conn);
			}
		}
		
		
		
		
		$call = "CALL GetMentor('$user_id')";
        $q = $conn->query($call);
        $r = $q->fetch_assoc();
        $fName = $r['FirstName'];
        $lName = $r['LastName'];
        $email = $r['Email'];
        $maxMentees = $r['MaxNumMentees'];
        mysqli_free_result($q);
        mysqli_next_result($conn);
		
		
		$expEntryIds = array();
		$expNames = array();
		$expDescs = array();
		$skillEntryIds = array();
		$skillNames = array();
		$skillDescs = array();
		$eduEntryIds = array();
		$degreeNames = array();
		$univNames = array();
		$eduDescs = array();
		$jobEntryIds = array();
		$companyNames = array();
		$titleNames = array();
		$jobDescs = array();
		
		$call = "CALL GetUserExpertise('$user_id')";
        $q = $conn->query($call);
        while($r = $q->fetch_assoc()) {
			$expEntryIds[] = $r['EntryID'];
			$expNames[] = $r['ExpertiseName'];
			$expDescs[] = $r['Details'];
		}
        mysqli_free_result($q);
        mysqli_next_result($conn);
		$expCnt = count($expEntryIds);
		
		$call = "CALL GetUserSkills('$user_id')";
        $q = $conn->query($call);
        while($r = $q->fetch_assoc()) {
			$skillEntryIds[] = $r['EntryID'];
			$skillNames[] = $r['SkillName'];
			$skillDescs[] = $r['Details'];
		}
        mysqli_free_result($q);
        mysqli_next_result($conn);
		$skillCnt = count($skillEntryIds);
		
		$call = "CALL GetUserEducation('$user_id')";
        $q = $conn->query($call);
        while($r = $q->fetch_assoc()) {
			$eduEntryIds[] = $r['EntryID'];
			$degreeNames[] = $r['DegreeName'];
			$univNames[] = $r['UniversityName'];
			$eduDescs[] = $r['Details'];
		}
        mysqli_free_result($q);
        mysqli_next_result($conn);
		$eduCnt = count($eduEntryIds);
		
		$call = "CALL GetUserWorkExperience('$user_id')";
        $q = $conn->query($call);
        while($r = $q->fetch_assoc()) {
			$jobEntryIds[] = $r['EntryID'];
			$companyNames[] = $r['CompanyName'];
			$titleNames[] = $r['JobName'];
			$jobDescs[] = $r['Details'];
		}
        mysqli_free_result($q);
        mysqli_next_result($conn);
		$jobCnt = count($jobEntryIds);
		
		$expInfo = array($expEntryIds, $expNames, $expDescs);
		$skillInfo = array($skillEntryIds, $skillNames, $skillDescs);
		$eduInfo = array($eduEntryIds, $degreeNames, $univNames, $eduDescs);
		$jobInfo = array($jobEntryIds, $companyNames, $titleNames, $jobDescs);
		
		$expInnerHtml = $skillInnerHtml = $eduInnerHtml = $jobInnerHtml = "";
		$x = 0;
		for($x = 0; $x < count($expInfo[0]); $x++) {
			$exp_tag_name = "exp".$expInfo[0][$x];
			$exp_tag_id = "exp".$x;
			$exp_btn_tag_id = "expBtn".$x;//.$expInfo[0][$x];
			$exp_name_tag_id = "expName".$x;//.$expInfo[0][$x];
			$exp_name_tag_name = "expName".$expInfo[0][$x];
			$exp_desc_tag_name = "expDesc".$expInfo[0][$x];
			$exp_desc_tag_id = "expDesc".$x;//.$expInfo[0][$x];
			$exp_name = $expInfo[1][$x];
			$exp_desc = $expInfo[2][$x];
			$expInnerHtml .= "<tr id = \"$exp_tag_id\"><td style=\"width: 50%;\"><textarea name=\"$exp_name_tag_name\" id=\"$exp_name_tag_id\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"No Entry Specified\" disabled>$exp_name</textarea></td><td style=\"width: 50%;\"><textarea name=\"$exp_desc_tag_name\" id=\"$exp_desc_tag_id\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"No Entry Specified\" disabled>$exp_desc</textarea></td><td id = \"$exp_btn_tag_id\" hidden=\"true\"><button type=\"button\" onclick=\"deleteRow(0, this, -1);\" style=\"float: right;\">Delete</button></td></tr>";
		}
		for($x = 0; $x < count($skillInfo[0]); $x++) {
			$skill_tag_id = "skill".$skillInfo[0][$x];
			$skill_btn_tag_id = "skillBtn".$x;//.$expInfo[0][$x];
			$skill_name_tag_id = "skillName".$x;//.$skillInfo[0][$x];
			$skill_name_tag_name = "skillName".$skillInfo[0][$x];
			$skill_desc_tag_id = "skillDesc".$x;//.$skillInfo[0][$x];
			$skill_desc_tag_name = "skillDesc".$skillInfo[0][$x];
			$skill_name = $skillInfo[1][$x];
			$skill_desc = $skillInfo[2][$x];
			$skillInnerHtml .= "<tr id=\"$skill_tag_id\"><td style=\"width: 50%;\"><textarea name=\"$skill_name_tag_name\" id=\"$skill_name_tag_id\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"No Entry Specified\" disabled>$skill_name</textarea></td><td style=\"width: 50%;\"><textarea name=\"$skill_desc_tag_name\" id=\"$skill_desc_tag_id\"rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"No Entry Specified\" disabled>$skill_desc</textarea></td><td id = \"$skill_btn_tag_id\" hidden=\"true\"><button type=\"button\" onclick=\"deleteRow(1, this, -1);\" style=\"float: right;\">Delete</button></td></tr>";
		}
		for($x = 0; $x < count($eduInfo[0]); $x++) {
			$edu_tag_id = "edu".$eduInfo[0][$x];
			$edu_btn_tag_id = "eduBtn".$x;//.$expInfo[0][$x];
			$edu_deg_name_tag_id = "degreeName".$x;//.$eduInfo[0][$x];
			$edu_deg_name_tag_name = "degreeName".$eduInfo[0][$x];
			$edu_univ_name_tag_id = "universityName".$x;//.$eduInfo[0][$x];
			$edu_univ_name_tag_name = "universityName".$eduInfo[0][$x];
			$edu_desc_tag_id = "eduDesc".$x;//.$eduInfo[0][$x];
			$edu_desc_tag_name = "eduDesc".$eduInfo[0][$x];
			$edu_deg_name = $eduInfo[1][$x];
			$edu_univ_name = $eduInfo[2][$x];
			$edu_desc = $eduInfo[3][$x];
			$eduInnerHtml .= "<tr id=\"$edu_tag_id\"><td style=\"width: 33%;\"><textarea name=\"$edu_deg_name_tag_name\" id=\"$edu_deg_name_tag_id\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"No Entry Specified\" disabled>$edu_deg_name</textarea></td><td style=\"width: 33%;\"><textarea name=\"$edu_univ_name_tag_name\" id=\"$edu_univ_name_tag_id\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"No Entry Specified\" disabled>$edu_univ_name</textarea></td><td><textarea name=\"$edu_desc_tag_name\" id=\"$edu_desc_tag_id\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"No Entry Specified\" disabled>$edu_desc</textarea></td><td id = \"$edu_btn_tag_id\" hidden=\"true\"><button type=\"button\" onclick=\"deleteRow(2, this, -1);\" style=\"float: right;\">Delete</button></td></tr>";
		}
		for($x = 0; $x < count($jobInfo[0]); $x++) {
			$job_tag_id = "job".$jobInfo[0][$x];
			$job_btn_tag_id = "jobBtn".$x;//.$expInfo[0][$x];
			$job_comp_name_tag_id = "companyName".$x;//.$jobInfo[0][$x];
			$job_comp_name_tag_name = "companyName".$jobInfo[0][$x];
			$job_title_name_tag_id = "titleName".$x;//.$jobInfo[0][$x];
			$job_title_name_tag_name = "titleName".$jobInfo[0][$x];
			$job_desc_tag_id = "jobDesc".$x;//.$jobInfo[0][$x];
			$job_desc_tag_name = "jobDesc".$jobInfo[0][$x];
			$job_comp_name = $jobInfo[1][$x];
			$job_title_name = $jobInfo[2][$x];
			$job_desc = $jobInfo[3][$x];
			$jobInnerHtml .= "<tr id=\"$job_tag_id\"><td style=\"width: 33%;\"><textarea name=\"$job_comp_name_tag_name\" id=\"$job_comp_name_tag_id\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"No Entry Specified\" disabled>$job_comp_name</textarea></td><td style=\"width: 33%;\"><textarea name=\"$job_title_name_tag_name\" id=\"$job_title_name_tag_id\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"No Entry Specified\" disabled>$job_title_name</textarea></td><td><textarea name=\"$job_desc_tag_name\" id=\"$job_desc_tag_id\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"No Entry Specified\" disabled>$job_desc</textarea></td><td id = \"$job_btn_tag_id\" hidden=\"true\"><button type=\"button\" onclick=\"deleteRow(3, this, -1);\" style=\"float: right;\">Delete</button></td></tr>";
		}
		
		$call = "CALL GetUserBiography('$user_id');";
		$q = $conn->query($call);
		$r = $q->fetch_assoc();
		$bioEntryId = $r['EntryID'];
		$bio = $r['Details'];
		mysqli_free_result($q);
		mysqli_next_result($conn);
		
		$call = "CALL GetUserPrimaryCommunication('$user_id');";
		$q = $conn->query($call);
		$r = $q->fetch_assoc();
		$comEntryID = $r['EntryID'];
		$com = $r['Details'];
		mysqli_free_result($q);
		mysqli_next_result($conn);
	}
	else {
		echo "<script> location.href='Login.php'; </script>";
		exit;
	}
?>

<html>
<head>
<script>
	var newExpCnt = 0;
	var nextExpTagId = 0;
	var newSkillCnt = 0;
	var nextSkillTagId = 0;
	var newEduCnt = 0;
	var nextEduTagId = 0;
	var newJobCnt = 0;
	var nextJobTagId = 0;
	
	if(window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	};
	
	function enableFields() {
		document.getElementById("edit_btn").disabled = true;
		document.getElementById("fName").disabled = false;
		document.getElementById("lName").disabled = false;
		document.getElementById("email").disabled = false;
		document.getElementById("maxMentee").disabled = false;
		document.getElementById("bio").disabled = false;
		document.getElementById("com").disabled = false;
		document.getElementById("submit_btn_area").innerHTML = "<button type=\"submit\" style=\"float:right;\">Submit Changes</button>";
		
		var curExpCnt = document.getElementById("expCnt").value;
		var curSkillCnt = document.getElementById("skillCnt").value;
		var curEduCnt = document.getElementById("eduCnt").value;
		var curJobCnt = document.getElementById("jobCnt").value;
		console.log(curExpCnt);
		var x = 0;
		for(x=0; x<curExpCnt; x++) {
			document.getElementById("expName"+x).disabled = false;
			document.getElementById("expDesc"+x).disabled = false;
			document.getElementById("expBtn"+x).hidden = false;
		}
		for(x=0; x<curSkillCnt; x++) {
			document.getElementById("skillName"+x).disabled = false;
			document.getElementById("skillDesc"+x).disabled = false;
			document.getElementById("skillBtn"+x).hidden = false;
		}
		for(x=0; x<curEduCnt; x++) {
			document.getElementById("degreeName"+x).disabled = false;
			document.getElementById("universityName"+x).disabled = false;
			document.getElementById("eduDesc"+x).disabled = false;
			document.getElementById("eduBtn"+x).hidden = false;
		}
		for(x=0; x<curJobCnt; x++) {
			document.getElementById("companyName"+x).disabled = false;
			document.getElementById("titleName"+x).disabled = false;
			document.getElementById("jobDesc"+x).disabled = false;
			document.getElementById("jobBtn"+x).hidden = false;
		}
		document.getElementById("addNewExpBtn").hidden = false;
		document.getElementById("addNewSkillBtn").hidden = false;
		document.getElementById("addNewEduBtn").hidden = false;
		document.getElementById("addNewJobBtn").hidden = false;
		document.getElementById("fName").focus();
	}
	
	function addRow(area) {
		if(area == 0) {
			var table = document.getElementById("expTable");
			var index = document.getElementById("expTable").rows.length;
			var row = table.insertRow(index);
			var c1 = row.insertCell(0);
			var c2 = row.insertCell(1);
			var c3 = row.insertCell(2);
			var c4 = row.insertCell(3);
			
			c1.innerHTML = "<textarea name=\"expNameNew"+nextExpTagId+"\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"Mathematics.\"></textarea>";
			c2.innerHTML = "<textarea name=\"expDescNew"+nextExpTagId+"\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"I am proficient in mathematics and achieved my minor in college.\"></textarea>";
			c3.innerHTML = "<button type=\"button\" onclick=\"deleteRow(0, this, "+nextExpTagId+");\" style=\"float: right;\">Delete</button>";
			newExpCnt++;
			nextExpTagId = newExpCnt;
			console.log(document.getElementById("highestNewExp").value);
			if(document.getElementById("highestNewExp").value < newExpCnt) {
				document.getElementById("highestNewExp").value = newExpCnt;
			}
		}
		else if(area == 1) {
			var table = document.getElementById("skillTable");
			var index = document.getElementById("skillTable").rows.length;
			var row = table.insertRow(index);
			var c1 = row.insertCell(0);
			var c2 = row.insertCell(1);
			var c3 = row.insertCell(2);
			var c4 = row.insertCell(3);
			
			c1.innerHTML = "<textarea name=\"skillNameNew"+nextSkillTagId+"\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"Resume Building.\"></textarea>";
			c2.innerHTML = "<textarea name=\"skillDescNew"+nextSkillTagId+"\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"I can assist you in building your resume or editting it as necessary.\"></textarea>";
			c3.innerHTML = "<button type=\"button\" onclick=\"deleteRow(1, this, "+nextSkillTagId+");\" style=\"float: right;\">Delete</button>";
			newSkillCnt++;
			nextSkillTagId = newSkillCnt;
			if(document.getElementById("highestNewSkill").value < newSkillCnt) {
				document.getElementById("highestNewSkill").value = newSkillCnt;
			}
		}
		else if(area == 2) {
			var table = document.getElementById("eduTable");
			var index = document.getElementById("eduTable").rows.length;
			var row = table.insertRow(index);
			var c1 = row.insertCell(0);
			var c2 = row.insertCell(1);
			var c3 = row.insertCell(2);
			var c4 = row.insertCell(3);
			
			c1.innerHTML = "<textarea name=\"degreeNameNew"+nextEduTagId+"\" rows=\"4\" style=\"resize:none;width:100%;\" placeholder=\"Bachelor of Computer Science\"></textarea>";
			c2.innerHTML = "<textarea name=\"universityNameNew"+nextEduTagId+"\"rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"Eastern Washington University\"></textarea>";
			c3.innerHTML = "<textarea name=\"eduDescNew"+nextEduTagId+"\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"I achieved my bachelors of computer science at EWU in 2015.\"></textarea>";
			c4.innerHTML = "<button type=\"button\" onclick=\"deleteRow(2, this, "+nextEduTagId+");\" style=\"float: right;\">Delete</button>";
			newEduCnt++;
			nextEduTagId = newEduCnt;
			if(document.getElementById("highestNewEdu").value < newEduCnt) {
				document.getElementById("highestNewEdu").value = newEduCnt;
			}
		}
		else if(area == 3) {
			var table = document.getElementById("jobTable");
			var index = document.getElementById("jobTable").rows.length;
			var row = table.insertRow(index);
			var c1 = row.insertCell(0);
			var c2 = row.insertCell(1);
			var c3 = row.insertCell(2);
			var c4 = row.insertCell(3);
			
			c1.innerHTML = "<textarea name=\"companyNameNew"+nextJobTagId+"\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"Eastern Washington University\"></textarea>";
			c2.innerHTML = "<textarea name=\"titleNameNew"+nextJobTagId+"\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"Human Resources Supervisor\"></textarea>";
			c3.innerHTML = "<textarea name=\"jobDescNew"+nextJobTagId+"\" rows=\"4\" style=\"resize:none;width: 100%;\" placeholder=\"I am a supervisor in the human resources department at Eastern Washington University.\"></textarea>";
			c4.innerHTML = "<button type=\"button\" onclick=\"deleteRow(3, this, "+nextJobTagId+");\" style=\"float: right;\">Delete</button>";
			newJobCnt++;
			nextJobTagId = newJobCnt;
			if(document.getElementById("highestNewJob").value < newJobCnt) {
				document.getElementById("highestNewJob").value = newJobCnt;
			}
		}
		else {
		}
	}
	function deleteRow(area, row, next) {
		var index = row.parentNode.parentNode.rowIndex;
		if(area == 0) {
			document.getElementById("expTable").deleteRow(index);
			if(next != -1) {
				newExpCnt--;
				nextExpTagId = next;
			}
		}
		else if(area == 1) {
			document.getElementById("skillTable").deleteRow(index);
			if(next != -1) {
				newSkillCnt--;
				nextSkillTagId = next;
			}
		}
		else if(area == 2) {
			document.getElementById("eduTable").deleteRow(index);
			if(next != -1) {
				newEduCnt--;
				nextEduTagId = next;
			}
		}
		else if(area == 3) {
			document.getElementById("jobTable").deleteRow(index);
			if(next != -1) {
				newJobCnt--;
				nextJobTagId = next;
			}
		}
		else {
			
		}
	}
</script>
</head>
<body>
	<div id="content" style="padding: 10px 10px 10px 10px">
		<h4 style="font-size: 24px;">Your Profile<button id="edit_btn" type="button" style="float:right;" onclick="enableFields();">Edit Profile</button></h4><br/>
		<form id="editForm" action="ProfileSecondEdition.php" method="post">
		
			<input type="hidden" id="expCnt" value="<?php echo $expCnt ?>">
			<input type="hidden" name="highestNewExp" id="highestNewExp" value="0">
			<input type="hidden" id="skillCnt" value="<?php echo $skillCnt ?>">
			<input type="hidden" name="highestNewSkill" id="highestNewSkill" value="0">
			<input type="hidden" id="eduCnt" value="<?php echo $eduCnt ?>">
			<input type="hidden" name="highestNewEdu" id="highestNewEdu" value="0">
			<input type="hidden" id="jobCnt" value="<?php echo $jobCnt ?>">
			<input type="hidden" name="highestNewJob" id="highestNewJob" value="0">

			<div class="form-group">
				<label for="fName" style="font-size: 16px;">First Name:</label>
				<input name="fName" style="width: 45%; margin-left:5%;" type="text" class="form-control" id="fName" value="<?php $printFName = str_replace('"', "&quot", $fName); echo $printFName; ?>" disabled>
			</div>
			<div class="form-group">
				<label for="lName" style="font-size: 16px;">Last Name:</label>
				<input name="lName" style="width: 45%; margin-left:5%;" type="text" class="form-control" id="lName" value="<?php $printLName = str_replace('"', "&quot", $lName); echo $printLName; ?>" disabled>
			</div>
			<div class="form-group">
				<label for="email" style="font-size: 16px;">Email:</label>
				<input name="email" style="width: 45%; margin-left:5%;" type="email" class="form-control" id="email" value="<?php echo $email; ?>" disabled>
			</div>
			<div class="form-group">
				<label for="maxMentee" style="font-size: 16px;">Maximum Number of Mentees:</label>
				<input name="maxMentee" style="width: 45%; margin-left:5%;" type="number" min="1" max="100" class="form-control" id="maxMentee" value="<?php echo $maxMentees; ?>" disabled>
			</div>
			<div class="form-group">
				<label for="bio" style="font-size: 16px;">Biography:</label>
				<textarea name="bio" type="text" rows="6" style="resize:none; width: 95%; margin-left:5%;" class="form-control" id="bio" placeholder="No Entry Specified" disabled><?php echo $bio; ?></textarea>
			</div>
			<div class="form-group">
				<label for="com" style="font-size: 16px;">Communication Methods:</label>
				<textarea name="com" type="text" rows="6" style="resize:none; width: 95%; margin-left:5%;" class="form-control" id="com" placeholder="No Entry Specified" disabled><?php echo $com; ?></textarea>
			</div>
			<div class="form-group">
				<label for="expTable" style="font-size: 16px;">Expertise:</label>
				<div style="width: 95%; margin-left:5%;">
					<table id="expTable" class="table table-bordered">
						<thead>
							<tr>
								<th hidden="true"></th>
								<th style="width: 50%;">Name</th>
								<th style="width: 50%;">Description</th>
							</tr>
						</thead>
						<tbody>
							<?php
								echo $expInnerHtml;
							?>
							<tbody id="expDynamicRows"></tbody>
						</tbody>
					</table>
					<button id="addNewExpBtn" type="button" onclick="addRow(0);" hidden="true">Add Expertise</button>
				</div>
			</div>
			<div class="form-group">
				<label for="skillTable" style="font-size: 16px;">Skills:</label>
				<div style="width: 95%; margin-left:5%;">
					<table id="skillTable" class="table table-bordered">
						<thead>
							<tr>
								<th style="width: 50%;">Name</th>
								<th style="width: 50%;">Description</th>
							</tr>
						</thead>
						<tbody>
							<?php
								echo $skillInnerHtml;
							?>
							<tbody id="skillDynamicRows"></tbody>
						</tbody>
					</table>
					<button id="addNewSkillBtn" type="button" onclick="addRow(1);" hidden="true">Add Skill</button>
				</div>
			</div>
			
			<div class="form-group">
				<label for="eduTable" style="font-size: 16px;">Education:</label>
				<div style="width: 95%; margin-left:5%;">
					<table id="eduTable" class="table table-bordered">
						<thead>
							<tr>
								<th style="width: 33%;">Degree</th>
								<th style="width: 33%;">University</th>
								<th style="width: 33%;">Description</th>
							</tr>
						</thead>
						<tbody>
							<?php
								echo $eduInnerHtml;
							?>
							<tbody id="eduDynamicRows"></tbody>
						</tbody>
					</table>
					<button id="addNewEduBtn" type="button" onclick="addRow(2);" hidden="true">Add Education</button>
				</div>
			</div>
			
			<div class="form-group">
				<label for="jobTable" style="font-size: 16px;">Job Experience:</label>
				<div style="width: 95%; margin-left:5%;">
					<table id="jobTable" class="table table-bordered">
						<thead>
							<tr>
								<th style="width: 33%;">Company</th>
								<th style="width: 33%;">Job Title</th>
								<th style="width: 33%;">Description</th>
							</tr>
						</thead>
						<tbody>
							<?php
								echo $jobInnerHtml;
							?>
							<tbody id="jobDynamicRows"></tbody>
						</tbody>
					</table>
					<button id="addNewJobBtn" type="button" onclick="addRow(3);" hidden="true">Add Job Experience</button>
				</div>
			</div>
			
			<br/>
			<div class="form-group" id="submit_btn_area">
			</div>
		</form>
	</div>

</body>
</html>

<?php
	include 'includes/footer.php';
?>