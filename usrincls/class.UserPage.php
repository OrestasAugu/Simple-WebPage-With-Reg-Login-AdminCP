<?php
// UserPage class (that sets data for User's page) extended from LogareReg
class UserPage extends Users {
  private $usr;         // for username
  private $idusr;           // user`s id in database
  protected $result = '';    // the result returned by this class

  // constructor
  public function __construct($conn_data) {
    Base::__construct($conn_data);        // incluude the Main __construct()

    // sets $usr and $idusr
    if(isset($_REQUEST['usr'])) $this->usr = $_REQUEST['usr'];
    if(isset($_POST['usr']) && is_numeric($_POST['usr'])) $this->idusr = $_POST['usr'];
    else if(isset($_POST['idusr'])) $this->idusr = $_POST['idusr'];

    // if SESSION with logged user, and data from his page. Or if an Admin "rank"=9
    if($_SESSION['usritspage'] === 1 && isset($_REQUEST['usr'])) {
      // if POSTs with user data, and POST['susr']: calls modfMailPass(), uploadImg(), addOptDat()
      if(isset($_POST['susr'])) {
        // add Aditional data
        if(isset($_POST['usradres']) && isset($_POST['usrbday'])) $this->result = $this->addOptDat($_POST);
        // upload users image
        else if(isset($_FILES['usrimg'])) $this->result = $this->uploadImg($_FILES['usrimg'], $_POST);
        // adds favorite
        else if(isset($_POST['adfav'])) $this->result = $this->addFav($_POST);
        // update Rank
        else if(isset($_POST['rank'])) $this->result = $this->setRank($_POST);
        // delete User/s
        else if(isset($_POST['delusr'])) $this->result = $this->deleteUsers($_POST);
        // modify password / email
        else if(isset($_POST['email']) && isset($_POST['passnew']) && isset($_POST['pass'])) $this->result = $this->modfMailPass($_POST);
        else $this->eror = $this->lsite['eror_reg']['eror_moddata'];
      }
    }

    if($this->eror !== false) $this->result = $this->setEror($this->eror);        // if there is a error, adds it in the returned data
    echo $this->result;        // return / output the data stored in $result
  }

  // this method gets the user data from database
  public function getUser($user) {
    $sql = "SELECT `users`.`id`, `users`.`email`, `users`.`rank`, `users`.`visits`, `usersdat`.`name`, `usersdat`.`pronoun`, `usersdat`.`country`, `usersdat`.`city`, `usersdat`.`adres`, `usersdat`.`ym`, `usersdat`.`msn`, `usersdat`.`site`, `usersdat`.`img`, `usersdat`.`ocupation`, `usersdat`.`interes`, `usersdat`.`transmit`, `usersdat`.`fav`, `users`.`dtreg`, `users`.`dtvisit`, DATE_FORMAT(`usersdat`.`bday`, '%M %D, %Y') AS bday FROM `users` LEFT JOIN `usersdat` ON `usersdat`.`id`=(SELECT `id` FROM `users` WHERE `name`='$this->usr' LIMIT 1) WHERE `users`.`name`='$this->usr' ORDER BY `id` LIMIT 1";

    if(($redb = $this->sqlExecute($sql)) && $this->affected_rows>0) {
      GLOBAL $imguprule;          // array with permissions for image
      $this->idusr = $redb[0]['id'];         // store user`s id number

      // sets data to be used in "usrbody.php"
      $usrdat['usr'] = $this->usr;
      $usrdat['idusr'] = $redb[0]['id'];
      $usrdat['usrmail'] = $redb[0]['email'];
      $usrdat['rank'] = $redb[0]['rank'];
      $usrdat['visits'] = $redb[0]['visits'];
      $usrdat['usrname'] = $redb[0]['name'];
      $usrdat['usrpronoun'] = $redb[0]['pronoun'];
      $usrdat['country'] = $redb[0]['country'];
      $usrdat['city'] = $redb[0]['city'];
      $usrdat['adres'] = $redb[0]['adres'];
      $usrdat['usrym'] = $redb[0]['ym'];
      $usrdat['usrmsn'] = $redb[0]['msn'];
      $usrdat['usrsite'] = $redb[0]['site'];
      $usrdat['imgusr'] = (isset($redb[0]['img']) && strlen($redb[0]['img'])>1 && file_exists($redb[0]['img'])) ? $redb[0]['img'] : $imguprule['dir'].'noimg.gif';
      $usrdat['ocupation'] = $redb[0]['ocupation'];
      $usrdat['interes'] = $redb[0]['interes'];
      $usrdat['transmit'] = $redb[0]['transmit'];
      $usrdat['dtreg'] = date('j-M-Y, H:i', $redb[0]['dtreg']);
      $usrdat['dtvisit'] = $redb[0]['dtvisit']>11111111 ? date('j-M-Y, H:i', $redb[0]['dtvisit']) : $this->lsite['userpage']['notloged'];
      $usrdat['bday'] = $redb[0]['bday']!='00-00-0000' ? $redb[0]['bday'] : '';
      $usrdat['fav'] = $redb[0]['fav'];

      // for the page of the logged user, get the last visit time from session becouse in table it is updated
      if(isset($_SESSION['username']) && strtolower($_SESSION['username'])==strtolower($this->usr)) $ar_usrdat['dtvisit'] = $_SESSION['dtvisit'];
    }
    else $usrdat = false;

    return $usrdat;
  }

