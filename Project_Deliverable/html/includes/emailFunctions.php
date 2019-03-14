<?PHP
  require_once "Mail.php";
  
  //used to inform mentor their email was verified and it is now under review by an admin
  function sendMentorCreationNotification($toEmail) {
		include "../internalIncludes/dbconfig.php";
		#Used for error checking
		#ini_set('display_errors','on');
		#error_reporting(E_ALL);    
    
    $call = "CALL GetAdminEmailCredentials";
        $q = $conn->query($call);
        $r = $q->fetch_assoc();
        $EmailAddress = $r['EmailAddress'];
        $Password = $r['Password'];
        mysqli_next_result($conn);
    
    
		$from = $EmailAddress;
    $to = $toEmail;
		$subject = "Mentor Application – Email Verification Complete";
		$message = "
		<html>
		<head>
		<title>EWU Mentor Notification</title>
		</head>
		<body>
		<b>Thank you for verifying your EWU Mentorship Program email – we do this for your protection!</b>
		<br>
		<p>Your account is currently under review by an administrator. After it has been approved, you will get a notification email with instructions on how to log in. This review process can take up to 24 hours, but you may contact Career Services at 509.359.6365 to request expedited consideration.</p>
		<p>Thank you for your patience.</p>
		</body>
		</html>";
		$host = "smtp.office365.com";
		$port = "587";
		$username = $EmailAddress;
		$password = $Password;
    $content = "text/html; charset=utf-8";
    $mime = "1.0";
            
		$headers = array ('From' => $from,
		  'To' => $to,
		  'Subject' => $subject,
      'MIME-Version' => $mime,
      'Content-type' => $content);
      
		$smtp = Mail::factory('smtp',
		  array ('host' => $host,
			'port' => $port,
			'auth' => true,
			'username' => $username,
			'password' => $password));
      
		$mail = $smtp->send($to, $headers, $message);
    
    #Used for testing. Must be disabled for final product
    /*
		if (PEAR::isError($mail)) {
		  echo("<p>" . $mail->getMessage() . "</p>");
		 } else {
		  echo("<p>Message successfully sent!</p>");
		 }
     */
	}
  
  //used to test if admin email credentials are properly set.
  function sendAdminTestEmail() {
		include "../internalIncludes/dbconfig.php";
    
    $call = "CALL GetAdminEmailCredentials";
        $q = $conn->query($call);
        $r = $q->fetch_assoc();
        $EmailAddress = $r['EmailAddress'];
        $Password = $r['Password'];
        mysqli_next_result($conn);
    
    
		$from = $EmailAddress;
    $to = $EmailAddress;
		$subject = "Mentorship Program Admin Test Email";
		$message = "
		<html>
		<head>
		<title>This is an automated test email.</title>
		</head>
		<body>
		<b>This is an automated test email.</b>
		<br>
		<p>If you are seeing this email, then the Admin email credentials used for the Mentorship program's automated emails are set correctly. This is the email address all automated emails will be sent from. You can change this from the Admin Console.</p>
		</body>
		</html>";
		$host = "smtp.office365.com";
		$port = "587";
		$username = $EmailAddress;
		$password = $Password;
    $content = "text/html; charset=utf-8";
    $mime = "1.0";
            
		$headers = array ('From' => $from,
		  'To' => $to,
		  'Subject' => $subject,
      'MIME-Version' => $mime,
      'Content-type' => $content);
      
		$smtp = Mail::factory('smtp',
		  array ('host' => $host,
			'port' => $port,
			'auth' => true,
			'username' => $username,
			'password' => $password));
      
		$mail = $smtp->send($to, $headers, $message);
	}
  
  //used to verify that an entered email is real and the user is in control of it.
  function sendEmailVerification($toEmail, $hash) {
    include "../internalIncludes/dbconfig.php";   
    
    $call = "CALL GetAdminEmailCredentials";
        $q = $conn->query($call);
        $r = $q->fetch_assoc();
        $EmailAddress = $r['EmailAddress'];
        $Password = $r['Password'];
        mysqli_next_result($conn);
    
		$from = $EmailAddress;
    $to = $toEmail;
		$subject = "Mentor Account Validation";
		$message = "
		<html>
		<head>
		<title>EWU Mentor Notification</title>
		</head>
		<body>
		<b>Thank you for signing up to be a mentor as part of the EWU Mentorship Program.</b>
		<br>
		<p>Please note – this is a two-step verification process. Once you verify your account, it will need to be approved by an administrator before you can access your account. If you registered to create Mentor Profile, please click the link below to verify your account. If you did not request this email, please call Career Services at 509.359.6465. Thank you.</p>
		<p><a href=\"http://146.187.134.42/verify.php?email=".$toEmail."&hash=".$hash."\">Verify Email</a>
		</body>
		</html>";
		$host = "smtp.office365.com";
		$port = "587";
		$username = $EmailAddress;
		$password = $Password;
    $content = "text/html; charset=utf-8";
    $mime = "1.0";
            
		$headers = array ('From' => $from,
		  'To' => $to,
		  'Subject' => $subject,
      'MIME-Version' => $mime,
      'Content-type' => $content);
      
		$smtp = Mail::factory('smtp',
		  array ('host' => $host,
			'port' => $port,
			'auth' => true,
			'username' => $username,
			'password' => $password));
      
		$mail = $smtp->send($to, $headers, $message);
	}
	
  //used to inform mentor they have been approved by an admin
  function sendMentorApprovalNotification($toEmail) {
    include "../internalIncludes/dbconfig.php";  
    
    $call = "CALL GetAdminEmailCredentials";
        $q = $conn->query($call);
        $r = $q->fetch_assoc();
        $EmailAddress = $r['EmailAddress'];
        $Password = $r['Password'];
        mysqli_next_result($conn);
  
    $from = $EmailAddress;
    $to = $toEmail;
    $subject = "Mentor Application – Account Approved";
    $message = "
    <html>
    <head>
    <title>Application Status</title>
    </head>
    <body>
    <b>Congratulations!</b>
    <br>
    <p>Your account has been approved! You may now sign in by using the following <a href=\"http://146.187.134.42/Login.php\">link</a> and begin working with students. In order to facilitate the best mentorship connections, please complete your profile as much as possible and keep it up to date. If you have any questions or concerns, you may contact Career Services at 509.359.6365 at any time.</p>
    <p>Thank you for participating in our EWU Mentorship Program.</p>
    </body>
    </html>";
    $host = "smtp.office365.com";
    $port = "587";
    $username = $EmailAddress;
    $password = $Password;
    $content = "text/html; charset=utf-8";
    $mime = "1.0";
      
    $headers = array ('From' => $from,
      'To' => $to,
      'Subject' => $subject,
      'MIME-Version' => $mime,
      'Content-type' => $content);
    
    $smtp = Mail::factory('smtp',
      array ('host' => $host,
      'port' => $port,
      'auth' => true,
      'username' => $username,
      'password' => $password));
    
    $mail = $smtp->send($to, $headers, $message);
  }
  
  //used to inform mentor they have been denied by an admin
  function sendMentorDenialNotification($toEmail) {
    include "../internalIncludes/dbconfig.php";
    
    $call = "CALL GetAdminEmailCredentials";
        $q = $conn->query($call);
        $r = $q->fetch_assoc();
        $EmailAddress = $r['EmailAddress'];
        $Password = $r['Password'];
        mysqli_next_result($conn);
  
    $from = $EmailAddress;
    $to = $toEmail;
    $subject = "Application Status";
    $message = "
    <html>
    <head>
    <title>EWU Mentor Notification</title>
    </head>
    <body>
    <br>
    <p>Thank you for applying to participate in the EWU Mentorship Program. In review of your request to create an account, our administrators found one or more “red flags” that indicate this could be an inappropriate or fraudulent account. If you this decision has been reached in error and you would like to request further review, please contact Career Services at 509.359.6365.</p>
    </body>
    </html>";
    $host = "smtp.office365.com";
    $port = "587";
    $username = $EmailAddress;
    $password = $Password;
    $content = "text/html; charset=utf-8";
    $mime = "1.0";
      
    $headers = array ('From' => $from,
      'To' => $to,
      'Subject' => $subject,
      'MIME-Version' => $mime,
      'Content-type' => $content);
    
    $smtp = Mail::factory('smtp',
      array ('host' => $host,
      'port' => $port,
      'auth' => true,
      'username' => $username,
      'password' => $password));
    
    $mail = $smtp->send($to, $headers, $message);
  }
  
  //used to verify that a mentor's requested new email is real and the user controls it
  function sendMentorEmailChangeRequest($toEmail, $hash, $oldemail) {
    include "../internalIncludes/dbconfig.php";
    
    $call = "CALL GetAdminEmailCredentials";
        $q = $conn->query($call);
        $r = $q->fetch_assoc();
        $EmailAddress = $r['EmailAddress'];
        $Password = $r['Password'];
        mysqli_next_result($conn);
  
    $from = $EmailAddress;
    $to = $toEmail;
    $subject = "Mentor Account Validation";
    $message = "
    <html>
    <head>
    <title>EWU Mentor Notification</title>
    </head>
    <body>
    <b>You have requested to update your EWU Mentor account email.</b>
		<br>
		<p>If you registered to change you Mentor Profile email, please click the link below to verify your account. If you did not request this email, please call Career Services at 509.359.6465. Thank you.</p>
		<p><a href=\"http://146.187.134.42/verifyNewEmail.php?email=".$toEmail."&hash=".$hash."&oldemail=".$oldemail."\">Verify Email</a>
    </body>
    </html>";
    $host = "smtp.office365.com";
    $port = "587";
    $username = $EmailAddress;
    $password = $Password;
    $content = "text/html; charset=utf-8";
    $mime = "1.0";
      
    $headers = array ('From' => $from,
      'To' => $to,
      'Subject' => $subject,
      'MIME-Version' => $mime,
      'Content-type' => $content);
    
    $smtp = Mail::factory('smtp',
      array ('host' => $host,
      'port' => $port,
      'auth' => true,
      'username' => $username,
      'password' => $password));
    
    $mail = $smtp->send($to, $headers, $message);
  }
  
  //used to inform mentor's original email that the email address tied to their mentor account was changed to "Insert new email here"
  function sendMentorEmailChangeNotice($toEmail, $newEmail) {
    include "../internalIncludes/dbconfig.php";
    
    $call = "CALL GetAdminEmailCredentials";
        $q = $conn->query($call);
        $r = $q->fetch_assoc();
        $EmailAddress = $r['EmailAddress'];
        $Password = $r['Password'];
        mysqli_next_result($conn);
  
    $from = $EmailAddress;
    $to = $toEmail;
    $subject = "Mentor Account Email Change Notification";
    $message = "
    <html>
    <head>
    <title>EWU Mentor Notification</title>
    </head>
    <body>
    <br>
    <p>You’re receiving this email because you recently requested to update your EWU Mentor account email. A verification message has been sent to ".$newEmail.". If you did not request to change your email, please contact Career Services immediately at 509.259.6365.</p>
    </body>
    </html>";
    $host = "smtp.office365.com";
    $port = "587";
    $username = $EmailAddress;
    $password = $Password;
    $content = "text/html; charset=utf-8";
    $mime = "1.0";
      
    $headers = array ('From' => $from,
      'To' => $to,
      'Subject' => $subject,
      'MIME-Version' => $mime,
      'Content-type' => $content);
    
    $smtp = Mail::factory('smtp',
      array ('host' => $host,
      'port' => $port,
      'auth' => true,
      'username' => $username,
      'password' => $password));
    
    $mail = $smtp->send($to, $headers, $message);
  }
  
  //used to inform mentors new email that their email account was updated to their profile
  function sendMentorEmailChangeNotification($toEmail) {
		include "../internalIncludes/dbconfig.php";
		#Used for error checking
		#ini_set('display_errors','on');
		#error_reporting(E_ALL);    
   
    $call = "CALL GetAdminEmailCredentials";
        $q = $conn->query($call);
        $r = $q->fetch_assoc();
        $EmailAddress = $r['EmailAddress'];
        $Password = $r['Password'];
        mysqli_next_result($conn);
    
    
		$from = $EmailAddress;
    $to = $toEmail;
		$subject = "Mentor Application – Email Verification Complete";
		$message = "
		<html>
		<head>
		<title>EWU Mentor Notification</title>
		</head>
		<body>
		<b>Thank you for verifying your EWU Mentorship Program email – we do this for your protection!</b>
		<br>
		<p>Your account is has now been updated. This will now be your primary email and login info for the EWU Mentor Program.</p>
    <p>Thank you</p>
		</body>
		</html>";
		$host = "smtp.office365.com";
		$port = "587";
		$username = $EmailAddress;
		$password = $Password;
    $content = "text/html; charset=utf-8";
    $mime = "1.0";
            
		$headers = array ('From' => $from,
		  'To' => $to,
		  'Subject' => $subject,
      'MIME-Version' => $mime,
      'Content-type' => $content);
      
		$smtp = Mail::factory('smtp',
		  array ('host' => $host,
			'port' => $port,
			'auth' => true,
			'username' => $username,
			'password' => $password));
      
		$mail = $smtp->send($to, $headers, $message);
    
    #Used for testing. Must be disabled for final product
    /*
		if (PEAR::isError($mail)) {
		  echo("<p>" . $mail->getMessage() . "</p>");
		 } else {
		  echo("<p>Message successfully sent!</p>");
		 }
     */
	}
  
  //used to reset Mentor/Admin type accounts passwords
  function sendMentorEmailPasswordReset($toEmail, $hash) {
    include "../internalIncludes/dbconfig.php";
    
    $call = "CALL GetAdminEmailCredentials";
        $q = $conn->query($call);
        $r = $q->fetch_assoc();
        $EmailAddress = $r['EmailAddress'];
        $Password = $r['Password'];
        mysqli_next_result($conn);
  
    $from = $EmailAddress;
    $to = $toEmail;
    $subject = "Mentor Account Password Reset Request";
    $message = "
    <html>
    <head>
    <title>EWU Mentor Notification</title>
    </head>
    <body>
    <p>You’re receiving this email because you recently requested to update your EWU Mentor account password. You may do so by following the link below. If you did not request to create a new password, please contact Career Services as soon as possible.</p>
		<p><a href=\"http://146.187.134.42/resetPasswordInput.php?email=".$toEmail."&hash=".$hash."\">Reset Password</a>
    </body>
    </html>";
    $host = "smtp.office365.com";
    $port = "587";
    $username = $EmailAddress;
    $password = $Password;
    $content = "text/html; charset=utf-8";
    $mime = "1.0";
      
    $headers = array ('From' => $from,
      'To' => $to,
      'Subject' => $subject,
      'MIME-Version' => $mime,
      'Content-type' => $content);
    
    $smtp = Mail::factory('smtp',
      array ('host' => $host,
      'port' => $port,
      'auth' => true,
      'username' => $username,
      'password' => $password));
    
    $mail = $smtp->send($to, $headers, $message);
  }
  
  
  
  
  
  //old style
	function sendTestEmail() {
		$to = "Prince_of_1war@hotmail.com";
		$subject = "Test email";
		
		$message = "
		<html>
		<head>
		<title>EWU Mentorship Notification</title>
		</head>
		<body>
		<b>Thank you for signing up to be a mentor as part of the EWU Mentorship Program.</b>
		<br>
		<p>Your account is currently under review. After it has been activated, you will get a notification email with instructions on how to log in.</p>
		<p>Thank you for your patience.
		<br><br><br>
		<p> ^This was an automated message sent from our server. How does it look to you?</p>
		</body>
		</html>";

		// Always set content-type when sending HTML email
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		$headers .= 'From: <jordancaraway@eagles.ewu.edu>' . "\r\n";
		
		mail($to,$subject,$message,$headers);
	}
?>