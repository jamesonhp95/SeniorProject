<html>
<?php
session_start();
require_once("../internalIncludes/dbconfig.php");
include 'includes/pageOverlay.php';
include 'includes/header.php';
include 'includes/site-header-loader.php';

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
  
  if($IsMentor != 1)
  {
	echo "<script> location.href='Login.php'; </script>";
    exit;
  }
}
else
{
	echo "<script> location.href='Login.php'; </script>";
    exit;
}

if(isset($_SESSION['user_id']))
{    
   //change Name if needed
    if(isset($_POST['lastname']) && isset($_POST['firstname']))
    {
        if(strcmp($_POST['lastname'], $_POST['oldlastname']) != 0 || strcmp($_POST['firstname'], $_POST['oldfirstname']) != 0)//it has been changed so delete and add
        {
    
            $fname = $_POST['firstname'];
            $lname = $_POST['lastname'];
            $call = "CALL EditUserFirstName('$user_id', '$fname');";
            $q = $conn->query($call);
            $r = $q->fetch_assoc();
            mysqli_free_result($r);
            mysqli_next_result($conn);
            $call = "CALL EditUserLastName('$user_id', '$lname');";
            $q = $conn->query($call);
            $r = $q->fetch_assoc();
            mysqli_free_result($r);
            mysqli_next_result($conn);
        }
    }
    //change MaxNumMentees if needed
    if(isset($_POST['maxMentees']))
    {
      if(strcmp($_POST['maxMentees'], $_POST['oldmaxMentees']) != 0)//it has been changed so delete and add
      {   
        if(intval($_POST['maxMentees']) == 0)
        {
          $maxMentees = $_POST['oldmaxMentees'];
        }
        else
        {
          $maxMentees = intval($_POST['maxMentees']);
        }
        
        if($maxMentees < 1){$maxMentees = 1;}
        if($maxMentees > 20000){$maxMentees = 20000;}
        $call = "CALL EditMentorMaxNumMentee('$user_id', '$maxMentees');";
        $q = $conn->query($call);
        $r = $q->fetch_assoc();
        mysqli_free_result($r);
        mysqli_next_result($conn);
      }
    }
    
    //change biography if needed
    if(isset($_POST['biography']))
    {
        if(strcmp($_POST['biography'], $_POST['oldbiography']) != 0)//it has been changed so delete and add
        {
            $EntryId = $_POST['oldbiographyEntryId'];
            $call = "CALL DeleteUserBiography('$EntryId')";
            $q = $conn->query($call);
            mysqli_next_result($conn);
            
            $newBiography = $_POST['biography'];
            $call = "CALL SetUserBiography('$user_id', '$newBiography');";
            $q = $conn->query($call);
            $r = $q->fetch_assoc();
            mysqli_free_result($r);
            mysqli_next_result($conn);
        }
    }
     
    //change communication preferences if need be
    if(isset($_POST['comDesc']))
    {
        if(strcmp($_POST['comDesc'], $_POST['oldcomDesc']) != 0)//it has been changed so delete and add
        {
            $EntryId = $_POST['oldcomEntryId'];
            $call = "CALL DeleteUserPrimaryCommunication('$EntryId')";
            $q = $conn->query($call);
            mysqli_next_result($conn);
            
            $comDesc = $_POST['comDesc'];
            $call = "CALL SetUserPrimaryCommunication('$user_id','$comDesc', '$comDesc');";
            $q = $conn->query($call);
            $r = $q->fetch_assoc();
            mysqli_free_result($r);
            mysqli_next_result($conn);
        }
    }
        
        
        
        //-------DELETE ENTRIES THAT HAVE BEEN REMOVED USING POST ARRAY, for oldnumExpertise etc.------------//
        
        //delete old entries exp
        for($x = 0; $x < intval($_POST['oldnumExpertise']); $x++)
        {
            if(isset($_POST['expertiseName'.$x]) && isset($_POST['expertiseDesc'.$x]))//entry exists so check if same
            {
                //echo "<script type='text/javascript'>alert('inside delete if isset x is: ".$x."');</script>";
                $oldexpertiseName = $_POST['oldexpertiseName'.$x];
                $expertiseName = $_POST['expertiseName'.$x];
                $oldexpertiseDesc = $_POST['oldexpertiseDesc'.$x];
                $expertiseDesc = $_POST['expertiseDesc'.$x];
                if(strcmp($oldexpertiseName, $expertiseName) == 0 && strcmp($oldexpertiseDesc, $expertiseDesc) == 0)//they match so do nothing
                {
                    //echo "<script type='text/javascript'>alert('inside if strcmp matches do nothing x is: ".$x."');</script>";
                    continue;
                }
                else//delete since they dont match and its a different entry
                {
                    //echo "<script type='text/javascript'>alert('inside isset and inside else x is: ".$x."');</script>";
                    $EntryId = $_POST['oldexpertiseEntryID'.$x];
                    $call = "CALL DeleteUserExpertise('$EntryId')";
                    $q = $conn->query($call);
                    mysqli_next_result($conn);
                }
            }
            else//if not set then can delete as well since row is deleted
            {
                //echo "<script type='text/javascript'>alert('inside delete else other than isset x is: ".$x."');</script>";
                $EntryId = $_POST['oldexpertiseEntryID'.$x];
                $call = "CALL DeleteUserExpertise('$EntryId')";
                $q = $conn->query($call);
                mysqli_next_result($conn);
            }           
        }
        
        //add new entries exp
        $highestRow = intval($_POST['highestExpRow']);
        for($x = 0; $x < $highestRow; $x++)
        {
            if(isset($_POST['expertiseName'.$x]) && isset($_POST['expertiseDesc'.$x]))//check if the row exists
            {
                $expertiseName = $_POST['expertiseName'.$x];
                $expertiseDesc = $_POST['expertiseDesc'.$x];
                if(isset($_POST['oldexpertiseName'.$x]) && isset($_POST['oldexpertiseDesc'.$x]))//check to make sure already exists
                {
                    $oldexpertiseName = $_POST['oldexpertiseName'.$x];               
                    $oldexpertiseDesc = $_POST['oldexpertiseDesc'.$x];               
                    if(strcmp($oldexpertiseName, $expertiseName) != 0 || strcmp($oldexpertiseDesc, $expertiseDesc) != 0)//they are not the same so can add to database
                    {
                        if(strcmp($expertiseName, "") != 0 && strcmp($expertiseDesc, "") != 0)//add only if its not empty
                        {
                            $call = "CALL SetUserExpertise('$user_id', '$expertiseName', '$expertiseDesc');";
                            $q = $conn->query($call);
                            $r = $q->fetch_assoc();
                            mysqli_free_result($r);
                            mysqli_next_result($conn);
                        }                        
                    }
                }
                else// attempt add to database because its a new entry
                {
                    if(strcmp($expertiseName, "") != 0 && strcmp($expertiseDesc, "") != 0)//add only if its not empty
                    {
                        $call = "CALL SetUserExpertise('$user_id', '$expertiseName', '$expertiseDesc');";
                        $q = $conn->query($call);
                        $r = $q->fetch_assoc();
                        mysqli_free_result($r);
                        mysqli_next_result($conn);
                    }
                }
            }
        }
        
        /*---------------------------------------------------------------------------------------------------------------------*/
        //delete old entries Skills
        for($x = 0; $x < intval($_POST['oldnumSkills']); $x++)
        {
            if(isset($_POST['skillsName'.$x]) && isset($_POST['skillsDesc'.$x]))//entry exists so check if same
            {
                //echo "<script type='text/javascript'>alert('inside delete if isset x is: ".$x."');</script>";
                $oldskillsName = $_POST['oldskillsName'.$x];
                $skillsName = $_POST['skillsName'.$x];
                $oldskillsDesc = $_POST['oldskillsDesc'.$x];
                $skillsDesc = $_POST['skillsDesc'.$x];
                if(strcmp($oldskillsName, $skillsName) == 0 && strcmp($oldskillsDesc, $skillsDesc) == 0)//they match so do nothing
                {
                    //echo "<script type='text/javascript'>alert('inside delete if isset if strcmp x is: ".$x."');</script>";
                    continue;
                }
                else//delete since they dont match and its a different entry
                {
                    //echo "<script type='text/javascript'>alert('inside delete if isset else x is: ".$x."');</script>";
                    $EntryId = $_POST['oldskillsEntryID'.$x];
                    $call = "CALL DeleteUserSkill('$EntryId')";
                    $q = $conn->query($call);
                    mysqli_next_result($conn);
                }
            }
            else//if not set then can delete as well since row is deleted
            {
                //echo "<script type='text/javascript'>alert('inside delete else isset x is: ".$x."');</script>";
                $EntryId = $_POST['oldskillsEntryID'.$x];
                //echo "<script type='text/javascript'>alert('attempting to delete is: ".$EntryId."');</script>";
                $call = "CALL DeleteUserSkill('$EntryId')";
                $q = $conn->query($call);
                mysqli_next_result($conn);
            }
        }
        
        //add new entries Skills
        $highestRow = intval($_POST['highestSkRow']);
        for($x = 0; $x < $highestRow; $x++)
        {
            if(isset($_POST['skillsName'.$x]) && isset($_POST['skillsDesc'.$x]))//check if the row exists
            {
                $skillsName = $_POST['skillsName'.$x];
                $skillsDesc = $_POST['skillsDesc'.$x];
                if(isset($_POST['oldskillsName'.$x]) && isset($_POST['oldskillsDesc'.$x]))//check to make sure already exists
                {
                    $oldskillsName = $_POST['oldskillsName'.$x];
                    $oldskillsDesc = $_POST['oldskillsDesc'.$x];
                    if(strcmp($oldskillsName, $skillsName) != 0 || strcmp($oldskillsDesc, $skillsDesc) != 0)//they are not the same so can add to database
                    {
                        if(strcmp($skillsName, "") != 0 && strcmp($skillsDesc, "") != 0)//add only if its not empty
                        {
                            $call = "CALL SetUserSkill('$user_id', '$skillsName', '$skillsDesc');";
                            $q = $conn->query($call);
                            $r = $q->fetch_assoc();
                            mysqli_free_result($r);
                            mysqli_next_result($conn);
                        }
                    }
                }
                else// attempt add to database because its a new entry
                {
                    if(strcmp($skillsName, "") != 0 && strcmp($skillsDesc, "") != 0)//add only if its not empty
                    {
                        $call = "CALL SetUserSkill('$user_id', '$skillsName', '$skillsDesc');";
                        $q = $conn->query($call);
                        $r = $q->fetch_assoc();
                        mysqli_free_result($r);
                        mysqli_next_result($conn);
                    }
                }
            }
        }
        
        /*---------------------------------------------------------------------------------------------------------------------*/
        //delete old entries Work Experience
        for($x = 0; $x < intval($_POST['oldnumJobs']); $x++)
        {
            if(isset($_POST['jobName'.$x]) && isset($_POST['jobDesc'.$x]) && isset($_POST['companyName'.$x]))//entry exists so check if same
            {
                //echo "<script type='text/javascript'>alert('inside delete if isset x is: ".$x."');</script>";
                $oldjobName = $_POST['oldjobName'.$x];
                $jobName = $_POST['jobName'.$x];
                $oldcompanyName = $_POST['oldcompanyName'.$x];
                $companyName = $_POST['companyName'.$x];
                $oldjobDesc = $_POST['oldjobDesc'.$x];
                $jobDesc = $_POST['jobDesc'.$x];
                if(strcmp($oldjobName, $jobName) == 0 && strcmp($oldjobDesc, $jobDesc) == 0 && strcmp($oldcompanyName, $companyName) == 0)//they match so do nothing
                {
                    //echo "<script type='text/javascript'>alert('inside delete if isset strcmp x is: ".$x."');</script>";
                    continue;
                }
                else//delete since they dont match and its a different entry
                {
                    //echo "<script type='text/javascript'>alert('inside delete else isset x is: ".$x."');</script>";
                    $EntryId = $_POST['oldjobEntryID'.$x];
                    $call = "CALL DeleteWorkExperience('$EntryId')";
                    $q = $conn->query($call);
                    mysqli_next_result($conn);
                }
            }
            else//if not set then can delete as well since row is deleted
            {
                //echo "<script type='text/javascript'>alert('inside delete else x is: ".$x."');</script>";
                $EntryId = $_POST['oldjobEntryID'.$x];
                $call = "CALL DeleteWorkExperience('$EntryId')";
                $q = $conn->query($call);
                mysqli_next_result($conn);
            }
        }
        
        //add new entries Work Experience
        $highestRow = intval($_POST['highestJobRow']);
        for($x = 0; $x < $highestRow; $x++)
        {
            //echo "<script type='text/javascript'>alert('inside work experience x is: ".$x."');</script>";
            if(isset($_POST['jobName'.$x]) && isset($_POST['jobDesc'.$x]) && isset($_POST['companyName'.$x]))//check if the row exists
            {
                $jobName = $_POST['jobName'.$x];
                $jobDesc = $_POST['jobDesc'.$x];
                $companyName = $_POST['companyName'.$x];
      
                if(isset($_POST['oldjobName'.$x]) && isset($_POST['oldjobDesc'.$x]) && isset($_POST['oldcompanyName'.$x]))//check to make sure already exists
                {
                    //echo "<script type='text/javascript'>alert('inside if isset setting old ');</script>";
                    $oldjobName = $_POST['oldjobName'.$x];
                    $oldjobDesc = $_POST['oldjobDesc'.$x];
                    $oldcompanyName = $_POST['oldcompanyName'.$x];
                    
                    if(strcmp($oldjobName, $jobName) != 0 || strcmp($oldjobDesc, $jobDesc) != 0 || strcmp($oldcompanyName, $companyName) != 0)//they are not the same so can add to database
                    {
                        if(strcmp($jobName, "") != 0 && strcmp($jobDesc, "") != 0 && strcmp($companyName, "") != 0)//add only if its not empty
                        {
                            //echo "<script type='text/javascript'>alert('inside if strcmpare attempting to add');</script>";
                            $call = "CALL SetUserWorkExperience('$user_id', '$companyName', '$jobName', '$jobDesc');";
                            $q = $conn->query($call);
                            $r = $q->fetch_assoc();
                            mysqli_free_result($r);
                            mysqli_next_result($conn);
                        }
                    }
                }
                else// attempt add to database because its a new entry
                {
                    if(strcmp($jobName, "") != 0 && strcmp($jobDesc, "") != 0 && strcmp($companyName, "") != 0)//add only if its not empty
                    {
                        //echo "<script type='text/javascript'>alert('inside else strcmpare attempting to add');</script>";
                        $call = "CALL SetUserWorkExperience('$user_id', '$companyName', '$jobName', '$jobDesc');";
                        $q = $conn->query($call);
                        $r = $q->fetch_assoc();
                        mysqli_free_result($r);
                        mysqli_next_result($conn);
                    }
                }
            }
        }
        
        /*---------------------------------------------------------------------------------------------------------------------*/
        //delete old entries Education
        for($x = 0; $x < intval($_POST['oldnumEducation']); $x++)
        {
            if(isset($_POST['universityName'.$x]) && isset($_POST['degreeName'.$x]) && isset($_POST['degreeDesc'.$x]))//entry exists so check if same
            {
                //echo "<script type='text/javascript'>alert('inside Education delete if isset x is: ".$x."');</script>";
                $olddegreeName = $_POST['olddegreeName'.$x];
                $degreeName = $_POST['degreeName'.$x];
                $olduniversityName = $_POST['olduniversityName'.$x];
                $universityName = $_POST['universityName'.$x];
                $olddegreeDesc = $_POST['olddegreeDesc'.$x];
                $degreeDesc = $_POST['degreeDesc'.$x];
                if(strcmp($olddegreeName, $degreeName) == 0 && strcmp($olddegreeDesc, $degreeDesc) == 0 && strcmp($olduniversityName, $universityName) == 0)//they match so do nothing
                {
                    //echo "<script type='text/javascript'>alert('inside Education if isset inner if do nothing x is: ".$x."');</script>";
                    continue;
                }
                else//delete since they dont match and its a different entry
                {
                    //echo "<script type='text/javascript'>alert('inside Education isset else x is: ".$x."');</script>";
                    //echo "<script type='text/javascript'>alert('".strcmp($olddegreeName, $degreeName)."Comparing ".$olddegreeName." and ".$degreeName."');</script>";
                    //echo "<script type='text/javascript'>alert('".strcmp($olduniversityName, $universityName)."Comparing ".$oldduniversityName." and ".$universityName."');</script>";
                    //echo "<script type='text/javascript'>alert('".strcmp($olddegreeDesc, $degreeDesc)."Comparing ".$olddegreeDesc." and ".$degreeDesc."');</script>";
                    $EntryId = $_POST['olddegreeEntryID'.$x];
                    $call = "CALL DeleteUserEducation('$EntryId')";
                    $q = $conn->query($call);
                    mysqli_next_result($conn);
                }
            }
            else//if not set then can delete as well since row is deleted
            {
                //echo "<script type='text/javascript'>alert('inside Education else x is: ".$x."');</script>";
                $EntryId = $_POST['olddegreeEntryID'.$x];
                $call = "CALL DeleteUserEducation('$EntryId')";
                $q = $conn->query($call);
                mysqli_next_result($conn);
            }
        }
        
        //add new entries Education
        $highestRow = intval($_POST['highestEdRow']);
        for($x = 0; $x < $highestRow; $x++)
        {
            if(isset($_POST['degreeName'.$x]) && isset($_POST['degreeDesc'.$x]) && isset($_POST['universityName'.$x]))//check if the row exists
            {
                //echo "<script type='text/javascript'>alert('inside Education add if isset x is: ".$x."');</script>";
                $degreeName = $_POST['degreeName'.$x];
                $degreeDesc = $_POST['degreeDesc'.$x];
                $universityName = $_POST['universityName'.$x];
                if(isset($_POST['olddegreeName'.$x]) && isset($_POST['olddegreeDesc'.$x]) && isset($_POST['olduniversityName'.$x]))//check to make sure already exists
                {
                    //echo "<script type='text/javascript'>alert('inside Education if isset x is: ".$x."');</script>";
                    $olddegreeName = $_POST['olddegreeName'.$x];
                    $olddegreeDesc = $_POST['olddegreeDesc'.$x];
                    $olduniversityName = $_POST['olduniversityName'.$x];
                    
                    if(strcmp($olddegreeName, $degreeName) != 0 || strcmp($olddegreeDesc, $degreeDesc) != 0 || strcmp($olduniversityName, $universityName) != 0)//they are not the same so can add to database
                    {
                        if(strcmp($degreeName, "") != 0 && strcmp($degreeDesc, "") != 0 && strcmp($universityName, "") != 0)//add only if its not empty
                        {
                            //echo "<script type='text/javascript'>alert('inside Education if isset if strcmp not empty x is: ".$x."');</script>";
                            $call = "CALL SetUserEducation('$user_id', '$degreeName', '$universityName', '$degreeDesc');";
                            $q = $conn->query($call);
                            $r = $q->fetch_assoc();
                            mysqli_free_result($r);
                            mysqli_next_result($conn);
                        }
                    }
                }
                else// attempt add to database because its a new entry
                {
                    if(strcmp($degreeName, "") != 0 && strcmp($degreeDesc, "") != 0 && strcmp($universityName, "") != 0)//add only if its not empty
                    {
                        //echo "<script type='text/javascript'>alert('inside Education else x is: ".$x."');</script>";
                        $call = "CALL SetUserEducation('$user_id', '$degreeName', '$universityName', '$degreeDesc');";
                        $q = $conn->query($call);
                        $r = $q->fetch_assoc();
                        mysqli_free_result($r);
                        mysqli_next_result($conn);
                    }
                }
            }
        }
        
        $call = "CALL GetMentor('$user_id')";
        $q = $conn->query($call);
        $r = $q->fetch_assoc();
        $firstname = $r['FirstName'];
        $lastname = $r['LastName'];
        $email = $r['Email'];
        $maxMentees = $r['MaxNumMentees'];
        mysqli_free_result($r);
        mysqli_next_result($conn);
        
        //create expertise//
        $call = "CALL GetUserExpertise('$user_id')";
        $q = $conn->query($call);
        $r = $q->fetch_assoc();
        $num_expertise_rows = $q->num_rows;
        $expertiseDesc = $r['Details'];
        mysqli_free_result($r);
        mysqli_next_result($conn);
        
        $call = "CALL GetUserSkills('$user_id')";
        $q = $conn->query($call);
        $r = $q->fetch_assoc();
        $num_skills_rows = $q->num_rows;
        $skillDesc = $r['Details'];
        mysqli_free_result($r);
        mysqli_next_result($conn);
        
        
        //get biography//
        $call = "CALL GetUserBiography('$user_id')";
        $q = $conn->query($call);
        $r = $q->fetch_assoc();
        $biography = $r['Details'];
        $biographyEntryId = $r['EntryID'];
        mysqli_free_result($r);
        mysqli_next_result($conn);
        
        
        //create contactInfo//
        $call = "CALL GetUserPrimaryCommunication('$user_id')";
        $q = $conn->query($call);
        $r = $q->fetch_assoc();
        $comDesc = $r['Details'];
        $comEntryId = $r['EntryID'];
        mysqli_free_result($r);
        mysqli_next_result($conn);
        
        //create education
        $call = "CALL GetUserEducation('$user_id')";
        $q = $conn->query($call);
        $r = $q->fetch_assoc();
        $num_education_rows = $q->num_rows;
        $degreeDesc = $r['Details'];
        mysqli_free_result($r);
        mysqli_next_result($conn);
        
        
        //create workExperience
        $call = "CALL GetUserWorkExperience('$user_id')";
        $q = $conn->query($call);
        $r = $q->fetch_assoc();
        $num_jobs_rows = $q->num_rows;
        $jobDesc = $r['Details'];
        mysqli_free_result($r);
        mysqli_next_result($conn);
}
else
{
    echo "<script> location.href='Login.php'; </script>";
    exit;
}
?>
<head>
<script>
//MAIN EDIT FUNCTION
  function enableEditting() {
	document.getElementById("edit_prof_btn").disabled = true;
	document.getElementById("submit_changes_button").disabled = false;
  	document.getElementById("maxMentees").disabled = false;
  	document.getElementById("firstname").disabled = false;
  	document.getElementById("lastname").disabled = false;
	document.getElementById("biography").disabled = false;
	document.getElementById("comDesc").disabled = false;
	document.getElementById("exadd_row").disabled = false;
	document.getElementById("edadd_row").disabled = false;
	document.getElementById("weadd_row").disabled = false;
	document.getElementById("skadd_row").disabled = false;


	var numExp = parseInt(document.getElementById("numExpertise").value);
	for(var x=0;x < numExp; x++)
	{
		if(x != 0)
		{
            document.getElementById("deleteExpertisebtn"+x).disabled = false;
		}		
		document.getElementById("expertiseName"+x).disabled = false;
		document.getElementById("expertiseDesc"+x).disabled = false;
	}

	
	var numSkills = parseInt(document.getElementById("numSkills").value);
	for(var x=0;x < numSkills; x++)
	{
		if(x != 0)
		{
            document.getElementById("deleteSkillsbtn"+x).disabled = false;
		}		
		document.getElementById("skillsName"+x).disabled = false;
		document.getElementById("skillsDesc"+x).disabled = false;
	}
	
	var numJobs = parseInt(document.getElementById("numJobs").value);
	for(var x=0;x < numJobs; x++)
	{
		if(x != 0)
		{
            document.getElementById("deleteJobsbtn"+x).disabled = false;
		}		
		document.getElementById("companyName"+x).disabled = false;
		document.getElementById("jobDesc"+x).disabled = false;
		document.getElementById("jobName"+x).disabled = false;
	}

	var numEducation = parseInt(document.getElementById("numEducation").value);
	for(var x=0;x < numEducation; x++)
	{
		if(x != 0)
		{
            document.getElementById("deleteEducationbtn"+x).disabled = false;
		}		
		document.getElementById("degreeName"+x).disabled = false;
		document.getElementById("degreeDesc"+x).disabled = false;
		document.getElementById("universityName"+x).disabled = false;
	}
  };
 //MAIN SUBMIT CHANGES FUNCTION TO DATABASE 
  function submitChanges() {
	document.getElementById("myForm").submit();
  };

  function deleteExpertiseRow(row)
  {
	 document.getElementById("exaddr"+row).innerHTML = '';
	 var num = parseInt(document.getElementById("numExpertise").value);
	 document.getElementById("numExpertise").value = (num - 1);
	 var lastDeleted = document.getElementById(['lastDeletedExp']).value;
	 if(lastDeleted == -1.25) document.getElementById(['lastDeletedExp']).value = row;
	 else document.getElementById(['lastDeletedExp']).value = lastDeleted+row;
  };

  function deleteSkillsRow(row)
  {
	 document.getElementById("skaddr"+row).innerHTML = '';
	 var num = parseInt(document.getElementById("numSkills").value);
	 document.getElementById("numSkills").value = num - 1;
	 var lastDeleted = document.getElementById(['lastDeletedSk']).value;
	 if(lastDeleted == -1.25) document.getElementById(['lastDeletedSk']).value = row;
	 else document.getElementById(['lastDeletedSk']).value = lastDeleted+row; 
  };

  function deleteWorkExperienceRow(row)
  {
	 document.getElementById("weaddr"+row).innerHTML = '';
	 var num = parseInt(document.getElementById("numJobs").value);
	 document.getElementById("numJobs").value = num - 1;
	 var lastDeleted = document.getElementById(['lastDeletedJob']).value;
	 if(lastDeleted == -1.25) document.getElementById(['lastDeletedJob']).value = row;
	 else document.getElementById(['lastDeletedJob']).value = lastDeleted+row;
  };

  function deleteEducationRow(row)
  {
	 document.getElementById("edaddr"+row).innerHTML = '';
	 var num = parseInt(document.getElementById("numEducation").value);
	 document.getElementById("numEducation").value = num - 1;
	 var lastDeleted = document.getElementById(['lastDeletedEd']).value;
	 if(lastDeleted == -1.25) document.getElementById(['lastDeletedEd']).value = row;
	 else document.getElementById(['lastDeletedEd']).value = lastDeleted+row;
  };
  
	if(window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	};
	
