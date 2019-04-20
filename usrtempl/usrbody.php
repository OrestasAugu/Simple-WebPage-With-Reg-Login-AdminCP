<?php
include(USRINCLS.'msgs.php');        // the part with comments /messages in user page

// start the variable with the html code for the user page
$usrhtml = '<section id="center"><div id="usrdat">';

// if Admin, Rank 9 (but not firs Admin, id=1), show to can change user`s rank, and Delete user
if($usrdat['idusr']>1 && isset($_SESSION['rank']) && $_SESSION['rank']==9) {
  $usrhtml .= '<div id="setrankdel"><b id="setrankms">'.$lsite['userpage']['setrank'].': '. $functions->setRank('users', $usrdat['rank'], $usrdat['idusr'], 'setrank', 'setrankms').'</b> - '.  $lsite['userpage']['setrankmsg'].'<br/><input type="checkbox" name="delusr" value='.$usrdat['idusr'].' id="delusr0" class="delusr" /><label for="delusr0">'.$lsite['delete'].'</label> <button onclick="delUsers();">'.$lsite['delusr'].'</button></div>';
}

// adds Span tags to user data if has value, to be designed with css
$usrdtreg = strlen($usrdat['dtreg'])>1 ? '<span class="spndat">'. $usrdat['dtreg']. '</span>' : '';
$usrdtvisit = strlen($usrdat['dtvisit'])>1 ? '<span class="spndat">'. $usrdat['dtvisit']. '</span>' : '';
$usrvisits = '<span class="spndat">'. $usrdat['visits']. '</span>';
$usrym = strlen($usrdat['usrym'])>1 ? '<span class="spndat">'. $usrdat['usrym']. '</span>' : '';
$usrmsn = strlen($usrdat['usrmsn'])>1 ? '<span class="spndat">'. $usrdat['usrmsn']. '</span>' : '';
$usrsite = strlen($usrdat['usrsite'])>1 ? '<span id="usrwebsite" class="spndat">'. $usrdat['usrsite']. '</span>' : '';
$usrname = strlen($usrdat['usrname'])>1 ? '<span class="spndat">'. $usrdat['usrname']. '</span>' : '';
$usrpronoun = strlen($usrdat['usrpronoun'])>1 ? '<span class="spndat">'. $usrdat['usrpronoun']. '</span>' : '';
$usrbday = strlen($usrdat['bday'])>1 ? '<span class="spndat">'. $usrdat['bday']. '</span>' : '';
$usrcountry = strlen($usrdat['country'])>1 ? '<span class="spndat">'. $usrdat['country']. '</span>' : '';
$usrcity = strlen($usrdat['city'])>1 ? '<span class="spndat">'. $usrdat['city']. '</span>' : '';
$usradres = strlen($usrdat['adres'])>1 ? '<i>'. $usrdat['adres']. '</i>' : '';

$usrhtml .= '<img src="'. $usrdat['imgusr']. '" alt="Image '. $usr. '" id="imgusr" class="fl" />
 <div id="datusr">
  <div class="fl">'.
   $lsite['userpage']['dtreg'].': '. $usrdtreg. '<br/>'.
   $lsite['userpage']['dtvisit'].':'. $usrdtvisit. '<br/>'.
   $lsite['userpage']['visits']. $usrvisits. '<br/><br/>
   <h5>- Contact -</h5>
   Yahoo Messenger: '. $usrym. '<br/>
   MSN Messenger: '. $usrmsn. '<br/>
   Web Site: '. $usrsite.
  '</div>
  <div class="fr">'.
   $lsite['name'].': '.$usrname.'<br/>'.
   $lsite['pronoun'].$usrpronoun.'<br/>'.
   $lsite['birthday']. $usrbday.
   '<h5>'.$lsite['location'].'</h5>
   <ul>
    <li>'.$lsite['country'].$usrcountry.'</li>
    <li>'.$lsite['city'].$usrcity.'</li>
    <li>'.$lsite['address']. $usradres. '</li>
   </ul>
  </div><br class="clr" />
 </div>';

