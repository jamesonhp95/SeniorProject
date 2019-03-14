<html>
<?php
session_start();
require_once("../internalIncludes/dbconfig.php");
include 'includes/pageOverlay.php';
include 'includes/header.php';
include 'includes/site-header-loader.php';
include 'includes/PopUpStyle.php';

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
  
  if($IsMentor != 0)
  {
	echo "<script> location.href='MenteeLogin.php'; </script>";
    exit;
  }
}
else
{
	echo "<script> location.href='MenteeLogin.php'; </script>";
    exit;
}

if(isset($_SESSION['IsMentor']) && $_SESSION['IsMentor'] == 0) 
{
    if(isset($_SESSION['view_user_id']))//if the view-user is set
    {
    	$user_id = $_SESSION['view_user_id'];
		$logged_in_id = $_SESSION['user_id'];
    	$message= "user id is set in the session";
               
        $call = "CALL GetMentor('$user_id');";
        $q = $conn->query($call);
        $r = $q->fetch_assoc();
        $firstname = $r['FirstName'];
        $lastname = $r['LastName'];
        $email = $r['Email'];
        mysqli_free_result($r);
        mysqli_next_result($conn);
        
        //create expertise//
        $call = "CALL GetUserExpertise('$user_id');";
        $q = $conn->query($call);
        $r = $q->fetch_assoc();
        $expertiseDesc = $r['Details'];
        mysqli_free_result($r);
        mysqli_next_result($conn);
        
        $call = "CALL GetUserSkills('$user_id');";
        $q = $conn->query($call);
        $r = $q->fetch_assoc();
        $skillDesc = $r['Details'];
        mysqli_free_result($r);
        mysqli_next_result($conn);
        
        
        //get biography//
        $call = "CALL GetUserBiography('$user_id');";
        $q = $conn->query($call);
        $r = $q->fetch_assoc();
        $biography = $r['Details'];
        mysqli_free_result($r);
        mysqli_next_result($conn);
        
        
        //create contactInfo//
        $call = "CALL GetUserPrimaryCommunication('$user_id');";
        $q = $conn->query($call);
        $r = $q->fetch_assoc();
        $comDesc = $r['Details'];
        mysqli_free_result($r);
        mysqli_next_result($conn);      
        
        //create education
        $call = "CALL GetUserEducation('$user_id');";
        $q = $conn->query($call);
        $r = $q->fetch_assoc();
        $degreeDesc = $r['Details'];
        mysqli_free_result($r);
        mysqli_next_result($conn);
        
        
        //create workExperience
        $call = "CALL GetUserWorkExperience('$user_id');";
        $q = $conn->query($call);
        $r = $q->fetch_assoc();
        $jobDesc = $r['Details'];
        mysqli_free_result($r);
		mysqli_next_result($conn);
		
		//check if conversation exists
		$call = "CALL GetConversationExists('$logged_in_id', '$user_id');";
		$q = $conn->query($call);
		$r = $q->fetch_assoc();
		$c_id = $r['ConversationID'];
		mysqli_free_result($r);
		mysqli_next_result($conn);
		$isDisabled = "";
		$errorMessage = "";
		if($c_id != -1) {
			$isDisabled = "disabled";
			$call = "CALL GetConversationStatus('$c_id');";
			$q = $conn->query($call);
			$r = $q->fetch_assoc();
			$isLocked = $r['status'];
			mysqli_free_result($r);
			mysqli_next_result($conn);
			if($isLocked == 0) {
				$errorMessage = "<br/>It looks like you already have an open conversation with this mentor, please go to conversations to continue chatting!<br/>";
			}
			else if($isLocked == 1) {
				$errorMessage = "<br/>It looks like the this mentor is currently pending availability, please wait until they have an opening for a mentorship.<br/>";
			}
			else if($isLocked == 2) {
				$errorMessage = "<br/>It looks like this mentor hasn't checked your previous message, please wait until you hear back from them. Meanwhile, head over to the Search For Mentors tab to find more mentors!<br/>";
			}
			else {
				$isDisabled = "";
			}
		}
    }
    else//view user is not set
    {
	echo "<script> location.href='Search.php'; </script>";
	exit;
    }
}
else//user is not logged in/is not a mentee
{
  echo "<script> location.href='MenteeLogin.php'; </script>";
  exit;
}
?>
<head>
  <script>
    function makePopUp() {
      var xmlhttp = new XMLHttpRequest();
	  var textPrompt = "Hello, I am a Computer Science major looking for advise on interviews! Will you become my mentor?";
	  var notO = -1;
      xmlhttp.onreadystatechange = function() {
        if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
          document.getElementById("PopUp").innerHTML = this.responseText;
		}
      };
      xmlhttp.open("GET", "popupMaker.php?q="+textPrompt+"&o="+notO, true);
      xmlhttp.send();
    };
	
    function sendPopUpMessage() {
	  var prevStr = document.getElementById("temp_msg").value;
		var str = prevStr.replace(/&/g, "%26amp;").replace(/>/g, "%26gt;").replace(/</g, "%26lt;").replace(/\+/g, "%2B").replace(/#/gi, "%23");
		prevStr = str;
		var str = prevStr.replace(/\n/g, '<br/>');
	  if(str != "") {
		  var xmlhttp = new XMLHttpRequest();
		  xmlhttp.onreadystatechange = function() {
		  if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			document.getElementById("PopUp").innerHTML = this.responseText;
		  }
		  };
		  xmlhttp.open("GET", "sendPopUpMessage.php?q=" +str, true);
		  xmlhttp.send();
	  }
	  else {
		document.getElementById("PopUp").innerHTML = "";
	  }
    };
	
	function cancelMessage() {
		document.getElementById("PopUp").innerHTML = "";
	};
  </script>
