<?php
  session_start();
  require_once("../internalIncludes/dbconfig.php");
  
  $srch_constraint = $_REQUEST['q'];
  
  $q_mentor_names = array();
  $q_mentor_id = array();
  
  if(strlen($srch_constraint) <= 100) {
	$call = "CALL SearchMentors('$srch_constraint');";
	$query = $conn->query($call);
	while($res = $query->fetch_assoc()) {
		$q_mentor_names[] = $res['Name'];
		$q_mentor_id[] = $res['UserID'];
	}
    mysqli_free_result($res);
    mysqli_next_result($conn);
  } 

	$display_mentor_names = $q_mentor_names;
	$display_mentor_id = $q_mentor_id;
  
  $innerHtml = "";
	$cnt = 0;
	foreach($display_mentor_names as $i_name) {
		$i_id = $display_mentor_id[$cnt];
		$tag_id = $i_id;
		$tag_id .= " ";
		$tag_id .= $i_name;
		$innerHtml .= "<div class=\"row\" style=\"float: right; margin: 0 !important; padding: 0 !important; width: 95%; height: inherit; border: 1px solid #ccc;\"><ul style=\"margin: 0 !important; padding: 0 !important; width: 100%; height: inherit;\"><button style=\"padding: 0 !important; margin: 0 !important; width: 100%; height: inherit;\" value=\"$tag_id\" onclick=\"showSelectedMentorProfile(this.value, 1);\">";
		$innerHtml .= $i_name;
		$innerHtml .= "</button></ul></div>";
		$cnt++;
	}
	if(count($display_mentor_id) == 0) {
		$innerHtml = "<div class=\"row\" style=\"float: right; margin: 0 !important; padding: 0 !important; width: 95%; height: inherit; border: 1px solid #ccc;\"><ul style=\"margin: 0 !important; padding: 0 !important; width: 100%; height: inherit;\"><button style=\"padding: 0 !important; margin: 0 !important; width: 100%; height: inherit;\" value=\"default\" disabled>No Search Results Found</button></ul></div>";
	}
	echo $innerHtml;
?>