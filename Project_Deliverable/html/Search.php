<?php
  session_start();
  require_once("../internalIncludes/dbconfig.php");
  include 'includes/header.php';
  include 'includes/site-header-loader.php';
  include 'includes/pageOverlay.php';
  include 'includes/searchStyle.php';
  
  //ensures logged in users must be mentees
  if(!isset($_SESSION['IsMentor']) || $_SESSION['IsMentor'] != 0)
  {
    echo "<script> window.location='MenteeLogin.php'; </script>";
    exit;
  }
  
  if($_SESSION['IsEnabled'] != 1)
  {
    echo "<script>location.href='AccountDisabled.php';</script>";
	  exit;
  }
  
  if(isset($_POST['selected_profile'])) {
	  $_SESSION['view_user_id'] = $_POST['selected_profile'];
	  echo "<script>location.href='ViewProfile.php';</script>";
  }

  if(!isset($_SESSION['def_srch_names']) || !isset($_SESSION['def_srch_ids']) || !isset($_SESSION['def_srch_jobs']) || !isset($_SESSION['def_srch_companies']) || !isset($_SESSION['def_srch_degrees']) || !isset($_SESSION['def_srch_universities'])) {
	  $def_mentor_names = array();
	  $def_mentor_id = array();
	  $def_mentor_job = array();
	  $def_mentor_company = array();
	  $def_mentor_degree = array();
	  $def_mentor_university = array();
	  
	  $call = "CALL SearchMentor_All();";
	  $query = $conn->query($call);
	  $cnt = 0;
	  while($res = $query->fetch_assoc()) {
		  if($cnt < 20) {
			  $def_mentor_names[] = $res['Name'];
			  $def_mentor_id[] = $res['UserID'];
			  $def_mentor_job[] = $res['JobName'];
			  $def_mentor_company[] = $res['CompanyName'];
			  $def_mentor_degree[] = $res['DegreeName'];
			  $def_mentor_university[] = $res['UniversityName'];
			  $cnt++;
		  }
	  }
	  $_SESSION['def_srch_names'] = $def_mentor_names;
	  $_SESSION['def_srch_ids'] = $def_mentor_id;
	  $_SESSION['def_srch_jobs'] = $def_mentor_job;
	  $_SESSION['def_srch_companies'] = $def_mentor_company;
	  $_SESSION['def_srch_degrees'] = $def_mentor_degree;
	  $_SESSION['def_srch_universities'] = $def_mentor_university;
	  mysqli_free_result($res);
	  mysqli_next_result($conn);
  }
 
  if(!isset($_SESSION['sug_list'])) {
	$sugs = array();
  	$call = "CALL GetRecommendationList();";
  	$query = $conn->query($call);
  	while($res = $query->fetch_assoc()) {
	  	$sugs[] = $res['itemName'];
	}
	mysqli_free_result($res);
	mysqli_next_result($conn);
  	$_SESSION['sug_list'] = $sugs;
  }

  $q_mentor_names = array();
  $q_mentor_id = array();
  $q_mentor_job = array();
  $q_mentor_company = array();
  $q_mentor_degree = array();
  $q_mentor_university = array();
  
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
?>
<!DOCTYPE HTML>
<html>
<head>
  <script>
  function showSuggestion(tempStr) {
	  if(tempStr.length == 0) {
		  document.getElementById("suggestion").innerHTML = "";
	  	  return;
	  }
	  else {
		  var str = tempStr.replace(/&/g, "%26amp;").replace(/>/g, "%26gt;").replace(/</g, "%26lt;").replace(/\+/g, "%2B").replace(/#/gi, "%23");
		  tempStr = str;
		  str = tempStr.replace(/\n/g, '<br/>');
		  var xmlhttp = new XMLHttpRequest();
		  xmlhttp.onreadystatechange = function() {
			  if(this.readyState == 4 && this.status == 200) {
				  document.getElementById("suggestion").innerHTML = this.responseText;
			  }
		  };
		  xmlhttp.open("GET", "searchSuggestions.php?q=" + str, true);
		  xmlhttp.send();
	  }
  };
  
  function searchMentors(tempStr) {
	  if(tempStr != "") {
		  document.getElementById("search_constraint").value = "";
		  var str = tempStr.replace(/&/g, "%26amp;").replace(/>/g, "%26gt;").replace(/</g, "%26lt;").replace(/\+/g, "%2B").replace(/#/gi, "%23");
		  tempStr = str;
		  str = tempStr.replace(/\n/g, '<br/>');
		  var xmlhttp = new XMLHttpRequest();
		  xmlhttp.onreadystatechange = function() {
			  if(this.readyState == 4 && this.status == 200) {
				  document.getElementById("srch_results_area").innerHTML = this.responseText;
			  }
		  };
		  xmlhttp.open("GET", "searchMentors.php?q="+str, true);
		  xmlhttp.send();
	  }
  };
  </script>
</head>
<body>
<div id='content'>
    <div class="mentor-srch-bar">
      <div class="stylish-input-group">
        <input type="text" id="search_constraint" name="search_constraint" class="search-bar" placeholder="Search for the type of mentor you want!" onkeyup="showSuggestion(this.value)">
	    <button type="submit" style="position: absolute; padding-bottom:10px; width: 5%;" onclick="searchMentors(search_constraint.value);"><i class="fa fa-search" aria-hidden="true"></i></button>
      </div>
    </div>
  <div class="suggestion-box">
    <span style="padding-left: 5px;">Suggestions: </span>
    <span id="suggestion"></span>
  </div>
  <div id="srch_results_area">
  <?php
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
          echo "<form method=\"post\" action=\"Search.php\" style=\"margin-bottom: 0;\"><input type=\"hidden\" name=\"selected_profile\" value=\"$i_mentor_id\"></input><button class=\"srch_results_btn\" type=\"submit\"><div class=\"srch_list\"><div class=\"srch_img\"><img src=\"http://146.187.134.42/Images/M-Icon.png\"></div><div class=\"srch_ib\"><h4>$i_mentor_name</h4><h5>$jobStr</h5><h6>$eduStr</h6></div></div></button></form>";
    $jobStr = $eduStr = "";
    }
  ?>
  </div>
</div>
<!--</div>-->
</body>
</html>
<?php
  include 'includes/footer.php';
?>
