<?php
  session_start();
  require_once("../internalIncludes/dbconfig.php");
  
  $srch_constraint = $_REQUEST['q'];
  $srch_constraint = mysqli_real_escape_string($conn, $srch_constraint);
  $q_mentor_names = array();
  $q_mentor_id = array();
  $q_mentor_job = array();
  $q_mentor_company = array();
  $q_mentor_degree = array();
  $q_mentor_university = array();
  
  if(strlen($srch_constraint) <= 100) {
	$call = "CALL SearchMentors('$srch_constraint');";
	$query = $conn->query($call);
	while($res = $query->fetch_assoc()) {
		$q_mentor_names[] = $res['Name'];
		$q_mentor_id[] = $res['UserID'];
		$q_mentor_job[] = $res['JobName'];
		$q_mentor_company[] = $res['CompanyName'];
		$q_mentor_degree[] = $res['DegreeName'];
		$q_mentor_university[] = $res['UniversityName'];
	}
    mysqli_free_result($res);
    mysqli_next_result($conn);
  } 
  
  if(!empty($q_mentor_id)) {
	$display_mentor_names = $q_mentor_names;
	$display_mentor_id = $q_mentor_id;
	$display_mentor_job = $q_mentor_job;
	$display_mentor_company = $q_mentor_company;
	$display_mentor_degree = $q_mentor_degree;
	$display_mentor_university = $q_mentor_university;
  }
  else {
	  $display_mentor_names = $_SESSION['def_srch_names'];
	  $display_mentor_id = $_SESSION['def_srch_ids'];
	  $display_mentor_job = $_SESSION['def_srch_jobs'];
	  $display_mentor_company = $_SESSION['def_srch_companies'];
	  $display_mentor_degree = $_SESSION['def_srch_degrees'];
	  $display_mentor_university = $_SESSION['def_srch_universities'];
  }
  
  $innerHtml = "";
  
    $mentor_cnt = 0;
    foreach($display_mentor_id as $id) {
          $i_mentor_name = $display_mentor_names[$mentor_cnt];
	  $i_mentor_id = $display_mentor_id[$mentor_cnt];
          $i_mentor_job = $display_mentor_job[$mentor_cnt];
          $i_mentor_company = $display_mentor_company[$mentor_cnt];
          $i_mentor_degree = $display_mentor_degree[$mentor_cnt];
          $i_mentor_university = $display_mentor_university[$mentor_cnt];
	  $mentor_cnt++;
	  if($i_mentor_job != "") {
		  if($i_mentor_company != "") {
			  $jobStr = "Current job experience: $i_mentor_job, $i_mentor_company.";
		  }
		  else {
			  $jobStr = "Current job experience: $i_mentor_job.";
		  }
	  }
	  else if($i_mentor_company != "") {
		  $jobStr = "Current job experience: $i_mentor_company.";
	  }
	  if($i_mentor_degree != "") {
		  if($i_mentor_university != "") {
			  $eduStr = "Education: $i_mentor_degree, $i_mentor_university.";
		  }
		  else {
			  $eduStr = "Education: $i_mentor_degree.";
		  }
	  }
	  else if($i_mentor_university != "") {
		  $eduStr = "Education: $i_mentor_university.";
	  }
          $innerHtml .= "<form method=\"post\" action=\"Search.php\" style=\"margin-bottom: 0;\"><input type=\"hidden\" name=\"selected_profile\" value=\"$i_mentor_id\"></input><button class=\"srch_results_btn\" type=\"submit\"><div class=\"srch_list\"><div class=\"srch_img\"><img src=\"http://146.187.134.42/Images/M-Icon.png\"></div><div class=\"srch_ib\"><h4>$i_mentor_name</h4><h5>$jobStr</h5><h6>$eduStr</h6></div></div></button></form>";
      $jobStr = $eduStr = "";
    }
	
	echo $innerHtml;
?>