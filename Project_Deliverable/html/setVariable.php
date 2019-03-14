<?php
	session_start();
	$q = $_REQUEST['id'];
	$_SESSION['view_user_id'] = $q;
	echo "success";
?>