<?php
$globalSearchInput="";
echo '
<head>

<title>Mentorship Program</title>

<link rel="icon" href="Images/favicon.ico">
<link rel="stylesheet" type="text/css" href="styles/style.css">
<link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity=" sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<!--<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>-->
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
</head>

        <header class="global-header">
            <input class="menu-btn" type="checkbox" id="menu-btn" />
            <label class="menu-icon" for="menu-btn"><span class="navicon"></span></label>
            
            <ul>
                <a class="navbar-brand-mobile" href="https://www.ewu.edu/">
                    <img class="mobile-eagle" alt="EWU logo" src="Images/eagle-white.svg">
                </a>
            </ul>
            <ul class="menu container">
                <ul>
                    <li>
                        <a class="navbar-brand-desktop top-eagle" href="https://www.ewu.edu/">
                        </a>
                    </li>
                    <hr class="hr-mobile">
                    <li class="global-search-mobile">
			<form class="navbar-form navbar-left" action="" method="POST">
                            <div class="form-group">
                                <input type="text" class="search-mobile-input" placeholder="Search EWU" name="globalSearchInput" value="">
                                <button type="submit" class="btn btn-default global-search-button" name="BtnSubmit"><span class="fa fa-search" alt="ewu-search-button"></span></button>
                            </div>
                        </form>
                    </li>
                    <li><a href="https://www.ewu.edu/apply/">Apply</a></li>
                    <li><a href="https://www.ewu.edu/academics/">Academics</a></li>
                    <li><a href="http://goeags.com/">Athletics</a></li>
                    <li><a href="https://www.ewu.edu/campus-events/">Calendar</a></li>
                    <li><a href="https://www.ewu.edu/community">Community</a></li>
                    <li><a href="https://www.ewu.edu/about/">About</a></li>
                    <li><a href="https://my.ewu.edu">MyEWU</a></li>
                    <li><a href="https://canvas.ewu.edu/">Canvas</a></li>
                    <li class="global-search-desktop">
			<form action="https://sites.ewu.edu/search/?q='.urlencode($globalSearchInput).'" method="POST">
                            <div class="form-group">
                                <input type="text" class="search-desktop-input" placeholder="Search EWU" name="globalSearchInput" value="">
                                <button type="submit" class="global-search-button" name="BtnSubmit"><span class="fa fa-search" alt="ewu-search-button"></span></button>
                            </div>
                            
                            
                        </form>
                        
                    </li>
                    <li>
                    
                    </li>
                    
                </ul>
                
            </ul>
            
        </header>
        
';      
?>