</script>
</head>
<body style="font-size: 16px !important;">
<div id="content">
		<div class="col-md-9" style="width: 100%; max-width: 100% !important;padding: 15px; float:none !important;">
		    <div class="card">
		        <div class="card-body">
		            <div class="row">
		                <div class="col-md-12">
		                    <h4>Your Profile
		                    <button id="edit_prof_btn" type="submit" style="float: right;margin-bottom: 7px;" onclick="enableEditting();">Edit Profile</button>
		                    </h4>
		                </div>
		            </div>
		            <div class="row">
		                <div class="col-md-12">
		                    <form id = "myForm" action="Profile.php" method="post">
		                    
		                    <input type="hidden" id="numExpertise" name="numExpertise" value="<?php echo $num_expertise_rows;?>"/>
		                    <input type="hidden" id="oldnumExpertise" name="oldnumExpertise" value="<?php echo $num_expertise_rows;?>"/>
		                    <input type="hidden" id="lastDeletedExp" name="lastDeletedExp" value="-1.25"/>
		                    <input type="hidden" id="highestExpRow" name="highestExpRow" value="<?php echo $num_expertise_rows;?>"/>
		                    
		                    <input type="hidden" id="numJobs" name="numJobs" value="<?php echo $num_jobs_rows;?>"/>
		                    <input type="hidden" id="oldnumJobs" name="oldnumJobs" value="<?php echo $num_jobs_rows;?>"/>
		                    <input type="hidden" id="lastDeletedJob" name="lastDeletedJob" value="-1.25"/>
		                    <input type="hidden" id="highestJobRow" name="highestJobRow" value="<?php echo $num_jobs_rows;?>"/>
		                    
		                    <input type="hidden" id="numSkills" name="numSkills" value="<?php echo $num_skills_rows;?>"/>
		                    <input type="hidden" id="oldnumSkills" name="oldnumSkills" value="<?php echo $num_skills_rows;?>"/>
		                    <input type="hidden" id="lastDeletedSk" name="lastDeletedSk" value="-1.25"/>
		                    <input type="hidden" id="highestSkRow" name="highestSkRow" value="<?php echo $num_skills_rows;?>"/>
		                    
		                    <input type="hidden" id="numEducation" name="numEducation" value="<?php echo $num_education_rows;?>"/>
		                    <input type="hidden" id="oldnumEducation" name="oldnumEducation" value="<?php echo $num_education_rows;?>"/>
		                    <input type="hidden" id="lastDeletedEd" name="lastDeletedEd" value="-1.25"/>
		                    <input type="hidden" id="highestEdRow" name="highestEdRow" value="<?php echo $num_education_rows;?>"/>
		                    
		                    <input type="hidden" id="oldcomDesc" name="oldcomDesc" value="<?php echo $comDesc;?>"/>
		                    <input type="hidden" id="oldcomEntryId" name="oldcomEntryId" value="<?php echo $comEntryId;?>"/>
		                    <input type="hidden" id="oldbiographyEntryId" name="oldbiographyEntryId" value="<?php echo $biographyEntryId;?>"/>
		                    <input type="hidden" id="oldbiography" name="oldbiography" value="<?php echo $biography;?>"/>
		                    
		                    <input type="hidden" id="oldfirstname" name="oldfirstname" value="<?php echo $firstname;?>"/>
		                    <input type="hidden" id="oldlastname" name="oldlastname" value="<?php echo $lastname;?>"/>
		                    <input type="hidden" id="oldmaxMentees" name="oldmaxMentees" value="<?php echo $maxMentees;?>"/>	
							
		                    <?php 
                                                //populate hidden expertise fields for //
                                                
                                                $call = "CALL GetUserExpertise('$user_id')";
                                                $q = $conn->query($call);
                                                $num_expertise_rows = $q->num_rows;
                                                
                                                
                                                for($x = 0; $x < $num_expertise_rows; $x++){
                                                    $r = $q->fetch_assoc();  
                                                    $oldexpertiseDesc = $r['Details'];
                                                    $oldexpertiseName = $r['ExpertiseName']; 
                                                    $oldexpertiseEntryID = $r['EntryID'];
                                                   
                                                    echo "<input type='hidden' id= 'oldexpertiseName".$x."' name='oldexpertiseName".$x."' value='".$oldexpertiseName."'/>";
                                                    echo "<input type='hidden' id= 'oldexpertiseDesc".$x."' name='oldexpertiseDesc".$x."' value='".$oldexpertiseDesc."'/>";
                                                    echo "<input type='hidden' id= 'oldexpertiseEntryID".$x."' name='oldexpertiseEntryID".$x."' value='".$oldexpertiseEntryID."'/>";
                                                    mysqli_free_result($r);                                               
                                                }
                                                mysqli_next_result($conn);
                                                
                                                //populate hidden Work Experience fields for //
                                                $call = "CALL GetUserWorkExperience('$user_id')";
                                                $q = $conn->query($call);
                                                $num_jobs_rows = $q->num_rows;
                                                
                                                
                                                for($x = 0; $x < $num_jobs_rows; $x++){
                                                    $r = $q->fetch_assoc();
                                                    $oldjobDesc = $r['Details'];
                                                    $oldjobName = $r['JobName'];
                                                    $oldcompanyName = $r['CompanyName'];
                                                    $oldjobEntryID = $r['EntryID'];
                                                    
                                                    echo "<input type='hidden' id= 'oldjobName".$x."' name='oldjobName".$x."' value='".$oldjobName."'/>";
                                                    echo "<input type='hidden' id= 'oldjobDesc".$x."' name='oldjobDesc".$x."' value='".$oldjobDesc."'/>";
                                                    echo "<input type='hidden' id= 'oldjobEntryID".$x."' name='oldjobEntryID".$x."' value='".$oldjobEntryID."'/>";
                                                    echo "<input type='hidden' id= 'oldcompanyName".$x."' name='oldcompanyName".$x."' value='".$oldcompanyName."'/>";
                                                    mysqli_free_result($r);
                                                }
                                                mysqli_next_result($conn);
                                                
                                                //populate hidden Skills fields for //
                                                $call = "CALL GetUserSkills('$user_id')";
                                                $q = $conn->query($call);
                                                $num_skills_rows = $q->num_rows;
                                                
                                                
                                                for($x = 0; $x < $num_skills_rows; $x++){
                                                    $r = $q->fetch_assoc();
                                                    $oldskillsDesc = $r['Details'];
                                                    $oldskillsName = $r['SkillName'];
                                                    $oldskillsEntryID = $r['EntryID'];
                                                    
                                                    echo "<input type='hidden' id= 'oldskillsName".$x."' name='oldskillsName".$x."' value='".$oldskillsName."'/>";
                                                    echo "<input type='hidden' id= 'oldskillsDesc".$x."' name='oldskillsDesc".$x."' value='".$oldskillsDesc."' />";
                                                    echo "<input type='hidden' id= 'oldskillsEntryID".$x."' name='oldskillsEntryID".$x."' value='".$oldskillsEntryID."'/>";
                                                    mysqli_free_result($r);
                                                }
                                                mysqli_next_result($conn);
                                                
                                                //populate hidden Education fields for //
                                                $call = "CALL GetUserEducation('$user_id')";
                                                $q = $conn->query($call);
                                                $num_education_rows = $q->num_rows;
                                                
                                                
                                                for($x = 0; $x < $num_education_rows; $x++){
                                                    $r = $q->fetch_assoc();
                                                    $olddegreeDesc = $r['Details'];
                                                    $olddegreeName = $r['DegreeName'];
                                                    $olddegreeEntryID = $r['EntryID'];
                                                    $olduniversityName = $r['UniversityName'];
                                                    
                                                    echo "<input type='hidden' id= 'olddegreeName".$x."' name='olddegreeName".$x."' value='".$olddegreeName."'/>";
                                                    echo "<input type='hidden' id= 'olddegreeDesc".$x."' name='olddegreeDesc".$x."' value='".$olddegreeDesc."'/>";
                                                    echo "<input type='hidden' id= 'olddegreeEntryID".$x."' name='olddegreeEntryID".$x."' value='".$olddegreeEntryID."'/>";
                                                    echo "<input type='hidden' id= 'olduniversityName".$x."' name='olduniversityName".$x."' value='".$olduniversityName."'/>";
                                                    mysqli_free_result($r);
                                                }
                                                mysqli_next_result($conn);
                                                
                                                ?>
                              <div class="form-group row">
                                <label for="firstname" class="col-4 col-form-label">First Name</label> 
                                <div class="col-8">
                                  <input id="firstname" name="firstname" value="<?php echo $firstname;?>" class="form-control here" type="text"disabled>
                                </div>
                              </div>
                              <div class="form-group row">
                                <label for="lastname" class="col-4 col-form-label">Last Name</label> 
                                <div class="col-8">
                                  <input id="lastname" name="lastname" value="<?php echo $lastname;?>" class="form-control here" type="text"disabled>
                                </div>
                              </div>
                              <div class="form-group row">
                                <label for="email" class="col-4 col-form-label">Email</label> 
                                <div class="col-8">
                                  <input id="email" name="email" value="<?php echo $email;?>" class="form-control here" required="required" type="text"disabled>
                                </div>
                              </div>
                              <div class="form-group row">
                                <label for="Mentee.MaxNumMentees" class="col-4 col-form-label">Maximum amount of Consecutive Mentees</label> 
                                <div class="col-8">
                                  <input id="maxMentees" name="maxMentees" value="<?php echo $maxMentees;?>" class="form-control here" required="required" type="text"disabled>
                                </div>
                              </div> 
                              <div class="form-group row">
                                <label for="publicinfo" class="col-4 col-form-label">Biography</label> 
                                <div class="col-8">
                                  <textarea id="biography" name="biography" cols="40" rows="4" class="form-control" disabled><?php echo $biography;?></textarea>
                                </div>
                              </div>
                              <div class="form-group row">
                                <label for="publicinfo" class="col-4 col-form-label">Communication Methods</label> 
                                <div class="col-8">
                                  <textarea id="comDesc" name="comDesc" cols="40" rows="4" class="form-control" disabled><?php echo $comDesc;?></textarea>
                                </div>
                              </div>
                              
                              
                             
                              <div class="row clearfix">
                        		 <div class="col-md-12 column">
                        			<table class="table table-bordered table-hover" id="expertiseTable" name = "expertiseTable">
                        				<thead>
                        					<tr >
                        						<th class="text-center">
                        							Expertise Name
                        						</th>
                        						<th class="text-center">
                        							Expertise Description
                        						</th>
                        					</tr>
                        				</thead>
                        				<tbody>
                                            <?php 
                                                //populate expertise//
                                                
                                                $call = "CALL GetUserExpertise('$user_id')";
                                                $q = $conn->query($call);
                                                $num_expertise_rows = $q->num_rows;
                                                
                                                
                                                for($x = 0; $x < $num_expertise_rows; $x++){
                                                    $r = $q->fetch_assoc();  
                                                    $expertiseDesc = $r['Details'];
                                                    $expertiseName = $r['ExpertiseName'];
                                                    
                                                    echo "<tr id='exaddr".$x."'>";
                                                    echo "<td>";
                                                    echo "<input type='text' id='expertiseName".$x."' name='expertiseName".$x."' value='".$expertiseName."' class='form-control' cols='2' disabled/>";
                                                    echo "</td>";
                                                    echo "<td>";
                                                    echo "<div class='col-8'>";
                                                    echo "<textarea id='expertiseDesc".$x."' name='expertiseDesc".$x."' cols='40' rows='4' class='form-control' disabled>".$expertiseDesc."</textarea>";
                                                    echo "</div>";
                                                    echo "</td>";
                                                    
                                                    //create scripts for each row to delete it and the button
                                                    if($x != 0)//at least one row always
                                                    {
                                                        echo "<td>";
                                                        echo "<button id='deleteExpertisebtn".$x."' type='button' onclick='deleteExpertiseRow(".$x.")'disabled>Delete Expertise</button>";
                                                        echo "</td>";
                                                    }
                                                                                                       
                                                    echo "</tr>";
                                                    
                                                    mysqli_free_result($r);                                               
                                                }
                                                mysqli_next_result($conn);
                                                ?>
                        				</tbody>
                        			</table>
                        		</div>
                        	</div>
                        	<button id= "exadd_row" type="button" style="float: right;" disabled>Add Expertise</button>
                        	<br>
                        	<br>
                        	<br>
                        	<br>
                              
                              
                              
                             
                               <div class="row clearfix">
                        		 <div class="col-md-12 column">
                        			<table class="table table-bordered table-hover" id="skillTable" name = "skillTable">
                        				<thead>
                        					<tr >
                        						<th class="text-center">
                        							Skill Name
                        						</th>
                        						<th class="text-center">
                        							Skill Description
                        						</th>
                        					</tr>
                        				</thead>
                        				<tbody>
                        					<?php 
                                                //populate expertise//
                                                
                                                $call = "CALL GetUserSkills('$user_id')";
                                                $q = $conn->query($call);
                                                $num_skills_rows = $q->num_rows;
                                                
                                                
                                                for($x = 0; $x < $num_skills_rows; $x++){
                                                    $r = $q->fetch_assoc();  
                                                    $skillsDesc = $r['Details'];
                                                    $skillsName = $r['SkillName'];
                                                    
                                                    echo "<tr id=skaddr".$x.">";
                                                    echo "<td>";
                                                    echo "<input type='text' id='skillsName".$x."' name='skillsName".$x."' value='".$skillsName."' class='form-control' cols='2' disabled/>";
                                                    echo "</td>";
                                                    echo "<td>";
                                                    echo "<div class='col-8'>";
                                                    echo "<textarea id='skillsDesc".$x."' name='skillsDesc".$x."' cols='40' rows='4' class='form-control' disabled>".$skillsDesc."</textarea>";
                                                    echo "</div>";
                                                    echo "</td>";
                                                    
                                                    //create scripts for each row to delete it and the button
                                                    if($x != 0)//at least one row always
                                                    {
                                                        echo "<td>";
                                                        echo "<button id='deleteSkillsbtn".$x."' type='button' onclick='deleteSkillsRow(".$x.")'disabled>Delete Skill</button>";
                                                        echo "</td>";
                                                    }
                                                    
                                                    echo "</tr>";
                                                    
                                                    mysqli_free_result($r);                                               
                                                }
                                                mysqli_next_result($conn);
                                                ?>
                        				</tbody>
                        			</table>
                        		</div>
                        	</div>
                        	<button id= "skadd_row" type="button" style="float: right;" disabled>Add Skill</button>
                        	<br>
                        	<br>
                            <br>
                        	<br>
                              
                              
                              
                              
                              <div class="row clearfix">
                        		 <div class="col-md-12 column">
                        			<table class="table table-bordered table-hover" id="educationTable" name = "educationTable">
                        				<thead>
                        					<tr >
                        					<th class="text-center">
                        							Degree
                        						</th>
                        						<th class="text-center">
                        							University
                        						</th>
                        						<th class="text-center">
                        							Description
                        						</th>
                        					</tr>
                        				</thead>
                        				<tbody>
                        					<?php 
                                                //populate education//
                                                
                                                $call = "CALL GetUserEducation('$user_id')";
                                                $q = $conn->query($call);
                                                $num_education_rows = $q->num_rows;
                                                
                                                
                                                for($x = 0; $x < $num_education_rows; $x++){
                                                    $r = $q->fetch_assoc();  
                                                    $degreeDesc = $r['Details'];
                                                    $universityName = $r['UniversityName'];
                                                    $degreeName = $r['DegreeName'];
                                                    
                                                    echo "<tr id=edaddr".$x.">";
                                                    echo "<td>";
                                                    echo "<input type='text' id='degreeName".$x."' name='degreeName".$x."' value='".$degreeName."' class='form-control' cols='2' disabled/>";
                                                    echo "</td>";
                                                    echo "<td>";
                                                    echo "<input type='text' id='universityName".$x."' name='universityName".$x."' value='".$universityName."' class='form-control' cols='2' disabled/>";
                                                    echo "</td>";
                                                    echo "<td>";
                                                    echo "<div class='col-8'>";
                                                    echo "<textarea id='degreeDesc".$x."' name='degreeDesc".$x."' cols='40' rows='4' class='form-control' disabled>".$degreeDesc."</textarea>";
                                                    echo "</div>";
                                                    echo "</td>";
                                                    
                                                    //create scripts for each row to delete it and the button
                                                    if($x != 0)//at least one row always
                                                    {
                                                        echo "<td>";
                                                        echo "<button id='deleteEducationbtn".$x."' type='button' onclick='deleteEducationRow(".$x.")'disabled>Delete Education</button>";
                                                        echo "</td>";
                                                    }
                                                    
                                                    echo "</tr>";
                                                    
                                                    mysqli_free_result($r);                                               
                                                }
                                                mysqli_next_result($conn);
                                                ?>
                        				</tbody>
                        			</table>
                        		</div>
                        	</div>
                        	<button id= "edadd_row" type="button" style="float: right;" disabled>Add Education</button>
                        	<br>
                        	<br>
                            <br>
                        	<br>
                              
                              
                              
                              <div class="row clearfix">
                        		 <div class="col-md-12 column">
                        			<table class="table table-bordered table-hover" id="jobTable" name = "jobTable">
                        				<thead>
                        					<tr >
                        					<th class="text-center">
                        							Company 
                        						</th>
                        						<th class="text-center">
                        							Job Title
                        						</th>
                        						<th class="text-center">
                        							Description
                        						</th>
                        					</tr>
                        				</thead>
                        				<tbody>
                        					<?php 
                                                //populate work experience//
                                                
                                                $call = "CALL GetUserWorkExperience('$user_id')";
                                                $q = $conn->query($call);
                                                $num_jobs_rows = $q->num_rows;
                                                
                                                
                                                for($x = 0; $x < $num_jobs_rows; $x++){
                                                    $r = $q->fetch_assoc();  
                                                    $jobDesc = $r['Details'];
                                                    $companyName = $r['CompanyName'];
                                                    $jobName = $r['JobName'];
                                                    
                                                    echo "<tr id=weaddr".$x.">";
                                                    echo "<td>";
                                                    echo "<input type='text' id='companyName".$x."' name='companyName".$x."' value='".$companyName."' class='form-control' cols='2' disabled/>";
                                                    echo "</td>";
                                                    echo "<td>";
                                                    echo "<input type='text' id='jobName".$x."' name='jobName".$x."' value='".$jobName."' class='form-control' cols='2' disabled/>";
                                                    echo "</td>";
                                                    echo "<td>";
                                                    echo "<div class='col-8'>";
                                                    echo "<textarea id='jobDesc".$x."' name='jobDesc".$x."' cols='40' rows='4' class='form-control' disabled>".$jobDesc."</textarea>";
                                                    echo "</div>";
                                                    echo "</td>";
                                                    
                                                    //create scripts for each row to delete it and the button
                                                    if($x != 0)//at least one row always
                                                    {
                                                        echo "<td>";
                                                        echo "<button id='deleteJobsbtn".$x."' type='button' onclick='deleteWorkExperienceRow(".$x.")'disabled>Delete Work Experience</button>";
                                                        echo "</td>";
                                                    }
                                                    
                                                    echo "</tr>";
                                                    
                                                    mysqli_free_result($r);                                               
                                                }
                                                mysqli_next_result($conn);
                                                ?>
                        				</tbody>
                        			</table>
                        		</div>
                        	</div>
                        	<button id= "weadd_row" type="button" style="float: right;" disabled>Add Job</button>
                        	<br>
                        	<br>
                        	<br>
                        	<br>
                              
                              
                              <button id= "submit_changes_button" type="submit" style="float: right;" onclick="submitChanges();" disabled>Submit Changes</button>
                              
                              <p name="testText" style="color: #FF0000;"><?php echo $message?></p>
                            </form>
		                </div>
		           </div>		            
		        </div>
		   </div> 
	</div>
