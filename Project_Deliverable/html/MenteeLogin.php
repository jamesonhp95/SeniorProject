<?php 
  session_start();
  require_once("../internalIncludes/dbconfig.php");
  ini_set('display_errors','on');
	error_reporting(E_ALL);
  if(isset($_SESSION['user_id'])) //if user is already logged in
  {
    //redirect to somewhere because they are already logged in. Login.php handles redirects
    echo "<script> location.href='Login.php'; </script>";
    exit();
  }
?>

<?php
  require_once "./CAS/CAS.php";

  error_reporting(E_ALL & ~E_NOTICE);

  /* CAS Protocol Configuration */
  $cas_host = "login.ewu.edu";
  $cas_port = 443;
  $cas_context = "/cas";
  $cas_server_ca_cert_path = "./CAS/STAR_ewu.edu.ca";

  phpCAS::client(SAML_VERSION_1_1, $cas_host, $cas_port, $cas_context);
  error_reporting(E_ALL & ~E_NOTICE);
  //phpCAS::client(SAML_VERSION_1_1, $cas_host, $cas_port, $cas_context);

  //* Don't validate the CAS Server
//   phpCAS::setDebug();
   phpCAS::setNoCasServerValidation();

  // Set the cert path
  error_reporting(E_ALL & ~E_NOTICE);
  //phpCAS::setCasServerCACert($cas_server_ca_cert_path);

  // DO CAS Authentication
  error_reporting(E_ALL & ~E_NOTICE);
  phpCAS::forceAuthentication();

  error_reporting(E_ALL & ~E_NOTICE);
  $user = phpCAS::getUser();
  $attributes = phpCAS::getAttributes();
  /*
  Notes:
  $user = phpCAS:GetUser();                   fetches user object and stores into $user
  print $user;                                prints the netid of sso user;
  $attributes = phpCAS::getAttributes();      fetches user atributes and stores into $attribues as an array
    UserType =>                               Student or Staff or etc
    Email =>                                  
    FirstName =>
    Ewuid =>                                  ewu id number
    LastName =>
  
   echo print_r($attributes);                 prints array of attributes with their names and values
  */
  
  //start edits
  if(strcmp('Student',$attributes['UserType']) == 0  && $attributes['UserType'] != NULL) //sso user is a student
  {
    $username = $user;
    $ewuID = $attributes['Ewuid'];
	//echo print_r($_SESSION);
    
    //check for a mentee already existing with that ewuID
    $sql = "CALL GetMenteeUserID_EwuID('$ewuID');";
    $q = $conn->query($sql);
    $r = $q->fetch_assoc();
		$user_id = $r['UserID'];
		mysqli_free_result($q);
		mysqli_next_result($conn);
    
    if($user_id == -1) //user has not been created with that userName
    {
      $firstname = $attributes['FirstName'];
      $lastname = $attributes['LastName'];
      $email = $attributes['Email'];
      
      //create mentee
      $sql = "CALL CreateMentee('$firstname','$lastname','$email','$username','$ewuID');";
      $q = $conn->query($sql);
      $r = $q->fetch_assoc();
      $user_id = $r['UserID'];
      mysqli_free_result($q);
      mysqli_next_result($conn);
      //print $user_id;      
    }
    
    //set session variables for mentee
    $sql = "CALL GetUser('$user_id')";
    $q = $conn->query($sql);
    $r = $q->fetch_assoc();
    $_SESSION['user_id'] = $r['UserID'];
    $_SESSION['IsMentor'] = $r['IsMentor'];
    $_SESSION['IsEnabled'] = $r['IsEnabled'];
    $_SESSION['full_name'] = $r['Name'];
    mysqli_free_result($q);
    mysqli_next_result($conn);
    //redirect to Login.php which handles all redirects
    echo "<script> location.href='Login.php'; </script>";
    exit;
  }
    
    //SSO user is NOT a student and no user is logged in
    //echo "<script> location.href='Login.php'; </script>";
    //exit;
    echo "You are logged in as a non-student SSO and no mentee account was created for you.<br>Please go   <a href=\"http://146.187.134.42/Login.php\"Here</a>   to log in as a mentor.";

  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