// If Admin, or its the page of the logged user ($_SESSION['usritspage'] is 1, defined in "headphp"), add image upload
if(isset($_SESSION['usritspage']) && $_SESSION['usritspage']===1)  {
 $usrhtml .= '<form action="" id="usrupimg" enctype="multipart/form-data" target="sendimg" method="post">
  <input type="hidden" name="idusr" value="'. $usrdat['idusr']. '" />
  <input type="hidden" name="isajax" value="1" />
  (<i>'.$lsite['max'].': '.$imguprule['width'].'/'.$imguprule['height'].' px, '.$imguprule['maxsize'].' KB, '. strtoupper(implode(', ', $imguprule['allowext'])).'</i>)<br/>
  <label for="usrimg">'.$lsite['userpage']['adimg'].'</label> <input type="file" id="usrimg" name="usrimg" onchange="checkName(this, \'supimg\')" />
  <input type="submit" name="susr" id="supimg" value="Upload" disabled="disabled" />
 </form><div id="ifrmup"></div>
 <button id="forupimg">'.$lsite['userpage']['forupimg'].'</button>';
}
$usrhtml .= '</div>
 <aside id="rightusr"><div id="userss">'.
$lsite['userpage']['totalusr'].'<span class="sb">'.$objUsers->users['total'].'</span><br/>'.
$lsite['userpage']['newusr'].$objUsers->users['last'].
 '<h5>'.$lsite['userpage']['online'].'</h5>
 <div id="useron">'.$objUsers->users['online'].'</div>
</div></aside>
<ul id="ultabs">
 <li title="dateopt" class="etabvi">'.$lsite['userpage']['usrdata'].'</li>
 <li title="coment_tag">'.$lsite['msgs']['title'].' ('.$objMsg->totalrows.')</li>
 <li title="favorites">'.$lsite['favorites'].'</li>';

// If Admin, or its the page of the logged user ($_SESSION['usritspage'] is 1), add LI to show edit forms
if(isset($_SESSION['usritspage']) && $_SESSION['usritspage']===1) {
$usrhtml .= ' <li title="usrform1">'.$lsite['userpage']['changeep'].'</li>
 <li title="usrform2">'.$lsite['userpage']['editopt'].'</li> ';
}

// continue ading html with data in $usrhtml
$usrhtml .= '
</ul><div id="usrmod" class="eror"></div><div id="dateopt">
 <h3 class="usrh3">'.$lsite['userpage']['ocupation'].'</h3>
 <div class="usropt">'. nl2br($usrdat['ocupation']). '</div>
 <h3 class="usrh3">'.$lsite['userpage']['interes'].'</h3>
 <div class="usropt">'. nl2br($usrdat['interes']). '</div>
 <h3 class="usrh3">'.$lsite['userpage']['transmit'].'</h3>
 <div class="usropt">'. nl2br($usrdat['transmit']). '</div>
</div>
<div id="coment_tag"><h3>'.$lsite['msgs']['title2'].'</h3>'.$msgs.'</div>
<div id="favorites">'.$favorites.'</div>';      // adds form to add favorite, and favorite links in Div

