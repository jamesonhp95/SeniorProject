<?php
  session_start();
  $sug = $_SESSION['sug_list'];
  //$sug[] = "test";
  $q = $_REQUEST["q"];
  $suggestions = "";
  $cnt = 0;
  if($q != "") {
	  $q = strtolower($q);
	  $len = strlen($q);
	  foreach($sug as $text) {
		  if(stristr($q, substr($text, 0, $len)) && $cnt < 10) {
			  if($suggestions == "") {
				  $cnt++;
				  $suggestions = $text;
			  }
			  else {
				  $cnt++;
				  $suggestions .= ", $text";
			  }
		  }
	  }
  }

  echo $suggestions;
?>

