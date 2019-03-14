<html>
<?php
session_start();
require_once("../internalIncludes/dbconfig.php");
include 'includes/header.php';
include 'includes/site-header-loader.php';
include 'includes/pageOverlay.php';
include 'includes/loginStyle.php';
$message = "OOPS YOUR ACCOUNT IS EITHER DISABLED OR BANNED, AN EMAIL SHOULD BE ARRIVING SHORTLY OR ONE HAS ALREADY BEEN SENT, MAKE SURE TO CHECK YOUR SPAM FOLDER";

if(isset($_SESSION['user_id'])) //user is logged in
{
  $user_id = $_SESSION['user_id'];

  $sql = "CALL GetUser('$user_id')";
  $q = $conn->query($sql);
  $r = $q->fetch_assoc();
  $IsEnabled = $r['IsEnabled'];
  $IsMentor = $r['IsMentor'];
  $FullName = $r['Name'];
  mysqli_free_result($q);
  mysqli_next_result($conn);
  $_SESSION['IsEnabled'] = $IsEnabled;
  $_SESSION['IsMentor'] = $IsMentor;
  $_SESSION['full_name'] = $FullName;
  
  if($IsEnabled == 1) //User is enabled properly. Redirect to Login
  {
    echo "<script> location.href='Login.php'; </script>";
    exit;
  }
  else if($IsEnabled == 0) //user has not been approved by an admin yet
  {
    //check if user has authenticated their email      
      $sql = "CALL CheckUserVerified('$user_id')";
      $q = $conn->query($sql);
      $r = $q->fetch_assoc();
      $IsValidated = $r['validated'];
      mysqli_free_result($q);
      mysqli_next_result($conn);
      
      if($IsValidated == -1)//this is an error where the email address could not be found
      {
        if($IsMentor == 0)
        {
          $message = "<p>It has come to our attention this account may be involved in inappropriate and unapproved activity. Your account has been temporarily disabled or suspended while administrators conduct their review. If you have questions, you may contact Career Services at 509.359.6365.</p>";
        }
        $message = "<p><b>An Error occurred when checking on your account. Please check back later.<br>If this issue persists, please contact Career Services for more info.</p></b>";
      }
      else if($IsValidated == 0) //email has not been validated. Check email
      {
        $message = "<p><b>The email address set for your account has not been verified yet.</b></p><br><p>Please check your email for a verification link.</p><p> Make sure to check your spam folder as it occasionally gets put there.</p>";
      }
      else if($IsValidated == 1) //email was validated. Wait for admin to accept your mentorship.
      {
        $message = "<p><b>Your account is currently pending admin approval.</b></p><br><p>You will be sent an email when your account has been reviewed.</p><p> Please be patient as this may take a day or two. If you have questions, you may contact Career Services at 509.359.6365.</p>";
      }
      else //this is a general sql error
      {
        
      }
  }
  else if($IsEnabled == 2) //user has been banned/susspended by admin
  {
    $message = "<p>It has come to our attention this this account may be involved in inappropriate and unapproved activity. Your account has been temporarily disabled or suspended while administrators conduct their review. If you have questions, you may contact Career Services at 509.359.6365.</p>";
  }
  else //edgecase check
  {
    echo "<script> location.href='LandingPage.php'; </script>";
    exit;
  }
}
else //no user is logged in
{
  echo "<script> location.href='Login.php'; </script>";
  exit;
}




?>
<body>
<div id='content'>
  <center style="font-size: 35px; margin: 13% 4.5% 0 4.5%;">
    <?php echo $message;?>
  </center>
</div>
</body>
<?php
include 'includes/footer.php';
//"</td><td><input name='name"+i+"' type='text' placeholder='Name' class='form-control input-md'  /> </td><td><input  name='mail"+i+"' type='text' placeholder='Mail'  class='form-control input-md'></td><td><input  name='mobile"+i+"' type='text' placeholder='Mobile'  class='form-control input-md'></td>");
?>
</html>
