<?php
	session_start();
	require_once("../internalIncludes/dbconfig.php");
	
	$user_id = $_REQUEST["id"];
	$option = $_REQUEST['o'];
	$buttonLayout = "";
	if($option == 0) {
		$buttonLayout = "<button type=\"submit\" style=\"float: right\" onclick=\"denyMentor();\">Deny</button><button type=\"submit\" style=\"float: right; margin-right: 5px;\" onclick=\"approveMentor();\">Approve</button>";
	}
	else if($option == 2) {
		$buttonLayout = "<button type=\"submit\" style=\"float: right\" onclick=\"unbanUser();\">Unban User</button>";
	}
		   
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
	
	
?>

<body>
		<div class="col-md-9" style="width: 100%; max-width: 100% !important; padding: 15px;">
		    <div class="card">
		        <div class="card-body">
		            <div class="row">
		                <div class="col-md-12">
				<h4><?php echo $firstname." ".$lastname; echo $buttonLayout; ?>
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
</body>
