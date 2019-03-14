<?php
  session_start();
  require_once("../internalIncludes/dbconfig.php");
  include 'includes/header.php';
  include 'includes/site-header-loader.php';
  include 'includes/pageOverlay.php';
  include 'includes/loginStyle.php';
  
  //If user is logged in, re-set all main session values and redrect as needed
  if(isset($_SESSION['user_id'])) {
      $user_id = $_SESSION['user_id'];
      $sql = "CALL GetUser('$user_id')";
      $q = $conn->query($sql);
      $r = $q->fetch_assoc();
      $IsEnabled = $r['IsEnabled'];
      $IsMentor = $r['IsMentor'];
      $FullName = $r['Name'];
      mysqli_free_result($q);
      mysqli_next_result($conn);
      $_SESSION['IsEnabled'] = $IsEnabled; //re-affirms that Mentor is still enabled
      $_SESSION['IsMentor'] = $IsMentor;
      $_SESSION['full_name'] = $FullName;
      $disabled_alert_num = 0;
      if($IsEnabled == 1)
      {
        if($IsMentor == 1)
        {
          echo "<script> location.href='Profile.php'; </script>";
          exit;
        }
        elseif($IsMentor == 0)
        {
          echo "<script> location.href='Conversations.php'; </script>";
          exit;
        }
        elseif($IsMentor == 2)
        {
          echo "<script> location.href='AdminConsole.php'; </script>";
          exit;
        }   
        else//something went wrong
        {
          echo "<script> location.href='LandingPage.php'; </script>";
          exit;
        }
      }    
      else #This block needs changed to a proper disabled page. IsEnabled=0 will be simple disabled/pending admin approval IsEnabled=2 will be banned.
      {
         $msg = "Sorry your account is under review. Please contact Career Services with any questions.";
         #echo '<script type="text/javascript">alert("' . $msg . '")</script>';
         $disabled_alert_num = 1;
        echo "<script> location.href='AccountDisabled.php'; </script>";
        exit;
      }
  }
   
  $error = $username = $user_id = "";
  if(isset($_POST['username']) && isset($_POST['password'])) {
	  if($_POST['username'] != "" && $_POST['password'] != "")
	  {
		  $username = $_POST['username'];
      $sql = "CALL GetUserID_Username('$username')";
      $q = $conn->query($sql);
      $r = $q->fetch_assoc();
		  $user_id = $r['UserID'];
		  mysqli_free_result($q);
		  mysqli_next_result($conn);
      
      if($user_id >= 0)
		  {
			  $password = $_POST['password'];
        $sql = "CALL GetUserPassword('$user_id')";
        $q = $conn->query($sql);
        $r = $q->fetch_assoc();
			  $serverHash = $r['PasswordHash'];
        mysqli_free_result($q);
        mysqli_next_result($conn);
        
        if(password_verify($password, $serverHash))
        {
          $_SESSION['user_id'] = $user_id;
          
          //set session variables for user
          $sql = "CALL GetUser('$user_id');";
          $q = $conn->query($sql);
          $r = $q->fetch_assoc();
          $_SESSION['user_id'] = $r['UserID'];
          $_SESSION['IsMentor'] = $r['IsMentor'];
          $_SESSION['IsEnabled'] = $r['IsEnabled'];
          $_SESSION['full_name'] = $r['Name'];
          mysqli_free_result($q);
          mysqli_next_result($conn); 
          
          //redirect to Login.php again, but now logged in, to handle redirects
          echo "<script> location.href='Login.php'; </script>";
          exit;
        }
        else
			  {
				  $error = "Username or Password was incorrect";
        }
      }
      else
		  {
			  $error = "Username or Password was incorrect.";
      }
  	}
  }
?>
<!DOCTYPE html>
  <html>
  <head>
    <title>Chat System Login</title>
	<script>
		if(window.history.replaceState) {
			window.history.replaceState(null, null, window.location.href);
		};
	</script>
  </head>
  <body>
  <div id='content'>
      <?php
        $username = $password = "";
      ?>
    <!--<h1 style="color:#FF0000" align="center">Login</h1>-->
    <div class="login-container">
    <form class="login-form" method="post" action="Login.php">
      <label class="login-label2" for="username">Username:</label>
      <input class="login-input2" required type="text" id="username" name="username" placeholder="Enter Your Username" value="<?php echo $username;?>">
      <label class="login-label2" for="password">Password:</label>
      <input class="login-input2" required type="password" id="password" name="password" placeholder="Enter Your Password" value="<?php echo $password;?>">
      <span style= "margin: 7px 0 0 20px; color: #FF0000" id="error" name="error"><?php echo $error ?></span></br>
      <a href="resetPassword.php" style="margin:5px 0 0 20px; color: orangered;">Forgot your password?</a>
      <div id="lower">
      <input class="login-input" type="submit" value="login" name="login">
      <input class="login-input" type="button" onclick="window.location='AccountCreation.php'" name="AccountCreation" value="Create New Mentor" >
      

      </div>
    </form>
    </div>
  </div>
  </body>
  </html>
<?php
  include 'includes/footer.php';
?>