</head>
<body>
<div id="content">
		<div class="col-md-9" style="width: 100%; max-width: 100% !important; padding: 15px; float: none;">
		    <div class="card">
		        <div class="card-body">
		            <div class="row">
		                <div class="col-md-12">
				<h4><?php echo $firstname." ".$lastname;?><button type="submit" style="float: right" onclick="makePopUp();" <?php echo $isDisabled; ?>>Start Conversation</button><p/><div id="PopUp" style="margin:0 auto;"><span id="statusResponse"><?php echo $errorMessage; ?></span></div></h4>
		                    <hr>
		                </div>
		            </div>
		            <div class="row">
		                <div class="col-md-12">
		                    <form action="Profile.php" method="post">
			      <div class="form-group row">
   				<label for="email" class="col-4 col-form-label">Email</label> 
                                <div class="col-8">
                                  <input id="email" name="email" placeholder="<?php echo $email;?>" class="form-control here" required="required" type="text"disabled>
                                </div>
                              </div>
                              <div class="form-group row">
                                <label for="publicinfo" class="col-4 col-form-label">Biography</label> 
                                <div class="col-8">
                                  <textarea id="biography" name="biography" cols="40" rows="4" class="form-control" placeholder="<?php echo $biography;?>"disabled></textarea>
                                </div>
                              </div>
                              <div class="form-group row">
                                <label for="publicinfo" class="col-4 col-form-label">Communication Methods</label> 
                                <div class="col-8">
                                  <textarea id="comDesc" name="comDesc" cols="40" rows="4" class="form-control" placeholder="<?php echo $comDesc;?>"disabled></textarea>
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
                                                    
                                                    echo "<tr id=exaddr".$x."></tr>";
                                                    echo "<td>";
                                                    echo "<input type='text' name='expertiseName".$x."' placeholder='".$expertiseName."' class='form-control' cols='2' disabled/>";
                                                    echo "</td>";
                                                    echo "<td>";
                                                    echo "<div class='col-8'>";
                                                    echo "<textarea name='expertiseDesc".$x."' cols='40' rows='4' class='form-control' placeholder='".$expertiseDesc."'disabled></textarea>";
                                                    echo "</div>";
                                                    echo "</tr>";
                                                    
                                                    mysqli_free_result($r);                                               
                                                }
                                                mysqli_next_result($conn);
                                                ?>
                        				</tbody>
                        			</table>
                        		</div>
                        	</div>
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
                                                    
                                                    echo "<tr id=skaddr".$x."></tr>";
                                                    echo "<td>";
                                                    echo "<input type='text' name='skillsName".$x."' placeholder='".$skillsName."' class='form-control' cols='2' disabled/>";
                                                    echo "</td>";
                                                    echo "<td>";
                                                    echo "<div class='col-8'>";
                                                    echo "<textarea name='skillsDesc".$x."' cols='40' rows='4' class='form-control' placeholder='".$skillsDesc."'disabled></textarea>";
                                                    echo "</div>";
                                                    echo "</tr>";
                                                    
                                                    mysqli_free_result($r);                                               
                                                }
                                                mysqli_next_result($conn);
                                                ?>
                        				</tbody>
                        			</table>
                        		</div>
                        	</div>
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
                                                    
                                                    echo "<tr id=edaddr".$x."></tr>";
                                                    echo "<td>";
                                                    echo "<input type='text' name='degreeName".$x."' placeholder='".$degreeName."' class='form-control' cols='2' disabled/>";
                                                    echo "</td>";
                                                    echo "<td>";
                                                    echo "<input type='text' name='universityName".$x."' placeholder='".$universityName."' class='form-control' cols='2' disabled/>";
                                                    echo "</td>";
                                                    echo "<td>";
                                                    echo "<div class='col-8'>";
                                                    echo "<textarea name='degreeDesc".$x."' cols='40' rows='4' class='form-control' placeholder='".$degreeDesc."'disabled></textarea>";
                                                    echo "</div>";
                                                    echo "</tr>";
                                                    
                                                    mysqli_free_result($r);                                               
                                                }
                                                mysqli_next_result($conn);
                                                ?>
                        				</tbody>
                        			</table>
                        		</div>
                        	</div>
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
                                                    
                                                    echo "<tr id=weaddr".$x."></tr>";
                                                    echo "<td>";
                                                    echo "<input type='text' name='companyName".$x."' placeholder='".$companyName."' class='form-control' cols='2' disabled/>";
                                                    echo "</td>";
                                                    echo "<td>";
                                                    echo "<input type='text' name='jobName".$x."' placeholder='".$jobName."' class='form-control' cols='2' disabled/>";
                                                    echo "</td>";
                                                    echo "<td>";
                                                    echo "<div class='col-8'>";
                                                    echo "<textarea name='jobDesc".$x."' cols='40' rows='4' class='form-control' placeholder='".$jobDesc."'disabled></textarea>";
                                                    echo "</div>";
                                                    echo "</tr>";
                                                    
                                                    mysqli_free_result($r);                                               
                                                }
                                                mysqli_next_result($conn);
                                                ?>
                        				</tbody>
                        			</table>
                        		</div>
                        	</div>
                        	<br>
                        	<br>
                        	<br>
                            
                            </form>
		                </div>
		            </div>		            
		        </div>
		    </div>
	</div>
</div>
</body>
<?php
include 'includes/footer.php';
?>
</html>