<script type="text/javascript">

$(document).ready(function(){

    //expertise------------------------------- works
   $("#exadd_row").click(function(){
	   if(parseFloat(document.getElementById(['lastDeletedExp']).value) == -1.25)//nothing deleted
	   {
			var i = parseInt(document.getElementById("numExpertise").value);
	   }
	   else//something deleted
	   {
		   var lastDeletedExp = parseInt(document.getElementById(['lastDeletedExp']).value);
		   var lastDeletednum = lastDeletedExp % 10;
		   var lastDeleted = parseInt(lastDeletedExp/10);	   
           if(lastDeleted == 0)
           {
               var i = lastDeletedExp;
               document.getElementById(['lastDeletedExp']).value = -1.25;
               
           }
           else 
           {
        	   var i = lastDeletednum;
               document.getElementById(['lastDeletedExp']).value = lastDeleted;	
           }   
	   }
	   var highestRow = parseInt(document.getElementById(['highestExpRow']).value);
	   if(highestRow <= i)
	   {
		   document.getElementById(['highestExpRow']).value = (i+1);
	   }
	   
	   if(document.getElementById["exaddr"+i] == null)
	   {
            $('#expertiseTable').append('<tr id="exaddr'+i+'"></tr>');
	   }
            $('#exaddr'+i).html("<td><input type='text' id='expertiseName"+i+"'name='expertiseName"+i+"' value='Expertise Name' class='form-control' cols='2'/></td><td><div class='col-8'><textarea id='expertiseDesc"+i+"'name='expertiseDesc"+i+"' cols='40' rows='4' class='form-control' >EXAMPLE TEXT HERE"+i+"</textarea></div></td><td><button id='deleteExpertisebtn"+i+"' type='button' onclick='deleteExpertiseRow("+i+")'>Delete Expertise</button></td>");    							             
 	       document.getElementById("numExpertise").value = (parseInt(document.getElementById("numExpertise").value)+1);
 	       i++;
	});	

   //education--------------------------- works
   $("#edadd_row").click(function(){
           if(parseFloat(document.getElementById(['lastDeletedEd']).value) == -1.25)//nothing deleted
    	   {
        	   var a = parseInt(document.getElementById("numEducation").value);
    	   }
    	   else//something deleted
    	   {
    		   var lastDeletedExp = parseInt(document.getElementById(['lastDeletedEd']).value);
    		   var lastDeletednum = lastDeletedExp % 10;
    		   var lastDeleted = parseInt(lastDeletedExp/10);	   
               if(lastDeleted == 0)
               {
                   var a = lastDeletedExp;
                   document.getElementById(['lastDeletedEd']).value = -1.25;                  
               }
               else 
               {
            	   var a = lastDeletednum;
                   document.getElementById(['lastDeletedEd']).value = lastDeleted;	
               }   
    	   }
    	   var highestRow = parseInt(document.getElementById(['highestEdRow']).value);
    	   if(highestRow <= a)
    	   {
    		   document.getElementById(['highestEdRow']).value = (a+1);
    	   }
    	   
    	   if(document.getElementById["edaddr"+a] == null)
    	   {
    		   $('#educationTable').append('<tr id="edaddr'+a+'"></tr>');
    	   }
     	      $('#edaddr'+a).html("<td><input type='text' id='degreeName"+a+"' name='degreeName"+a+"' value='Bachelors CompScie' class='form-control' cols='2' required/></td><td><input type='text' id='universityName"+a+"' name='universityName"+a+"' value='EWU' class='form-control' cols='2' required/></td><td><div class='col-8'><textarea id='degreeDesc"+a+"'name='degreeDesc"+a+"' cols='40' rows='4' class='form-control' required>EXAMPLE TEXT HERE</textarea></div></td><td><button id='deleteEducationbtn"+a+"' type='button' onclick='deleteEducationRow("+a+")'>Delete Education</button></td>");    							            
              document.getElementById("numEducation").value = (parseInt(document.getElementById("numEducation").value)+1);
              a++;
	});
	
  //work experience-----------------------------------
  $("#weadd_row").click(function(){
       if(parseFloat(document.getElementById(['lastDeletedJob']).value) == -1.25)//nothing deleted
   	   {
        	  var b = parseInt(document.getElementById("numJobs").value);
   	   }
   	   else//something deleted
   	   {
   		   var lastDeletedExp = parseInt(document.getElementById(['lastDeletedJob']).value);
   		   var lastDeletednum = lastDeletedExp % 10;
   		   var lastDeleted = parseInt(lastDeletedExp/10);	   
              if(lastDeleted == 0)
              {
                  var b = lastDeletedExp;
                  document.getElementById(['lastDeletedJob']).value = -1.25;                  
              }
              else 
              {
           	   var b = lastDeletednum;
                  document.getElementById(['lastDeletedJob']).value = lastDeleted;	
              }   
   	   }
   	   var highestRow = parseInt(document.getElementById(['highestJobRow']).value);
   	   if(highestRow <= b)
   	   {
   		   document.getElementById(['highestJobRow']).value = (b+1);
   	   }
   	   
   	   if(document.getElementById["weaddr"+b] == null)
   	   {
   		$('#jobTable').append('<tr id="weaddr'+b+'"></tr>');
   	   }
             $('#weaddr'+b).html("<td><input type='text' id='companyName"+b+"' name='companyName"+b+"' value='Google' class='form-control' cols='2' required/></td><td><input type='text' id='jobName"+b+"' name='jobName"+b+"' value='Project manager' class='form-control' cols='2' required/></td><td><div class='col-8'><textarea id='jobDesc"+b+"' name='jobDesc"+b+"' cols='40' rows='4' class='form-control' required>EXAMPLE TEXT HERE</textarea></div></td><td><button id='deleteJobsbtn"+b+"' type='button' onclick='deleteWorkExperienceRow("+b+")'>Delete Work Experience</button></td>");    							          
             document.getElementById("numJobs").value = (parseInt(document.getElementById("numJobs").value)+1);
             b++;
	});
	
 //Skills--------------------------------------------------------
 $("#skadd_row").click(function(){
        if(parseFloat(document.getElementById(['lastDeletedSk']).value) == -1.25)//nothing deleted
    	   {
        		var c = parseInt(document.getElementById("numSkills").value);
    	   }
    	   else//something deleted
    	   {
    		   var lastDeletedExp = parseInt(document.getElementById(['lastDeletedSk']).value);
    		   var lastDeletednum = lastDeletedExp % 10;
    		   var lastDeleted = parseInt(lastDeletedExp/10);	   
               if(lastDeleted == 0)
               {
                   var c = lastDeletedExp;
                   document.getElementById(['lastDeletedSk']).value = -1.25;                  
               }
               else 
               {
            	   var c = lastDeletednum;
                   document.getElementById(['lastDeletedSk']).value = lastDeleted;	
               }   
    	   }
    	   var highestRow = parseInt(document.getElementById(['highestSkRow']).value);
    	   if(highestRow <= c)
    	   {
    		   document.getElementById(['highestSkRow']).value = (c+1);
    	   }
    	   
    	   if(document.getElementById["skaddr"+c] == null)
    	   {
    		   $('#skillTable').append('<tr id="skaddr'+c+'"></tr>'); 
    	   }
              $('#skaddr'+c).html("<td><input type='text' id='skillsName"+c+"' name='skillsName"+c+"' value='Skill Name' class='form-control' cols='2'required/></td><td><div class='col-8'><textarea id='skillsDesc"+c+"' name='skillsDesc"+c+"' cols='40' rows='4' class='form-control' required>EXAMPLE TEXT HERE</textarea></div></td><td><button id='deleteSkillsbtn"+c+"' type='button' onclick='deleteSkillsRow("+c+")'>Delete Skill</button></td>");    							         
              document.getElementById("numSkills").value = (parseInt(document.getElementById("numSkills").value)+1);
              c++; 
	});

});
</script>
</div>
</body>
<?php
include 'includes/footer.php';
?>
</html>
