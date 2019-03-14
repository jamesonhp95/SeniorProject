<?php
session_start();
unset($_SESSION['user_id']);
unset($_SESSION['IsEnabled']);
unset($_SESSION['IsMentor']);
unset($_SESSION['full_name']);
session_destroy();
echo "<script> location.href='LandingPage.php'; </script>";
exit;
?>
