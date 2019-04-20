<?php
// Sets variables with form fields that are defined according to permissions 1 or 0 set in "config.php"
$namec = isset($nameusr) ? '<input type="hidden" name="namec" value="'.$nameusr.'" /><label>'.$lsite['name'].':</label> &nbsp; &nbsp; &nbsp;&nbsp; <b>'.$nameusr.'</b><br/>' : '<label for="namec">'.$lsite['name'].'</label> &nbsp; &nbsp; &nbsp;&nbsp; <input type="text" name="namec" id="namec" size="20" maxlength="32" /><br/>';
$amail = (ALLOWMAIL===1)?'<input type="checkbox" name="amail" id="amail" value="2" /><label for="amail"> - <i>'.$lsite['msgs']['fcamail'].'</i></label><br/>':'';


$upimg = (ALLOWIMG===1)? $lsite['msgs']['fcupimg'].'<div class="eror">'.$lsite['max'].': '.$imguprule['width'].'/'.$imguprule['height'].' px, '.$imguprule['maxsize'].' KB, '. strtoupper(implode(', ', $imguprule['allowext'])).'</div><input type="file" name="upimg" /><br/><br/>':'';
?>
<!-- Form to add messages -->
<div id="fcom">
  <h3 id="show_formc"><?php echo $lsite['msgs']['addmsg']; ?></h3>
  <form name="formc" id="formc" method="post" action="<?php echo USRINCLS; ?>msgs.php" enctype="multipart/form-data" target="adbox_ifr">
  <!-- WebMaster courses http://coursesweb.net -->
    <input type="hidden" name="usr" value="<?php echo $objMsg->user; ?>" />
    <fieldset><legend align="right" id="formc_cls"><?php echo $lsite['close']; ?></legend>
<?php echo $namec; ?>
  <label for="emailc">E-mail: </label> &nbsp; &nbsp; &nbsp;
      <input type="text" name="emailc" id="emailc" size="23" maxlength="55" value="<?php if(isset($_SESSION['email'])) echo $_SESSION['email']; ?>" readonly="readonly" /><br/>
      <div id="afemail">
<?php
echo $amail;
echo '<input type="checkbox" name="showmail" id="showmail" value="1" /><label for="showmail"> - '.$lsite['msgs']['fcshowmail'].'</label>';
?></div><br/>
  <label for="coment"><?php echo $lsite['msgs']['chrmsg']; ?></label><br/>
  <div id="icos">
    <img src="<?php echo BASE; ?>icos/bold.png" border="0" alt="B" title="b" class="addtag" />
    <img src="<?php echo BASE; ?>icos/italic.png" border="0" alt="I" title="i" class="addtag" />
    <img src="<?php echo BASE; ?>icos/underline.png" border="0" alt="U" title="u" class="addtag" />
  <img src="<?php echo BASE; ?>icos/0.gif" alt=":)" title=":)" border="0" class="addsmile" />
  <img src="<?php echo BASE; ?>icos/1.gif" alt=":(" title=":(" border="0" class="addsmile" />
  <img src="<?php echo BASE; ?>icos/2.gif" alt=":P" title=":P" border="0" class="addsmile" />
  <img src="<?php echo BASE; ?>icos/3.gif" alt=":D" title=":D" border="0" class="addsmile" />
  <img src="<?php echo BASE; ?>icos/4.gif" alt=":S" title=":S" border="0" class="addsmile" />
  <img src="<?php echo BASE; ?>icos/5.gif" alt=":O" title=":O" border="0" class="addsmile" />
  <img src="<?php echo BASE; ?>icos/6.gif" alt=":=)" title=":=)" border="0" class="addsmile" />
  <img src="<?php echo BASE; ?>icos/7.gif" alt=":|H" title=":|H" border="0" class="addsmile" />
  <img src="<?php echo BASE; ?>icos/8.gif" alt=":X" title=":X" border="0" class="addsmile" />
  <img src="<?php echo BASE; ?>icos/9.gif" alt=":-*" title=":-*" border="0" class="addsmile" /></div>
  <textarea name="coment" id="coment" cols="41" rows="7"></textarea><br/>
  <?php echo $upimg; ?>
  <?php echo $lsite['codev0']; ?> &nbsp; <span id="codev0"><?php echo $objMsg->setCaptcha('codev'); ?></span><br/>
  <label for="codev"><?php echo $lsite['code']; ?></label><input type="text" name="codev" id="codev" size="5" maxlength="6" /><br/>
  </fieldset><br/>
  <input type="hidden" name="sbmt" value="<?php echo $lsite['msgs']['addmsg']; ?>" />
  <input type="submit" value="<?php echo $lsite['msgs']['addmsg']; ?>" id="addcomm" /><br/>
  </form>
</div>