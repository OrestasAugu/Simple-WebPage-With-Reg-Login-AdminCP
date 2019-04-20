<!-- Form to delete message(s) -->
<form name="fdel" id="fdel" action="<?php echo USRINCLS; ?>msgs.php" method="post" target="adbox_ifr">
 <input type="hidden" name="id_dcm" id="id_dcm" />
 <input type="hidden" name="img_dcm" id="img_dcm" value="" />
 <input type="hidden" name="sbmt" value="delcmm" />
</form>
<?php
if(isset($_SESSION['rank']) && $_SESSION['rank']==9)
echo '<b><a href="'.USRINCLS.'latestmsgs.php" title="All new messages">'.$lsite['msgs']['newmsg'].'</a></b>';
?>