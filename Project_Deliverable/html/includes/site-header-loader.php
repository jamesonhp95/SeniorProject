<?php
if(isset($_SESSION['IsMentor']))
{
  if($_SESSION['IsMentor'] == 0)
  {
    //load mentee header
    include 'site-header-mentee.php';
  }
  elseif($_SESSION['IsMentor'] == 1)
  {
    //load mentor header
    include 'site-header-mentor.php';
  }
  elseif($_SESSION['IsMentor'] == 2)
  {
    //load mentor header
    include 'site-header-admin.php';
  }
} else
{
    //load generic header
    include 'site-header.php';
}
 ?>
 