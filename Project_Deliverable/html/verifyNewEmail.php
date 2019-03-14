<?php
session_start();
#ini_set('display_errors','on');
#error_reporting(E_ALL);
require_once("../internalIncludes/dbconfig.php");
include 'includes/emailFunctions.php';

if(isset($_GET['email']) && !empty($_GET['email']) AND isset($_GET['hash']) && !empty($_GET['hash']) AND isset($_GET['oldemail']) && !empty($_GET['oldemail']))
{
    $email = mysqli_real_escape_string($conn, $_GET['email']); // Set email variable
    $oldemail = mysqli_real_escape_string($conn, $_GET['oldemail']); //set old email variable
    $hash = mysqli_real_escape_string($conn, $_GET['hash']); // Set hash variable
    $call = "CALL CheckEmailChangeVerification('$oldemail','$hash');";
        $q = $conn->query($call);
        $r = $q->fetch_assoc();
        $Confirmation = $r['Confirmation'];
        mysqli_free_result($r);
        mysqli_next_result($conn);
    echo "Confirmation: ".$Confirmation;
    echo "<br>newemail: ".$email;
    echo "<br>oldemail: ".$oldemail;
    
    
    if($Confirmation == 3) 
    {
      //get userID
      $call = "CALL GetUserID_UserName('$oldemail');";
      $q = $conn->query($call);
      $r = $q->fetch_assoc();
      $UserID = $r['UserID'];
      mysqli_free_result($r);
      mysqli_next_result($conn);
      echo "<br>UserID: ".$UserID;
      //update User.Username
      $call = "CALL EditUserEmail('$UserID','$email');";
      $q = $conn->query($call);
      $r = $q->fetch_assoc();
      $UserID = $r['UserID'];
      mysqli_free_result($r);
      mysqli_next_result($conn);
      
      sendMentorEmailChangeNotification($email);
      echo "<script> location.href='Login.php'; </script>";
      exit;
    }
    elseif($Confirmation == 1)
    {
      echo "<script>location.href='Login.php';</script>";
      exit;
    }
    #echo '<br><br><br>0 = Bad Hash Match<br>1 = Already Validated<br>2 = No Email Found (in EmailVerification)<br>3 = Success! Email is now validated. Future clicks of the link should return 1.<br><br>Confirmation: '.$Confirmation;
    echo "<script> location.href='Login.php'; </script>";
    exit;
}
else
{
  #echo 'Something went wrong with the URL';
  echo "<script> location.href='Login.php'; </script>";
  exit;
}

?>