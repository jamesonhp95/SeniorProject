<?php
  $host = 'localhost:3306';
  $dbname = 'MentorProgram';
  $username = 'databaseUser';
  $password = 'Az16dqwBztTj-agB';

  try {
	  $conn = new mysqli($host, $username, $password, $dbname);
	  //echo "<span style=\"color:#FF0000\">Connected to $dbname at $host successfully.</span>";
  }catch(PDOException $pe) {
	  die("Could not connect to the database $dbname :" . $pe->getMessage());
  }
?>
