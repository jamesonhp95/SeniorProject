<?php
  session_start();
  require_once("../internalIncludes/dbconfig.php");
  include 'includes/loginStyle.php';
 
  //ensures logged in user is an admin
  if(!isset($_SESSION['IsMentor']) || $_SESSION['IsMentor'] != 2)
  {
    echo "<script> window.location='LandingPage.php'; </script>";
    exit;
  }
  
  if($_SESSION['IsEnabled'] != 1)
  {
    echo "<script>location.href='AccountDisabled.php';</script>";
	  exit;
  }
  
  $error = $error2 = $username = $user_id = "";
  if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['password2'])) 
  {
	  if($_POST['username'] != "" && $_POST['password'] != "" && $_POST['password2'] != "")
	  {
      if(strcmp($_POST['password'],$_POST['password2']) == 0)//password match
      {
        $error = "Your credentials have been updated";
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        $call = "CALL SetAdminEmailCredentials('$username','$password');";
        $q = $conn->query($call);
        mysqli_next_result($conn);
      }
      else//passwords did not match
      {
        $error = "Your passwords did not match. Please try again.";
      }
    }
    else //1 or more entries were left blank
    {
      $error = "Some of the entries above were empty. Please try again.";
    }
  }
?>
<!DOCTYPE html>
  <html>
  <head>
    <title>Email Credential Password Change</title>
  </head>
  <script>

</script>
  <body>
  <div id='content'>
      <?php
        $username = $password = $password2 = "";
      ?>
    <!--<h1 style="color:#FF0000" align="center">Login</h1>-->
    <div class="login-container2">
    <form class="login-form" method="post" action="showAdminEmailCredentialInput.php">
      <label class="login-labelHeader">Change Automated Email Credentials</label>
      <label class="login-label2" for="username">Email Address:</label>
      <input class="login-input2" required type="text" id="username" name="username" placeholder="Enter Email Address" value="<?php echo $username;?>">
      <label class="login-label2" for="password">Password:</label>
      <input class="login-input2" required type="password" id="password" name="password" placeholder="Enter Your Password" value="<?php echo $password;?>">
      <label class="login-label2" for="password">Confirm Password:</label>
      <input class="login-input2" required type="password" id="password2" name="password" placeholder="Enter Your Password" value="<?php echo $password2;?>">
      <span style= "margin-left: 20px; color: #FF0000" id="message" name="error"><?php echo $error ?></span>
      <div id="lower">
      <input class="login-input" type="button" value="Change Credentials" style="width: 150px; float: right;" name="login" onclick="postAdminEmailCredentialsPage();">
      </div>
    </form>
    </div>
    <div class="login-container2" Style="margin-top: 50px;">
    <form class="login-form" method="post" action="showAdminEmailCredentialInput.php">
      <center>
        <label class="login-labelHeader">Test Automated Email Credentials</label>
        <label class="login-label2" for="info" style="padding:10px 10px 0 0px;">This will send an automated email to the email currently being used by the Mentorship program. After pressing the button bellow, you should recieve an email within 30-60 seconds. If you do not, the credentials entered may be incorrect. Please update the credentials above and try again.</label>
      </center>
      <span style= "margin-left: 20px; color: #FF0000" id="message2" name="error"></span>
      <div id="lower">
      <input class="login-input" type="button" value="Send Test Email" style="width: 130px; float: right;" name="login" onclick="sendTestEmail();">
      </div>
    </form>
    </div>
  </div>
  </body>
  </html>
