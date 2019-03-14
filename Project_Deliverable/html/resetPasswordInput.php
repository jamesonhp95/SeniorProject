<?php
  session_start();
  require_once("../internalIncludes/dbconfig.php");
  include 'includes/header.php';
  include 'includes/site-header-loader.php';
  include 'includes/pageOverlay.php';
  include 'includes/loginStyle.php';
  include 'includes/emailFunctions.php';

  $msg = $username = "";  

  if(isset($_GET['email']) && !empty($_GET['email']) AND isset($_GET['hash']) && !empty($_GET['hash']))
  {
    $email = mysqli_real_escape_string($conn, $_GET['email']); // Set email variable
    $hash = mysqli_real_escape_string($conn, $_GET['hash']); // Set hash variable
    $_SESSION['verification_email'] = $email;
    $_SESSION['verification_email_hash'] = $hash;
  }
  if(isset($_SESSION['verification_email']) && isset($_SESSION['verification_email_hash']))
  {
    $email = $_SESSION['verification_email'];
    $hash = $_SESSION['verification_email_hash'];
    
    $call = "CALL CheckPasswordChangeValidated('$email','$hash');";
    $q = $conn->query($call);
    $r = $q->fetch_assoc();
    $Confirmation = $r['Confirmation'];
    mysqli_free_result($q);
    mysqli_next_result($conn);
    if($Confirmation == 3)//email and hash match in PasswordChangeVerification Table. Ready to change password.
    {
      if(isset($_POST['password']) && isset($_POST['password2']))
      {
        if($_POST['password'] != "" && $_POST['password2'] != "")
        {
          if(strcmp($_POST['password'],$_POST['password2']) == 0)//password match
          {
            $msg = "Your credentials have been updated. Click <a href=\"Login.php\">Here</a> to log in.";
            $password = $_POST['password'];
            $username = $_SESSION['verification_email'];
            
            $sql = "CALL GetUserID_Username('$username')";
            $q = $conn->query($sql);
            $r = $q->fetch_assoc();
            $user_id = $r['UserID'];
            mysqli_free_result($q);
            mysqli_next_result($conn);
            
            $pass_hash = password_hash($password, PASSWORD_DEFAULT);
            
            $sql = "CALL ChangeUserPassword('$user_id', '$pass_hash')";
            $q = $conn->query($sql);
            $r = $q->fetch_assoc();
            $success = $r['Confirmation'];
            mysqli_free_result($q);
            mysqli_next_result($conn);
            if($success != 0)
            {
              $msg = "An error occurred when creating your account. Please try again.";
            }
            else
            {
              $call = "CALL ValidatePasswordChange('$email');";
              $q = $conn->query($call);
              $r = $q->fetch_assoc();
              $Confirmation = $r['Confirmation'];
              mysqli_free_result($q);
              mysqli_next_result($conn);
              
              UNSET($_SESSION['verification_email']);
              UNSET($_SESSION['verification_email_hash']);
            }
          }
          else//passwords did not match
          {
            $msg = "Your passwords did not match. Please try again.";
          }
        }
        else //1 or more entries were left blank
        {
          $msg = "Some of the entries above were empty. Please try again.";
        }
      }
    }
    elseif($Confirmation == 1)//that email does not have an open password change request
    {
      UNSET($_SESSION['verification_email']);
      UNSET($_SESSION['verification_email_hash']);
      echo "<script> location.href='Login.php'; </script>";
      exit;
    }
    elseif($Confirmation == 0)//email has an open password change request but the hash does not match. This means the link has expired as a new link has been sent already.
    {
      UNSET($_SESSION['verification_email']);
      UNSET($_SESSION['verification_email_hash']);
      $msg = "This password reset link has expired. If you need a new link sent, click <a href=\"resetPassword.php\">HERE.</a>";
    }
    elseif($Confirmation == 2)//email was not found in PasswordChangeVerification table. This means a password change has never been requested
    {
      UNSET($_SESSION['verification_email']);
      UNSET($_SESSION['verification_email_hash']);
      echo "<script> location.href='LandingPage.php'; </script>";
      exit;
    }
    else//error. Should never reach here
    {
      
    }
    //call the main print page
    $password = $password2 = "";
    echo 
    ' <!DOCTYPE html>
        <html>
          <head>
            <title>Chat System Login</title>
          </head>
          <body>
            <div id=\'content\'>
              <!--<h1 style="color:#FF0000" align="center">Login</h1>-->
              <div class="login-container">
                <form class="login-form" method="POST" action="resetPasswordInput.php">
                  <label class="login-labelHeader">Reset Your Account Password</label>
                  <label class="login-label2" for="password">Password:</label>
                  <input class="login-input2" required type="password" id="password" name="password" placeholder="Enter Your Password" value="'.$password.'">
                  <label class="login-label2" for="password">Confirm Password:</label>
                  <input class="login-input2" required type="password" id="password2" name="password2" placeholder="Enter Your Password" value="'.$password2.'">
                  <label style= "margin-left: 20px; color: #FF0000" id="error" name="error">'.$msg.'</label>
                  <div id="lower">
                  <input class="login-input" type="submit" style="width:126px;" name="Password Reset" value="Reset Password">
                  </div>
                </form>
              </div>
            </div>
          </body>
        </html>';
  }
  else//this page was accessed in an odd way or has finished resetting the users password. Redirecting to landing page.
  {
    echo "<script> location.href='Login.php'; </script>";
    exit;
  }
?>

<?php
  include 'includes/footer.php';
?>
