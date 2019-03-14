<?php
  session_start();
  require_once("../internalIncludes/dbconfig.php");
  include 'includes/header.php';
  include 'includes/site-header-loader.php';
  include 'includes/pageOverlay.php';
  include 'includes/loginStyle.php';
  include 'includes/emailFunctions.php';
  ini_set('display_errors','on');
	error_reporting(E_ALL);
  $msg = $username = "";
  if(isset($_POST['username']) && $_POST['username'] != "") 
  {
    $username = $_POST['username'];
    $email = $username; //for consistency and readability
    
    $sql = "CALL CheckIsEmailAvailable('$username')";
    $q = $conn->query($sql);
    $r = $q->fetch_assoc();
    mysqli_free_result($q);
    mysqli_next_result($conn);
    
    if($r['Confirmation'] == 0) //reset password
    {
      $msg = "Please check your email for further instructions.";
      
      $EmailHash = md5( rand(0,1337) );
      $sql = "CALL CreatePasswordChangeVerification('$email','$EmailHash');";
      $conn->query($sql);
      sendMentorEmailPasswordReset($email,$EmailHash);  
    }
    else //account was not found with that email
    {
      $msg = "No account was found using that Email Address.";
    }
  }
?>
<!DOCTYPE html>
  <html>
  <head>
    <title>Chat System Login</title>
  </head>
  <body>
  <div id='content'>
      <?php
        $username = "";
      ?>
    <!--<h1 style="color:#FF0000" align="center">Login</h1>-->
    <div class="login-container">
    <form class="login-form" method="post" action="resetPassword.php">
      <label class="login-labelHeader">Reset Your Account Password</label>
      <label class="login-label2" for="username">Please enter your Email:</label>
      <input class="login-input2" required type="text" id="username" name="username" placeholder="Email" value="<?php echo $username;?>">
      <span style= "margin-left: 20px; color: #FF0000" id="msg" name="msg"><?php echo $msg ?></span>
      <div id="lower">
      <input class="login-input" type="submit" style="width:126px;" name="Password Reset" value="Reset Password">
      </div>
    </form>
    </div>
  </div>
  </body>
  </html>
<?php
  include 'includes/footer.php';
?>