// If its the page of the logged user, or Admin, adds forms for editing users data
if(isset($_SESSION['usritspage']) && $_SESSION['usritspage']===1)  {
  // this function define <option> tags for the Select birthday
  function setBdayOpt($nr, $nrlast, $check) {
    $re = '<option>--</option>';
    for($i=$nr; $i<$nrlast; $i++) {
      // adds the "selected" attribute
      if($i==$check) {
        $re .= '<option value="'.$i.'" selected="selected">'.$i.'</option>';
        continue;
      }
      $re .= '<option value="'.$i.'">'.$i.'</option>';
    }
    return $re;
  }

  // gets the day, month, and yers set for birthday (to be selected in <option>), and the current year
  $ar_bday = strtotime($usrdat['bday']) ? getdate(strtotime($usrdat['bday'])) : array('mday'=>0, 'mon'=>0, 'year'=>0);
  $set_zi = $ar_bday['mday'];
  $set_luna = $ar_bday['mon'];
  $set_an = $ar_bday['year'];
  $acum_an = date('Y')+1;

  $usrhtml .= '<form action="" method="post" id="usrform1" onsubmit="return usrModf(this);">
  <input type="hidden" name="usr" value="'. $usrdat['usr']. '" />
 <h4>'.$lsite['userpage']['editreg'].'</h4>
 <label for="pass">'.$lsite['userpage']['pass'].'</label> <input type="password" size="18" maxlength="32" name="pass" id="pass" /><br/>
 <label for="passnew">'.$lsite['userpage']['passnew'].'</label> <input type="password" size="18" maxlength="32" name="passnew" id="passnew" /><br/>
 <label for="email">E-mail:</label> <input type="text" size="18" maxlength="32" name="email" id="email" value="'.$usrdat['usrmail'].'" /><br/>';

 // if RANK, set in 'config.php' is 0, add info about changing the email
 if(RANK == 0) $usrhtml .= '<b>'. $lsite['userpage']['chgmail'] .'</b><br/>';
 $usrhtml .= '<input type="submit" name="susr" value="'.$lsite['modify'].'" />
</form>

<form action="" method="post" id="usrform2">
<h4>'.$lsite['userpage']['optionals'].'</h4>
 <input type="hidden" name="usr" value="'. $usrdat['usr']. '" />
 <input type="hidden" name="idusr" value="'. $usrdat['idusr']. '" />
 <label for="usrname">'.$lsite['name'].'</label> <input type="text" size="18" maxlength="32" name="usrname" id="usrname" value="'.$usrdat['usrname'].'" /><br/>
 <label for="usrpronoun">'.$lsite['pronoun'].'</label> <input type="text" size="18" maxlength="32" name="usrpronoun" id="usrpronoun" value="'.$usrdat['usrpronoun'].'" /><br/>
 <label for="usrcountry">'.$lsite['country'].'</label> <input type="text" size="18" maxlength="15" name="usrcountry" id="usrcountry" value="'.$usrdat['country'].'" /><br/>
 <label for="usrcity">'.$lsite['city'].'</label> <input type="text" size="18" maxlength="25" name="usrcity" id="usrcity" value="'.$usrdat['city'].'" /><br/>
 <label for="usradres">'.$lsite['address'].'</label> <input type="text" size="28" maxlength="125" name="usradres" id="usradres" value="'.$usrdat['adres'].'" /><br/>
 '.$lsite['birthday'].':<br/>
<label for="usrbday">'.$lsite['day'].'<select id="usrbday" name="usrbday">'.setBdayOpt(1, 32, $set_zi).'</select></label>
<label for="usrbmonth">'.$lsite['month'].'<select id="usrbmonth" name="usrbmonth">'.setBdayOpt(1, 13, $set_luna).'</select></label>
<label for="usrbyear">'.$lsite['year'].'<select id="usrbyear" name="usrbyear">'.setBdayOpt(1911, $acum_an, $set_an).'</select></label>
 <fieldset><legend>Contact</legend>
  <label for="usrym">Yahoo Messenger:</label> <input type="text" size="18" maxlength="25" name="usrym" id="usrym" value="'.$usrdat['usrym'].'" /><br/>
  <label for="usrmsn">MSN Messenger:</label> <input type="text" size="18" maxlength="32" name="usrmsn" id="usrmsn" value="'.$usrdat['usrmsn'].'" /><br/>
  <label for="usrsite">Web Site:</label> <input type="text" size="18" maxlength="32" name="usrsite" id="usrsite" value="'.$usrdat['usrsite'].'" />
 </fieldset>
 <fieldset><legend>'.$lsite['userpage']['aditionals'].'</legend>
  <label for="usrocupation">'.$lsite['userpage']['ocupation'].'</label> (<span id="ocupchr">'.$lsite['userpage']['max500chr'].'</span>)<br/>
  <textarea rows="4" cols="30" name="usrocupation" id="usrocupation" onkeydown="checkNrChr(this, 500, \'ocupchr\');" onkeyup="checkNrChr(this, 500, \'ocupchr\');">'. $usrdat['ocupation']. '</textarea><br/>
  <label for="usrinteres">'.$lsite['userpage']['interes'].'</label> (<span id="intereschr">'.$lsite['userpage']['max500chr'].'</span>)<br/>
  <textarea rows="4" cols="30" name="usrinteres" id="usrinteres" onkeydown="checkNrChr(this, 500, \'intereschr\');" onkeyup="checkNrChr(this, 500, \'intereschr\');">'. $usrdat['interes']. '</textarea><br/>
  <label for="usrtransmit">'.$lsite['userpage']['transmit'].'</label> (<span id="transmitchr">'.$lsite['userpage']['max1000chr'].'</span>)<br/>
  <textarea rows="5" cols="48" name="usrtransmit" id="usrtransmit" onkeydown="checkNrChr(this, 1000, \'transmitchr\');" onkeyup="checkNrChr(this, 1000, \'transmitchr\');">'. $functions->formatHtml($usrdat['transmit']). '</textarea><br/>'.$lsite['userpage']['usetags'].
 '</fieldset>
  <input type="submit" name="susr" value="'.$lsite['modify'].'" />
</form>';
}

echo $usrhtml.'</section>';      // output the html code