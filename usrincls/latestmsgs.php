<?php
include('config.php');
$new_reset = BASE.'usrimgup/ltmsgs_reset.txt';  // Store the time of last reset
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<title><?php echo $lsite['msgs']['newmsg']; ?></title>
<link href="<?php echo USRTEMPL; ?>usrpg.css" rel="stylesheet" type="text/css" />
<style type="text/css"><!--
.rosu {
  color:red;
}
#fnew {
  background-color:#f8f8fc;
  font-family:Calibri, sans-serif;
  font-size:16px;
  padding:3px;
}
--></style>
</head>
<body>
<center>
<?php
// Reset the time of last check, if data from form
 if(isset($_POST['pass_new']) && isset($_POST['timp_reset'])) {
  if ($_POST['pass_new'] == ADMINPASS) {
    if ((int)$_POST['timp_reset']>11111111) {
      // write data in $new_reset
      if (!file_put_contents($new_reset, intval($_POST['timp_reset']))) {
        print('<h3 class="rosu">'.$lsite['msgs']['eror_resetcheck'].'</h3>');
      }
    }
    else print '<h4 class="rosu"><u>'.$lsite['msgs']['noreset'].'</u></h4>';
  }
  else print '<h4 class="rosu"><u>'.$lsite['eror_pass'].'</u></h4>';
}

// gets the time of last reset, stored in new_reset.txt
$sec = intval(file_get_contents($new_reset));
$data_reset = date('j-F-Y, G:i ', $sec);
?>

<h2 id="coments_t"><?php echo $lsite['msgs']['newmsg']; ?></h2>
<div id="coments">
<?php
// create an object of Base class to perform SELECT query
$obj = new Base($mysql);
// Select new added comments
$sql = "SELECT * FROM `msgs` WHERE `dt`>$sec AND LENGTH(`msg`)>4 ORDER BY `id`";
$result = $obj->sqlExecute($sql);

echo '<p class="rosu"><u>'.$lsite['msgs']['lastreset'].'</u> : &nbsp; <b>'.$data_reset.'</b><br />
- <font color="blue">'.$lsite['msgs']['newmsgad'].' ('.$obj->affected_rows.')</font></p>';

if($result) {
  if($obj->affected_rows > 0) {
    for($i=0; $i<$obj->affected_rows; $i++) {
      $lastdt = $result[$i]['dt'];
      $result[$i]['msg'] = str_replace('<img src="icos/', '<img src="../icos/', $result[$i]['msg']);  
      $result[$i]['dt'] = date('j-F-Y, &\n\b\sp; G:i ', $result[$i]['dt']);
      $ad_link = '<span><a href="../users.php?usr='.$result[$i]['user'].'" target="_blank">'.sprintf($lsite['msgs']['usrpg'], $result[$i]['user']).'</a></span>';
      // Add user's website to name, if exists
      if (strlen($result[$i]['site'])>15) {
        $result[$i]['name'] = '<a href="'.$result[$i]['site'].'" id="n'.($i+1).'">'.$result[$i]['name'].'</a>';
      }
      // display comments
      echo "\r\n".'<div class="coms'.($i%2).'"><div class="n_coms">(<i>'.($i+1).'</i>) '.$result[$i]['name'].' </div> <div class="nr_coms"> '.$ad_link.' </div><span class="e_coms">'. $result[$i]['email']. '</span><em class="d_coms">'. $result[$i]['dt']. '</em> &nbsp; &nbsp; &nbsp; ';
      echo '<br /><div class="c_coms">'. str_replace('src="'.$imguprule['dir'], 'src="../'.$imguprule['dir'], $result[$i]['msg']). '</div></div><hr class="linie_coms"/>'."\r\n";
    }
  }
}
?>
</div><br />
<form action="" method="post" id="fnew">
  <h4><u><?php echo $lsite['msgs']['resetdt']; ?></u></h4>
  <input type="hidden" name="timp_reset" value="<?php echo $lastdt; ?>" />
  <b><i>Admin <?php echo $lsite['pass']; ?></i></b> <input type="password" name="pass_new" size="15" maxlength="40" /> &nbsp; 
  <input type="submit" value="Reset" />
</form><br />
</center>

</body>
</html>