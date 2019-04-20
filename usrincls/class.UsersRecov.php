<?php
// UsersReCon class, extended from UsersReg
class UsersRecov extends UsersReg {
  // constructor
  public function __construct($conn_data) {
    Base::__construct($conn_data);        // incluude the Main __construct()

    // if no errors calls setConn() method, otherwise, error
    if($this->eror === false) {
      // if 'conn' property is set
      if((isset($_POST['susr']) && isset($_POST['email'])) || isset($_GET['mp'])) {
        // if GET (for confirmation) calls getConfirm(), otherwise, calls getReCon() with form data
        if(isset($_GET['mp'])) $this->result = $this->getConfirm($_GET['mp']);
        else $this->result = $this->getReCon($_POST);
      }
      else $this->result = $this->setFormReCon($_REQUEST['rc']);       // initially returns the form
    }
    else $this->result = $this->eror;

    echo $this->result;        // return / output the data stored in $result
  }

  // this method sends the user data to his e-mail, or the message for reconfirmation
  private function getReCon($ar_post) {
    $tip = $ar_post['susr'];       // request type (recovery or confirmation)
    $email = $ar_post['email'];
    $re = $tip;                      // for data returned by this method

    // validate the e-mail address
    if(preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $email)) {
      // If the verification code is correct
      if(isset($_SESSION['codev']) && $_SESSION['codev']==$ar_post['codev']) {
        unset($_SESSION['codev']);

         // check if email address is in the database
         $sql = "SELECT `name`, `passenc`, `pass`, `email`, `id` from `users` WHERE `email`='$email' LIMIT 1";
         $redb = $this->sqlExecute($sql);

        // if not found, sets a message and add the form with the setFormReCon() method
        if(!$redb || $this->affected_rows<1) {
          $re = sprintf($this->lsite['eror_users']['eror_regmail'], $email).$this->setFormReCon($tip);
        }
        else {
          // gets the row, and then the name, password, and id associated to the email
          $name = $redb[0]['name'];
          $passenc = $redb[0]['passenc'];
          $pass = $redb[0]['pass'];
          $id = (int)$redb[0]['id'];

          // sends data to e-mail (recovery request)
          if($tip == 'Recover') {
            // sets the subject and message
            $subject = $this->lsite['recov']['mailsubj1'];
            $msg1 = sprintf($this->lsite['recov']['mailmsg1'], $this->site);
        }
        else if($tip == 'Confirm') {
          // sets the link for registration confirmation
          $set_url = $this->protocol. $this->site.TOUSRF. '?mp='. $id. '_'. $passenc;
          $link_confirm = '<a href="'. $set_url. '">'. $set_url. '</a>';
          // sets subject and message
          $subject = $this->lsite['recov']['mailsubj2'];
          $msg1 = sprintf($this->lsite['recov']['mailmsg2'], $this->site, $link_confirm);
        }

          $msgld = sprintf($this->lsite['recov']['mailmsgld'], $email, $pass);
          $msg = $msg1. $msgld;
          if($this->sendMail($email, ADMINMAIL, $this->site, $subject, $msg)) {
            $re = sprintf($this->lsite['recov']['re'], $email);
          }
          else {
            $re = $this->lsite['recov']['eror_re'].$this->setFormReCon($tip);
          }
        }
      }
      else {
        unset($_SESSION['codev']);
        $re = $this->lsite['eror_codev'].$this->setFormReCon($tip);
      }
    }
    else $re = $this->lsite['eror_email'].$this->setFormReCon($tip);
    return $re;
  }

  // the method that confirms the registration
  private function getConfirm($get) {
    // get and split data (id_password) to check in database
    $ar_get = explode('_', $get);
    $id = (int)$ar_get[0];
    $passenc = $ar_get[1];
    $rank = 1;

    // update to set 'rank' to the value of $rank
    $sql = "UPDATE `users` SET `rank`='$rank' WHERE `id`='$id' AND `passenc`='$passenc' AND `rank`=0 LIMIT 1";
    if($this->sqlExecute($sql)) {
      // Select to check if 'rank' was updated
      $sql = "SELECT `rank` FROM `users` WHERE `id`='$id' LIMIT 1";
      $redb = $this->sqlExecute($sql);
      if($redb[0]['rank']>0) $re = $this->lsite['recov']['reconfirm'];
      else  {
        $link_confirm = '<b><a href="'.TOUSRF.'?rc=Confirm">Click</a></b>';
        $re = sprintf($this->lsite['recov']['reunconfirm'], $link_confirm);
      }
    }
    else $re = $this->lsite['recov']['eror_confirm']. $this->eror;

    return $re;
  }

  // the method with the form for Recovery and Confirmation
  function setFormReCon($tip) {
    $codev = $this->setCaptcha('codev');     // calls the method that returns a verification code

    // define the form
    $re = sprintf($this->lsite['recov']['formcodev'], $codev).
     '<form action="" method="post" id="recov_form">
     <input type="hidden" name="rc" value="'.$tip.'" />
     <label for="email">E-mail: </label> <input type="text" name="email" maxlength="55" id="email" /><br/><br/>
     <label for="codev">'.$this->lsite['codev'].'</label><input type="text" name="codev" size="5" maxlength="6" id="codev" /><br/>
     <input type="submit" name="susr" id="submit" value="'. $tip. '" />
     </form></div>';
     return $re;
  }
}