  // this method Update the User data
  private function modfMailPass($ar_post) {
    $ar_post = array_map("strip_tags", $ar_post);       // remove tags
    $re = '';                    // store returned data

    // if there are data from 'email', 'pass', 'passnew'
    if(isset($ar_post['email']) && isset($_SESSION['email']) && isset($ar_post['pass']) && isset($ar_post['passnew'])) {
      // check password length and email address
      if(strlen($ar_post['passnew'])<7 || strlen($ar_post['passnew'])>18 || !$this->checkStr($ar_post['passnew'])) {
        $this->eror = $this->lsite['eror_users']['pass'];
      }
      if(!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $ar_post['email'])) {
        $this->eror = $this->lsite['eror_email'];
      }

      // if no errors, continue to filter and update data
      if($this->eror === false) {
        // gets the pass of user whois page is edited to check if the current password is the same as in database
        $sql = "SELECT `id`, `email`, `pass` FROM `users` WHERE `name`='".$ar_post['usr']."' LIMIT 1";

        // if query performed, with returned row, gets 'pass' and 'email'
        if(($redb = $this->sqlExecute($sql)) && $this->affected_rows>0) {
          $id = $redb[0]['id'];
          $email = $redb[0]['email'];
          $passenc = md5($ar_post['passnew']);        // encript the password

          // check if the current password is the same as in database
          if($redb[0]['pass'] == $ar_post['pass']) {
            // Select to check if there is already the updated e-mail addres to another user
            $sql = "SELECT `email` FROM `users` WHERE `name`!='".$ar_post['usr']."' AND `email`='". $ar_post['email']. "'";
            $redb = $this->sqlExecute($sql);

            // if the result contains at least one row, means that email is already used by other user
            if($this->affected_rows>0) {
              $this->eror = sprintf($this->lsite['eror_reg']['mailexist'], $ar_post['email']);
            }
            else {
              // if changed the email, and RANK defined in 'config.php' is 0, set to Update Rank
              $setrank = (RANK == 0 && $email != $ar_post['email']) ? ', rank='.RANK : '';

              // perform the update and sets the new password in session
              $sql = "UPDATE `users` SET `passenc`='". $passenc ."', `pass`='".$ar_post['passnew']."', `email`='". $ar_post['email']. "'". $setrank ." WHERE `name`='".$ar_post['usr']."' AND `pass`='". $ar_post['pass']."'";

              if ($this->sqlExecute($sql)) {
                // if logged user in his own page reset session with logged password
                if($_SESSION['username'] == $ar_post['usr']) $_SESSION['passenc'] = $passenc;
                $re = $this->lsite['userpage']['modfdata'];

                // sends an message to user's e-mail with the new data
                $subject = $this->lsite['userpage']['mailsubject'];
                // if changed the email, and RANK is 0. set to get text from 'texts.php' with msg with link to confirm the new email account
                // else, just msg with new login data
                if(RANK == 0 && $email != $ar_post['email']) {
                  $msgfrom = 'register';
                  $set_url = $this->protocol.$this->site. TOUSRF. '?mp='. $id .'_'. $passenc;
                  $link_confirm = '<a href="'. $set_url. '">'. $set_url. '</a>';
                }
                else {
                  $msgfrom = 'userpage';
                  $link_confirm = '';
                }

                $msg = sprintf($this->lsite[$msgfrom]['mailmsg'], $this->site, $link_confirm, $ar_post['email'], $ar_post['passnew']);
                if($this->sendMail($ar_post['email'], ADMINMAIL, $this->site, $subject, $msg)) $re .= sprintf($this->lsite['userpage']['mailsent'], $ar_post['email']);
              }
              else  $this->eror = 'Error Update: '. $this->eror;       // error on update
            }
          }
          else $this->eror = $this->lsite['userpage']['eror_pass'];
        }
        else $this->eror = sprintf($this->lsite['eror_users']['namenoreg'], $ar_post['usr']);
      }
    }
    else $this->eror = $this->lsite['userpage']['eror_modmp'];

