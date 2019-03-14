<html>
<?php
session_start();
#ini_set('display_errors','on');
#error_reporting(E_ALL);
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

if(isset($_POST['email'])){
  //Arrays to search for html injection.
	$searched_html_characters = array("&", ">", "<", "+", "#");
	$replacement_html_equivalents = array("%26amp", "%26gt", "%26lt", "%2B", "%23");
  $email = $_POST['email'];
  //$email = str_replace($searched_html_characters, $replacement_html_equivalents, $email);
  $email = mysqli_real_escape_string($conn, $email);
  $message= "Passwords did not match, please try again.";
  $password = $_POST['password'];
  $password_repeat = $_POST['password_repeat'];
  $companyName ="Google";
  $jobTitle = "Project Manager";
  $jobDesc = $_POST['jobDesc'];
  $jobDesc = str_replace("'", "&apos", $jobDesc);
  $jobDesc = str_replace('"', "&quot", $jobDesc);
  //$jobDesc = str_replace($searched_html_characters, $replacement_html_equivalents, $jobDesc);
  $jobDesc = mysqli_real_escape_string($conn, $jobDesc);
  $skillName = "Public Speaking";
  $skillDesc = $_POST['skillDesc'];
  $skillDesc = str_replace("'", "&apos", $skillDesc);
  $skillDesc = str_replace('"', "&quot", $skillDesc);
  //$skillDesc = str_replace($searched_html_characters, $replacement_html_equivalents, $skillDesc);
  $skillDesc = mysqli_real_escape_string($conn, $skillDesc);
  $university = "Eastern Washington University";
  $degreeName = "Bachelors in Computer Science";
  $degreeDesc = $_POST['degreeDesc'];
  $degreeDesc = str_replace("'", "&apos", $degreeDesc);
  $degreeDesc = str_replace('"', "&quot", $degreeDesc);
  //$degreeDesc = str_replace($searched_html_characters, $replacement_html_equivalents, $degreeDesc);
  $degreeDesc = mysqli_real_escape_string($conn, $degreeDesc);
  $expertiseName = "Algorithms";
  $expertiseDesc = $_POST['expertiseDesc'];
  $expertiseDesc = str_replace("'", "&apos", $expertiseDesc);
  $expertiseDesc = str_replace('"', "&quot", $expertiseDesc);
  //$expertiseDesc = str_replace($searched_html_characters, $replacement_html_equivalents, $expertiseDesc);
  $expertiseDesc = mysqli_real_escape_string($conn, $expertiseDesc);
  $comMethod = "Phone/Text";
  $comDesc = $_POST['comDesc'];
  $comDesc = str_replace("'", "&apos", $comDesc);
  $comDesc = str_replace('"', "&quot", $comDesc);
  //$comDesc = str_replace($searched_html_characters, $replacement_html_equivalents, $comDesc);
  $comDesc = mysqli_real_escape_string($conn, $comDesc);
  $biography = $_POST['biography'];
  $biography = str_replace("'", "&apos", $biography);
  $biography = str_replace('"', "&quot", $biography);
  //$biography = str_replace($searched_html_characters, $replacement_html_equivalents, $biography);
  $biography = mysqli_real_escape_string($conn, $biography);
  $maxMentees = intval($_POST['maxMentees']);
  if($maxMentees < 1){$maxMentees = 1;}
  if($maxMentees > 20000){$maxMentees = 20000;}
  
  if(strcmp($password, $password_repeat) == 0) {
      $message= "You successfully created a mentor with username: $email. Go to the login page to begin!";
      //$email = $_POST['email']; Unecessary
	  $firstname = $_POST['firstname'];
	  $firstname = str_replace("'", "&apos", $firstname);
	  $firstname = str_replace('"', "&quot", $firstname);
	  //$firstname = str_replace($searched_html_characters, $replacement_html_equivalents, $firstname);
	  $firstname = mysqli_real_escape_string($conn, $firstname);
	  $lastname = $_POST['lastname'];
	  $lastname = str_replace("'", "&apos", $lastname);
	  $lastname = str_replace('"', "&quot", $lastname);
	  //$lastname = str_replace($searched_html_characters, $replacement_html_equivalents, $lastname);
	  $lastname = mysqli_real_escape_string($conn, $lastname);
      $call = "CALL CreateMentor('$firstname', '$lastname', '$email', '$maxMentees');";
      $q = $conn->query($call);
      $r = $q->fetch_assoc();
      $user_id = $r['UserID'];
      mysqli_free_result($q);
      mysqli_next_result($conn);
      
    /*TEST AREA TEST*/
    if($user_id == 0) {
          $message = "An account with that username already exists, please try logging in instead.";
    }
    else {
      $pass_hash = password_hash($password, PASSWORD_DEFAULT);
      $sql = "CALL ChangeUserPassword('$user_id', '$pass_hash')";
      $query = $conn->query($sql);
      $res = $query->fetch_assoc();
      $success = $res['Confirmation'];
      mysqli_free_result($res);
      mysqli_next_result($conn);
      if($success != 0) {
        $message = "An error occurred when creating your account. Please try again.";
      }
      else 
      {
        $_POST['user_id'] = $user_id;
        
        //create expertise//
        for($x = 0; $x < $_POST['numExpertise']; $x++){
	      $expertiseName = $_POST['expertiseName'.$x];
		  $expertiseName = str_replace("'", "&apos", $expertiseName);
		  $expertiseName = str_replace('"', "&quot", $expertiseName);
	      //$expertiseName = str_replace($searched_html_characters, $replacement_html_equivalents, $expertiseName);
	      $expertiseName = mysqli_real_escape_string($conn, $expertiseName);
	      $expertiseDesc = $_POST['expertiseDesc'.$x];
		  $expertiseDesc = str_replace("'", "&apos", $expertiseDesc);
		  $expertiseDesc = str_replace('"', "&quot", $expertiseDesc);
	      //$expertiseDesc = str_replace($searched_html_characters, $replacement_html_equivalents, $expertiseDesc);
	      $expertiseDesc = mysqli_real_escape_string($conn, $expertiseDesc);
          if(strcmp($expertiseName, "") != 0 && strcmp($expertiseDesc, "") != 0) {
            $call = "CALL SetUserExpertise('$user_id', '$expertiseName', '$expertiseDesc');";
            $q = $conn->query($call);
            $r = $q->fetch_assoc();
            mysqli_free_result($r);
            mysqli_next_result($conn);
          }
        }
        
        for($b = 0; $b < $_POST['numSkills']; $b++){
	      $skillName = $_POST['skillName'.$b];
		  $skillName = str_replace("'", "&apos", $skillName);
		  $skillName = str_replace('"', "&quot", $skillName);
	      //$skillName = str_replace($searched_html_characters, $replacement_html_equivalents, $skillName);
	      $skillName = mysqli_real_escape_string($conn, $skillName);
	      $skillDesc = $_POST['skillDesc'.$b];
		  $skillDesc = str_replace("'", "&apos", $skillDesc);
		  $skillDesc = str_replace('"', "&quot", $skillDesc);
	      //$skillDesc = str_replace($searched_html_characters, $replacement_html_equivalents, $skillDesc);
	      $skillDesc = mysqli_real_escape_string($conn, $skillDesc);
          if(strcmp($skillName, "") != 0 && strcmp($skillDesc, "") != 0) {
            $call = "CALL SetUserSkill('$user_id', '$skillName', '$skillDesc');";
            $q = $conn->query($call);
            $r = $q->fetch_assoc();
            mysqli_free_result($r);
            mysqli_next_result($conn);
          }
          
        }
          
        
        
        //create biography//
        $call = "CALL SetUserBiography('$user_id', '$biography');";
        $q = $conn->query($call);
        $r = $q->fetch_assoc();
        mysqli_free_result($r);
        mysqli_next_result($conn);
        
        
        //create contactInfo//
        $call = "CALL SetUserPrimaryCommunication('$user_id','$comMethod', '$comDesc');";
        $q = $conn->query($call);
        $r = $q->fetch_assoc();
        mysqli_free_result($r);
        mysqli_next_result($conn);
        
        $call = "CALL SetUserAvailability('$user_id','$comMethod', '$comDesc');";
        $q = $conn->query($call);
        $r = $q->fetch_assoc();
        mysqli_free_result($r);
        mysqli_next_result($conn);
        
        
        //create education
        for($l = 0; $l < $_POST['numEducation']; $l++)
        {
	      $degreeName = $_POST['degreeName'.$l];
		  $degreeName = str_replace("'", "&apos", $degreeName);
		  $degreeName = str_replace('"', "&quot", $degreeName);
	      //$degreeName = str_replace($searched_html_characters, $replacement_html_equivalents, $degreeName);
	      $degreeName = mysqli_real_escape_string($conn, $degreeName);
	      $university = $_POST['university'.$l];
		  $university = str_replace("'", "&apos", $university);
		  $university = str_replace('"', "&quot", $university);
	      //$university = str_replace($searched_html_characters, $replacement_html_equivalents, $university);
	      $university = mysqli_real_escape_string($conn, $university);
	      $degreeDesc = $_POST['degreeDesc'.$l];
		  $degreeDesc = str_replace("'", "&apos", $degreeDesc);
		  $degreeDesc = str_replace('"', "&quot", $degreeDesc);
	      //$degreeDesc = str_replace($searched_html_characters, $replacement_html_equivalents, $degreeDesc);
	      $degreeDesc = mysqli_real_escape_string($conn, $degreeDesc);
          if(strcmp($degreeName, "") != 0 && strcmp($university, "") != 0 && strcmp($degreeDesc, "") != 0) {
            $call = "CALL SetUserEducation('$user_id', '$degreeName', '$university','$degreeDesc');";
            $q = $conn->query($call);
            $r = $q->fetch_assoc();
            mysqli_free_result($r);
            mysqli_next_result($conn);        
          }				
        }
        
        
        //create workExperience
        for($m = 0; $m < $_POST['numJobs']; $m++)
        {
	      $companyName = $_POST['companyName'.$m];
		  $companyName = str_replace("'", "&apos", $companyName);
		  $companyName = str_replace('"', "&quot", $companyName);
	      //$companyName = str_replace($searched_html_characters, $replacement_html_equivalents, $companyName);
	      $companyName = mysqli_real_escape_string($conn, $companyName);
	      $jobName = $_POST['jobName'.$m];
		  $jobName = str_replace("'", "&apos", $jobName);
		  $jobName = str_replace('"', "&quot", $jobName);
	      //$jobName = str_replace($searched_html_characters, $replacement_html_equivalents, $jobName);
	      $jobName = mysqli_real_escape_string($conn, $jobName);
	      $jobDesc = $_POST['jobDesc'.$m];
		  $jobDesc = str_replace("'", "&apos", $jobDesc);
		  $jobDesc = str_replace('"', "&quot", $jobDesc);
	      //$jobDesc = str_replace($searched_html_characters, $replacement_html_equivalents, $jobDesc);
	      $jobDesc = mysqli_real_escape_string($conn, $jobDesc);
          if(strcmp($companyName, "") != 0 && strcmp($jobName, "") != 0 && strcmp($jobDesc, "") != 0) {
            $call = "CALL SetUserWorkExperience('$user_id', '$companyName', '$jobName', '$jobDesc');";
            $q = $conn->query($call);
            $r = $q->fetch_assoc();
            mysqli_free_result($r);
            mysqli_next_result($conn);  
          }
        }
        
        /*Email and Email Verification Area*/
        
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
<head>
  <script>
	if(window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
	};
	</script>
</head>
<body>
<div id="content">
		<div class="col-md-9" style="width: 100%; max-width: 100% !important; padding: 15px;">
		    <div class="card">
		        <div class="card-body">
		            <div class="row">
		                <div class="col-md-12">
		                    <h4>Create Your Account</h4>
		                    <hr>
		                </div>
		            </div>
		            <div class="row">
		                <div class="col-md-12">
		                    <form action="AccountCreation.php" method="post">
		                    <input type="hidden" id="numExpertise" name="numExpertise" value="1"/>
		                    <input type="hidden" id="numJobs" name="numJobs" value="1"/>
		                    <input type="hidden" id="numSkills" name="numSkills" value="1"/>
		                    <input type="hidden" id="numEducation" name="numEducation" value="1"/>
                              <div class="form-group row">
                                <label for="firstname" class="col-4 col-form-label">First Name</label> 
                                <div class="col-8">
                                  <input name="firstname" placeholder="First Name" class="form-control here" type="text">
                                </div>
                              </div>
                              <div class="form-group row">
                                <label for="lastname" class="col-4 col-form-label">Last Name</label> 
                                <div class="col-8">
                                  <input name="lastname" placeholder="Last Name" class="form-control here" type="text">
                                </div>
                              </div>
                              <div class="form-group row">
                                <label for="newpass" class="col-4 col-form-label">Password</label> 
                                <div class="col-8">
                                  <input name="password" placeholder="Password" class="form-control here" type="password">
                                </div>
                              </div> 
                              <div class="form-group row">
                                <label for="password_repeat" class="col-4 col-form-label">Repeat Password</label> 
                                <div class="col-8">
                                  <input name="password_repeat" placeholder="Repeat your password" class="form-control here" type="password">
                                </div>
                              </div>
                              <div class="form-group row">
                                <label for="email" class="col-4 col-form-label">Email</label> 
                                <div class="col-8">
                                  <input name="email" placeholder="Email" class="form-control here" required="required" type="text">
                                </div>
                              </div>
                              <div class="form-group row">
                                <label for="Mentee.MaxNumMentees" class="col-4 col-form-label">Maximum amount of Consecutive Mentees</label> 
                                <div class="col-8">
                                  <input name="maxMentees" placeholder="number >= 1" class="form-control here" required="required" type="text">
                                </div>
                              </div>                               
                              <div class="form-group row">
                                <label for="publicinfo" class="col-4 col-form-label">Biography</label> 
                                <div class="col-8">
                                  <textarea name="biography" cols="40" rows="4" class="form-control" placeholder="EXAMPLE TEXT HERE"></textarea>
                                </div>
                              </div>
                              <div class="form-group row">
                                <label for="publicinfo" class="col-4 col-form-label">Communication Methods</label> 
                                <div class="col-8">
                                  <textarea name="comDesc" cols="40" rows="4" class="form-control" placeholder="EXAMPLE TEXT HERE"></textarea>
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
                        					<tr id='exaddr0'>
                        						<td>
                        						<input type="text" name='expertiseName0' placeholder='Expertise Name' class="form-control" cols="2" />
                        						</td>
                        						<td>
                        						<div class="col-8" style="max-width: 100% !important;">
                                                    <textarea name="expertiseDesc0" cols="40" rows="4" class="form-control" placeholder="EXAMPLE TEXT HERE"></textarea>
                                                </div>
                        					</tr>
                                            <tr id='exaddr1'></tr>
                        				</tbody>
                        			</table>
                        		</div>
                        	</div>
                        	<a id="exadd_row" class="btn btn-default pull-left">Add Expertise</a><a id='exdelete_row' class="pull-right btn btn-default">Delete Expertise</a>
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
                        					<tr id='skaddr0'>
                        						<td>
                        						<input type="text" name='skillName0' placeholder='Skill Name' class="form-control" cols="2" />
                        						</td>
                        						<td>
                        						<div class="col-8" style="max-width: 100% !important;">
                                                    <textarea name="skillDesc0" cols="40" rows="4" class="form-control" placeholder="EXAMPLE TEXT HERE"></textarea>
                                                </div>
                        					</tr>
                                            <tr id='skaddr1'></tr>
                        				</tbody>
                        			</table>
                        		</div>
                        	</div>
                        	<a id="skadd_row" class="btn btn-default pull-left">Add Skill</a><a id='skdelete_row' class="pull-right btn btn-default">Delete Skill</a>
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
                        					<tr id='edaddr0'>
                        						<td>
                        						<input type="text" name='degreeName0' placeholder='degree example' class="form-control" cols="2" />
                        						</td>
                        						<td>
                        						<input type="text" name='university0' placeholder='university example' class="form-control" cols="2" />
                        						</td>
                        						<td>
                        						<div class="col-8" style="max-width: 100% !important;">
                                                    <textarea name="degreeDesc0" cols="40" rows="4" class="form-control" placeholder="EXAMPLE TEXT HERE"></textarea>
                                                </div>
                        					</tr>
                                            <tr id='edaddr1'></tr>
                        				</tbody>
                        			</table>
                        		</div>
                        	</div>
                        	<a id="edadd_row" class="btn btn-default pull-left">Add Education</a><a id='eddelete_row' class="pull-right btn btn-default">Delete Education</a>
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
                        					<tr id='weaddr0'>
                        						<td>
                        						<input type="text" name='companyName0' placeholder='Google' class="form-control" cols="2" />
                        						</td>
                        						<td>
                        						<input type="text" name='jobName0' placeholder='Project manager' class="form-control" cols="2" />
                        						</td>
                        						<td>
                        						<div class="col-8" style="max-width: 100% !important;">
                                                    <textarea name="jobDesc0" cols="40" rows="4" class="form-control" placeholder="EXAMPLE TEXT HERE"></textarea>
                                                </div>
                        					</tr>
                                            <tr id='weaddr1'></tr>
                        				</tbody>
                        			</table>
                        		</div>
                        	</div>
                        	<a id="weadd_row" class="btn btn-default pull-left">Add Job</a><a id='wedelete_row' class="pull-right btn btn-default">Delete Job</a>
                        	<br>
                        	<br>
                        	<br>
                        	<br>
                              <div class="form-group row">
                                <div class="offset-4 col-8" style="margin-left: 0 !important;">
                                  <button name="submit" type="submit" class="btn btn-primary">Create Account</button>
                                </div>
                              </div>
                              <p name="testText" style="color: #FF0000;"><?php echo $message?></p>
                              <div class="container signin">
    							<p>Already have an account? <a href="Login.php">Sign in</a>.</p>
  							  </div>
                            </form>
		                </div>
		            </div>		            
		        </div>
		    <!--</div>
		</div>-->
	</div>
</div>
<script type="text/javascript">

$(document).ready(function(){
    var i=1;
    var a = 1;
    var b = 1;
    var c = 1;

    //expertise------------------------------- works
   $("#exadd_row").click(function(){
        $('#exaddr'+i).html("<td><input type='text' name='expertiseName"+i+"' placeholder='Expertise Name' class='form-control' cols='2'/></td><td><div class='col-8' style=\"max-width: 100% !important;\"><textarea name='expertiseDesc"+i+"' cols='40' rows='4' class='form-control' placeholder='EXAMPLE TEXT HERE'></textarea></div>");    							     
        $('#expertiseTable').append('<tr id="exaddr'+(i+1)+'"></tr>');
        document.getElementById("numExpertise").value = i+1;
        i++; 
	});
	
   $("#exdelete_row").click(function(){
  	 if(i>1){
		 $("#exaddr"+(i-1)).html('');
		 document.getElementById("numExpertise").value = i-1;
		 i--;
		 }
	 });

   //education--------------------------- works
   $("#edadd_row").click(function(){
       $('#edaddr'+a).html("<td><input type='text' name='degreeName"+a+"' placeholder='Bachelors CompScie' class='form-control' cols='2' /></td><td><input type='text' name='university"+a+"' placeholder='EWU' class='form-control' cols='2' /></td><td><div class='col-8' style=\"max-width: 100% !important;\"><textarea name='degreeDesc"+a+"' cols='40' rows='4' class='form-control' placeholder='EXAMPLE TEXT HERE'></textarea></div>");    							     
       $('#educationTable').append('<tr id="edaddr'+(a+1)+'"></tr>');
       document.getElementById("numEducation").value = a+1;
       a++; 
	});
	
  $("#eddelete_row").click(function(){
 	 if(a>1){
		 $("#edaddr"+(a-1)).html('');
		 document.getElementById("numEducation").value = a-1;
		 a--;
		 }
	 });

  //work experience-----------------------------------
  $("#weadd_row").click(function(){
      $('#weaddr'+b).html("<td><input type='text' name='companyName"+b+"' placeholder='Google' class='form-control' cols='2' /></td><td><input type='text' name='jobName"+b+"' placeholder='Project manager' class='form-control' cols='2' /></td><td><div class='col-8' style=\"max-width: 100% !important;\"><textarea name='jobDesc"+b+"' cols='40' rows='4' class='form-control' placeholder='EXAMPLE TEXT HERE'></textarea></div>");    							     
      $('#jobTable').append('<tr id="weaddr'+(b+1)+'"></tr>');
      document.getElementById("numJobs").value = b+1;
      b++; 
	});
	
 $("#wedelete_row").click(function(){
	 if(i>1){
		 $("#weaddr"+(b-1)).html('');
		 document.getElementById("numJobs").value = b-1;
		 b--;
		 }
	 });

 //Skills--------------------------------------------------------
 $("#skadd_row").click(function(){
     $('#skaddr'+c).html("<td><input type='text' name='skillName"+c+"' placeholder='Skill Name' class='form-control' cols='2'/></td><td><div class='col-8' style=\"max-width: 100% !important;\"><textarea name='skillDesc"+c+"' cols='40' rows='4' class='form-control' placeholder='EXAMPLE TEXT HERE'></textarea></div>");    							     
     $('#skillTable').append('<tr id="skaddr'+(c+1)+'"></tr>');
     document.getElementById("numSkills").value = c+1;
     c++; 
	});
	
$("#skdelete_row").click(function(){
	 if(c>1){
		 $("#skaddr"+(c-1)).html('');
		 document.getElementById("numSkills").value = c-1;
		 c--;
		 }
	 });

});
</script>
</div>
</body>
<?php
include 'includes/footer.php';
//"</td><td><input name='name"+i+"' type='text' placeholder='Name' class='form-control input-md'  /> </td><td><input  name='mail"+i+"' type='text' placeholder='Mail'  class='form-control input-md'></td><td><input  name='mobile"+i+"' type='text' placeholder='Mobile'  class='form-control input-md'></td>");
?>
</html>
