<?php
  session_start();
  require_once("../internalIncludes/dbconfig.php");
  include 'includes/header.php';
  include 'includes/site-header-loader.php';
  include 'includes/pageOverlay.php';
  include 'includes/loginStyle.php'; 
  
  //checks for logged in user
  if(isset($_SESSION['user_id']))
  {
    if(isset($_SESSION['IsEnabled']) && $_SESSION['IsEnabled'] == 1)//checks if logged in user is enabled
    {
      if(isset($_SESSION['IsMentor']) && $_SESSION['IsMentor'] == 2)//checks if logged in user is an admin
      {
        echo "<script> window.location='AdminConsole.php'; </script>";
        exit;
      }
      else //user is logged in but not a mentor. Redirect to Login.php for redirects
      {
        echo "<script> window.location='Login.php'; </script>";
        exit;
      }
    }
    else
    {
      echo "<script> window.location='AccountDisabled.php'; </script>";
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
		  mysqli_free_result($r);
		  mysqli_next_result($conn);
      
      if($user_id >= 0)
		  {
			  $password = $_POST['password'];
        $sql = "CALL GetUserPassword('$user_id')";
        $q = $conn->query($sql);
        $r = $q->fetch_assoc();
			  $serverHash = $r['PasswordHash'];
        mysqli_free_result($r);
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
          $_SESSION['full_name'] = r['Name'];
          mysqli_free_result($r);
          mysqli_next_result($conn);
          
          echo "<script>location.href='AdminConsole.php';</script>";
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
  </head>
  <body>
  <div id='content'>
      <?php
        $username = $password = "";
      ?>
    <!--<h1 style="color:#FF0000" align="center">Login</h1>-->
    <div class="login-container">
    <form class="login-form" method="post" action="AdminLogin.php">
      <label class="login-label" for="username">Username:</label>
      <input class="login-input" required type="text" id="username" name="username" placeholder="Enter Your Username" value="<?php echo $username;?>">
      <label class="login-label" for="password">Password:</label>
      <input class="login-input" required type="password" id="password" name="password" placeholder="Enter Your Password" value="<?php echo $password;?>">
      <span style= "padding-left: 20px; color: #FF0000" id="error" name="error"><?php echo $error ?></span>
      <div id="lower">
      <input class="login-input" type="submit" value="login" name="login">
      </div>
    </form>
    </div>
  </div>
  </body>
  </html>
<?php
  include 'includes/footer.php';
?>
