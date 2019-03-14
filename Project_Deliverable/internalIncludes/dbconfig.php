<?php
/*
* All passwords, usernames, host, and database names have been removed for security.
*/
  $host = '******';
  $dbname = '******';
  $username = '******';
  $password = '******';

  try {
	  $conn = new mysqli($host, $username, $password, $dbname);
	  //echo "<span style=\"color:#FF0000\">Connected to $dbname at $host successfully.</span>";
  }catch(PDOException $pe) {
	  die("Could not connect to the database $dbname :" . $pe->getMessage());
  }
?>
