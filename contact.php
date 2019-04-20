<?php
// Include the file in which are set the object of the classes for Login, Register, Recover-data, and User-page
include('users.php');
//include header template
require('usrtempl/head.php');
?>


<div align="center">
<div class="wrapper" id="tbox1">
<div class="title">

	  <h2>Contact Information</h2>
		</div>
        </div>
        <div align="center">
        <div id="footer">
               <!--
I have found some beautiful icons from the www.fontawesome.github.com, there were a lot more but I just used couple of them, since I didin't want to make my website full of icons. These ones for the contact me were quite styling. 
I had also writted the credits to the fontawesome in the fonts.css
-->
	<div class="container">
		<div class="fbox1">
		<span class="icon icon-map-marker"></span>
			<span>5 The Boulevard, Flat 14
			<br />Crawley, West Sussex, RH101UR</span>
		</div>
		<div class="fbox1">
			<span class="icon icon-phone"></span>
			<span>
				Telephone: +447491293839
			</span>
		</div>
		<div class="fbox1">
			<span class="icon icon-envelope"></span>
			<span>kompfix@gmail.com</span>
		</div>
        
	</div>
        
        
        
        </div>
</div> 
</div>  
<?php
//include header template
require('usrtempl/footer.php');
?>