    // if error, return #eror, else, JS alert with $re, and refresh page
    if($this->eror !== false) return $re;
    else return '<script type="text/javascript">alert("'.$re.'");window.location.reload(true);</script>';
  }

  // add user optional data (address, birthday ...) in "usersdat" table
  private function addOptDat($ar_post) {
    GLOBAL  $functions;      // gets the object with functions (for formatBbcode())
    $ar_post = array_map("strip_tags", $ar_post);       // remove tags
    $ar_post['usrtransmit'] = $functions->formatBbcode($ar_post['usrtransmit']);     // format bbcode in html

    $name = $ar_post['usrname'];
    $pronoun = $ar_post['usrpronoun'];
    $country = $ar_post['usrcountry'];
    $city = $ar_post['usrcity'];
    $adres = $ar_post['usradres'];
    $ym = $ar_post['usrym'];
    $msn = $ar_post['usrmsn'];
    $site = $ar_post['usrsite'];
    $ocupation = $ar_post['usrocupation'];
    $interes = $ar_post['usrinteres'];
    $id = $ar_post['idusr'];
    $bday = intval($ar_post['usrbyear']).'-'.intval($ar_post['usrbmonth']).'-'.intval($ar_post['usrbday']);    // Y-M-D

    // adds data in "usersdat", or Update if there is already a row for this user
    $sql = "INSERT INTO `usersdat` (`id`, `name`, `pronoun`, `country`, `city`, `adres`, `bday`, `ym`, `msn`, `site`, `ocupation`, `interes`, `transmit`) VALUES ($id, '$name', '$pronoun', '$country', '$city', '$adres', '$bday', '$ym', '$msn', '$site', '$ocupation', '$interes', '".$ar_post['usrtransmit']."') ON DUPLICATE KEY UPDATE `name`='$name', `pronoun`='$pronoun', `country`='$country', `city`='$city', `adres`='$adres', `bday`='$bday', `ym`='$ym', `msn`='$msn', `site`='$site', `ocupation`='$ocupation', `interes`='$interes', `transmit`='".$ar_post['usrtransmit']."'";

    // perform SQL, return JS alert with $re, and refresh page; or error
    if($this->sqlExecute($sql)) return '<script type="text/javascript">alert("'.$this->lsite['userpage']['regdata'].'");window.location.reload(true);</script>';
    else return $this->eror = $this->lsite['userpage']['eror_regdata']. $this->eror;
  }

  // this method Upload the image, save in database its name and path, and return it
  private function uploadImg($filedata, $ar_post) {
    GLOBAL $imguprule;          // array with permissions for image

    // sets image name and path for upload
	  $splitimg = explode('.', strtolower($filedata['name']));
    $ext = end($splitimg);
    $fileup = strtolower($imguprule['dir']. $this->usr. '.'.$ext);   
    
    // SQL to add file path in "usersdat", or update if there is already a record
    $sql = "INSERT INTO `usersdat` (`id`, `img`) VALUES (".$ar_post['idusr'].", '$fileup') ON DUPLICATE KEY UPDATE `img`='$fileup'";

    $re = $this->uploadFile($filedata, $imguprule, $fileup, $sql);    // upload the img and insert in database
    if(!$re) {    // if $re is false, gets the error, and resets $eror to false
      $re = $this->eror;
      $this->eror = false;
    }

    // returns the result in a call of a JavaScript function
    return '<body onload="parent.uplImg(\''.$re.'\')">'.$re.'</body>';
  }

  // sets the HTML code with favorite links, in JSON in $fav
  public function getFav($fav) {
    $adfav = '';             // store form to add favorite link
    $favlinks = '<h4 id="nofav">'.$this->lsite['userpage']['nofav'].'</h4>';    // store favorite links, initialy message no fav

    // decode JSON with favorites, and create HTML code to be returned
    $fav = json_decode(stripslashes($fav));
    $nrfav = count($fav);      // number of favorite links

    // If its the page of the logged user, or Admin, and maximum 10 links, sets form to add favorite link
    if(isset($_SESSION['usritspage']) && $_SESSION['usritspage']===1 && $nrfav<12) {
      $adfav = '<br/><div id="adfav">'.$this->lsite['userpage']['adfav'].'<br/>
       <form action="" method="post" onsubmit="return addFavLink(this, '.$this->idusr.')">
       <input type="hidden" name="usr" value="'.$this->usr.'" />
       <label for="favlnk">Link: </label> &nbsp; <input type="text" name="favlnk" id="favlnk" size="28" maxlength="110" /><br/>
       <label for="favtitl">'.$this->lsite['name'].': </label><input type="text" name="favtitl" id="favtitl" size="28" maxlength="110" /><br/>
       <input type="submit" name="susr" value="'.$this->lsite['userpage']['adfavbt'].'" />
       </form></div><h4>'.$this->lsite['userpage']['favhave'].'</h4>';
    }

    // define to add favorite links, if exists in $fav (arrays 0=>link, 1=>title)
    if($nrfav > 0) {
      $favlinks = '<ol id="favol">';
      for($i=0; $i<$nrfav; $i++) {
        $favlinks .= '<li><span title="'.$fav[$i][0].'">'.$fav[$i][1].'</span>';

        // If its the page of the logged user, or Admin, adds checkbox to delete favorite link
        if(isset($_SESSION['usritspage']) && $_SESSION['usritspage']===1)  {
          $favlinks .= '<input type="checkbox" name="delfav" value="'.$i.'" id="delfav'.$i.'" class="delsel" /><label for="delfav'.$i.'">'.$this->lsite['delete'].'</label>';
        }
        $favlinks .= '</li>';
      }
      $favlinks .= '</ol>';

      // If its the page of the logged user, or Admin, adds button to delete favorite links
      if(isset($_SESSION['usritspage']) && $_SESSION['usritspage']===1) $favlinks .= "<button onclick=\"delFavLink('$this->usr', $this->idusr);\">".$this->lsite['delsel'].'</button>';
    }

    return $adfav.$favlinks;
  }

  // adds favorite links (received by Ajax in JSON format)
  protected function addFav($ar_post) {
    // adds data in "usersdat", or Update if there is already a row for this user
    $sql = "INSERT INTO `usersdat` (`id`, `fav`) VALUES (".$ar_post['idusr'].", '".$ar_post['adfav']."') ON DUPLICATE KEY UPDATE `fav`='".$ar_post['adfav']."'";
    if($this->sqlExecute($sql)) return '<h4 style="color:blue">'.$this->lsite['userpage']['adfavok'].'</h4>'.$this->getFav(stripslashes($ar_post['adfav']));
  }

  // changes the User`s Rank, except first Admin (id 1)
  protected function setRank($ar_post) {
    // if user from 'users' table with 'id' less than 2, else, it`s Admin, and return error message
    // else, perform Update
    if($ar_post['table'] == 'users' && $ar_post['usr'] < 2 ) {
      $this->eror = $this->lsite['eror_rnkdeladmin'];
    }
    else {
      $sql = "UPDATE `". $ar_post['table'] ."` SET `rank`=".intval($ar_post['rank'])." WHERE `id`=".intval($ar_post['usr']);
      if($this->sqlExecute($sql)) return $this->lsite['userpage']['setrankok']. ' ( '.$ar_post['rank'].' ) ';
    }
  }

  // delete User/s, except first Admin (id 1). Receives POST with 'id'
  protected function deleteUsers($ar_post) {
    // if 'users' table, set to affect from id higher then 1, to not affect admin (1st account)
    $fromid = ($ar_post['table'] == 'users') ? 1 : 0;

    // delete users in table from $ar_post['table'], than the mesages added to their page in 'msgs', and its row in 'usersdat'
    $sql = "DELETE FROM `". $ar_post['table'] ."` WHERE `id` IN(". $ar_post['delusr'] .") AND `id`>". $fromid;

    // if deleted from main users tble, and is 'users' table, performs the other deletings
    if($this->sqlExecute($sql) && $ar_post['table'] == 'users') {
      $sql = "DELETE FROM `msgs` WHERE `idusr` IN(". $ar_post['delusr'] .") AND `idusr`>". $fromid;
      $this->sqlExecute($sql);
      $sql = "DELETE FROM `usersdat` WHERE `id` IN(". $ar_post['delusr'] .") AND `id`>". $fromid;
      $this->sqlExecute($sql);
    }

    if($this->eror === false) return '<script type="text/javascript">alert("'.$this->lsite['delok'].'");window.location=\''.TOUSRF.'?usr=0\';</script>';
    else echo $this->eror;
  }
}