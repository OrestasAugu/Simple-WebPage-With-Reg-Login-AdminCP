<?php
// Include the file in which are set the object of the classes for Login, Register, Recover-data, and User-page
include('users.php');
//include header template
require('usrtempl/head.php');
?>


<div class="wrapper">
<div align="center">
	
	<div id="welcome" class="container">
    <link rel="stylesheet" type="text/css" href="usrtempl/style.css" />
<script src="usrjs/jquery_1.7.js" type="text/javascript"></script>
<div class="title">
	  <h2>Welcome to our website!</h2>
      <p><b>Good day,<br /> The email validation doesn't work, since somehow the soc server is blocking the smtp server, and the email with the activation link doesn't reach the user.<br /> So I turned off this feature for the registration.<br /> If you would like to login with the admin account, the email address is: default@default.com and the password is: westlondon123.<br />
Please read my report for more details: <a href="reportpdf.pdf">Read More</a></b></p>
		<!-- 
        SlideShow. Pictures are from pixabay.com
        -->
   <div class="fling-minislide">
   <align="center">
  <img src="images/arrow-1784155_960_720.png" alt="Slide 4"/>
  <img src="images/computer-2760136_960_720.jpg" alt="Slide 3"/>
  <img src="images/hard-disk-2634175_960_720.jpg" alt="Slide 2"/>
  <img src="images/mother-board-581597_960_720.jpg" alt="Slide 1"/>
</div>
		<p><strong>Kompfix is a young company, established by assesment in University of West London. All of our services can only be booked in advance by a phone call, or please register and use the query form which can be found in the Members area. We will get in touch with you as soon as possible.  </strong></p>
        <br />
       
	</div>
	</div>
    
<br/><br/>
Number of registered users: <?php echo $objUsers->users['total']; ?><br/>
Newest user: <?php echo $objUsers->users['last']; ?>
<h5>Online users:</h5> <?php echo $objUsers->users['online']; ?>
<?php
//include header template
require('usrtempl/footer.php');
?>
