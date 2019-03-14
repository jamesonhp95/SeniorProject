<html>
<?php
session_start();
include 'includes/pageOverlay.php';
include 'includes/header.php';
include 'includes/site-header-loader.php';
?>
<body>
  <div id="demo" class="carousel slide" data-ride="carousel" style="margin-left: auto; margin-right: auto; height: 75% !important; width: 65% !important;">
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="Images/slide1.png" width="100%" height="100%">   
    </div>
    <div class="carousel-item">
      <img src="Images/slide2.jpg" width="100%" height="100%">   
    </div>
    <div class="carousel-item">
	<!-- used to be width: 650; height: 300; -->
      <img src="Images/slide3.jpg" width="100%" height="100%">  
    </div>
  </div>
  <a class="carousel-control-prev" href="#demo" data-slide="prev">
    <span class="carousel-control-prev-icon"></span>
  </a>
  <a class="carousel-control-next" href="#demo" data-slide="next">
    <span class="carousel-control-next-icon"></span>
  </a>
</div>
	<h3 style="margin-left: auto; margin-right: auto; color:white; width:15%; font-style:italic;">Mentee Stories</h3>
        <p style="font-style:italic; margin-left: auto; margin-right: auto; color:white; width:50%;">"   It has been most interesting speaking with an accountant hands on because I don't know anyone family or friend wise who is an accountant. It is nice to be able to ask questions and learn about the specifics in all aspects to the accounting field."</p>
       <br>
        <p style="font-style:italic; margin-left: auto; margin-right: auto; color:white; width:50%;">"   I've found this [mentoring] program to be very helpful! It's awesome to get a professional's advice on preparing career materials and a buffer for networking events. I've been highly recommending the [mentoring] program to my peers :)"</p>
       <br>
        <p style="font-style:italic; margin-left: auto; margin-right: auto; color:white; width:50%;">"   Practice with interviews is very valuable since that isn't really my strong suit. I'm always amazed at how many resources (blogs, websites, articles) that Rebecca can find on the topics we're focusing on. We've mostly discussed interviewing and then had a mock interview session with feedback. Both mentors were involved and they videotaped it so we can see our nervous tics, body language, etc."</p>
       
  </body>


<?php
include 'includes/footer.php';
?>
</html>
