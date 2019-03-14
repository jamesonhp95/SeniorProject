<?php
  session_start();
  require_once("../internalIncludes/dbconfig.php");
  include 'includes/header.php';
  include 'includes/site-header-loader.php';
  include 'includes/pageOverlay.php';
  //this is for if we need to have them accept the conditions, add this and then we can have a session variable changes on this page
  //<a href="https://somesite.com/" target="_blank">somesite.com</a>
?>
<!DOCTYPE html>
  <html>
  <head>
    <title>Terms And Conditions</title>
  </head>
  <body>
  <div id='content' style="padding: 20px;">
  <h1 style="text-align:center;color:red;">Terms of Service/Utilization Agreement</h1>
  <br><br>
           <p>1)&emsp;A platform administrator may, at any time, review and revoke a participants access. At any time, a participant may contact a platform administrator and request to be reconsidered. </p>
           <p>2)&emsp;This is a professional platform. As such, participants will, at all times, conduct themselves in a professional and respectful manner that adheres to the mission and values of this institution.</p>
           <p>3)&emsp;As a participant on this networking / professional development platform, I may be asked to share information about myself. Information requested must, at all times, be relevant to the discussion topic and held in the strictest confidence. Inappropriately requesting or sharing information will result in an immediate and irrevocable termination of ones account. Further action may be taken, as deemed necessary.</p>
           <p>4)&emsp;As a participant, I will respect the privacy and rights of all other participants.</p>                          
           <p>5)&emsp;A participant may, at any time, terminate the mentorship. </p>                                                               
           <p>6)&emsp;A participant may, at any time, contact an administrator and request a review of another participant, if they feel abuse of these terms is occurring.</p>
   </div>
  </body>
  </html>

<?php
  include 'includes/footer.php';
?>
