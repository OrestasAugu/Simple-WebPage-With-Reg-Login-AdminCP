<?php
// UsersReg class, extended from Users
class UsersReg extends Users {
  public $result = '';       // stores data that must be returned by this class

  // constructor
  public function __construct($conn_data, $rank=0) {
    Base::__construct($conn_data);        // incluude the Main __construct()

    // if no errors calls setConn() method, otherwise, error
    if($this->eror === false) {
      // if received data through POST, creates connection
      if(isset($_POST['susr']) && isset($_POST['codev'])) {
        // if received data from the registration form, call the method getReg()
        if(isset($_POST['username']) && isset($_POST['email']) && isset($_POST['pass']) && isset($_POST['pass2'])) {
          $_POST = array_map("strip_tags", $_POST);        // removes tags
          $this->result = $this->getReg($_POST);
        }
        else $this->result = $this->lsite['eror_reg']['nofields'];
      }
      else $this->result = $this->setFormReg();         // returns only the form
    }
    else $this->result = $this->eror;
  }

  // the method that adds user's data in database
  private function addUser($username, $pass, $email) {
    $passenc = md5($pass);
    $sql = "INSERT INTO `users` (`name`, `passenc`, `email`, `rank`, `ip_reg`, `ip_visit`, `dtreg`, `pass`) VALUES ('$username', '$passenc', '$email', ".RANK.", '$this->ip', '$this->ip', ".time().", '$pass')";
    if($this->sqlExecute($sql) !== false) {
      $msg = sprintf($this->lsite['register']['regmsg'], $username);

      $id = self::$conn->lastInsertId();      // gets the auto-inserted ID
  
      // sets the link for registration confirmation
      $set_url = $this->protocol. $this->site. TOUSRF. '?mp='. $id. '_'. $passenc;
      $link_confirm = '<a href="'. $set_url. '">'. $set_url. '</a>';

      // adds a row with this user in `msgs` to can send email when a message is added in its page
      $sql = "INSERT INTO `msgs` (user, idusr, email, dt, ip, amail) VALUES ('$username', $id, '$email', ".time().", '".$this->ip."', 2)";
      $this->sqlExecute($sql);

      if(RANK === 0) {
        // sets subject and message
        $subject = $this->lsite['register']['mailsubject'].$this->site;
        $msg = sprintf($this->lsite['register']['mailmsg'], $this->site, $link_confirm, $email, $pass);

        // sends the email
        if($this->sendMail($email, ADMINMAIL, $this->site, $subject, $msg)) {
          $msg = sprintf($this->lsite['register']['mailsent'], $email);
        } 
        else $msg = $this->lsite['eror_reg']['sendmailreg'];
      }
    }
    else $msg = sprintf($this->lsite['eror_reg']['register'], $this->eror, $email);

    return $msg;
  }

  // sets the string with the error
  private function strEror($str) { return $this->setEror($str).$this->setFormReg(); }

  private function getReg($ar_post) {
    $username = $ar_post['username'];
    $pass = $ar_post['pass'];
    $email = $ar_post['email'];
    $re = false;

    // check if there is already a session with registration
    if(isset($_SESSION['Rregistered'])) $re = $_SESSION['registered'];
    // Check if the password is the same as in "Retype password"
    else if($pass != $ar_post['pass2']) $re = $this->strEror($this->lsite['eror_reg']['pass2']);
    // check for these allowed characters
    else if(!$this->checkStr($username) || !$this->checkStr($pass)) $re = $this->strEror($this->lsite['eror_users']['datachr']);
    // check name length
    else if(strlen($username)<3 || strlen($username)>32) $re = $this->strEror($this->lsite['eror_users']['username']);
    // check password length
    else if(strlen($pass)<7 || strlen($pass)>18) $re = $this->strEror($this->lsite['eror_users']['pass']);
    // Validate the email
    else if(!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $email)) $re = $this->strEror($this->lsite['eror_reg']['email']);
    // check the verification code
    else if($_SESSION['codev'] !== $ar_post['codev']) {
      $re = $this->strEror($this->lsite['eror_reg']['codev']);
      $this->setCaptcha('codev');
    }
    // if $re is False check the name and e-mail in database
    else if($re === false) {
      // if ACCONT is 1, check for IP too, else, only for name and email
      if (ACCOUNT === 1) $sql = "SELECT `name`, `email`, `ip_reg`, `ip_visit` FROM `users` WHERE `name`='$username' OR `email`='$email' OR `ip_reg`='$this->ip' OR `ip_visit`='$this->ip' LIMIT 1";
      else $sql = "SELECT `name`, `email`, `ip_reg`, `ip_visit` FROM `users` WHERE `name`='$username' OR `email`='$email' LIMIT 1";
      $redb = $this->sqlExecute($sql);

      // check if name and email already in database
      if($this->affected_rows > 0) {
        // Sets error if the user name, email-ul, ip_reg, or ip_visit already registered
        if(strcasecmp($redb[0]['name'], $username)==0) $re = $this->strEror(sprintf($this->lsite['eror_reg']['namexist'], $username));
        else if (strcasecmp($redb[0]['email'], $email)==0) $re = $this->strEror(sprintf($this->lsite['eror_reg']['mailexist'], $email));
        else if (ACCOUNT===1 && (strcasecmp($redb[0]['ip_reg'], $this->ip)==0 || strcasecmp($redb[0]['ip_visit'], $this->ip)==0)) $re = $this->strEror($this->lsite['eror_reg']['ipexist']);
      }
      else {
        // calls addUser() method to add the new account in database, and sets a session with the registration
        $re = $this->addUser($ar_post['username'], $pass, $ar_post['email']);
        $_SESSION['registered'] = $re;
      }
    }
    return $re;
  }

  // method that sets the registration form
  private function setFormReg() {
    // if there is a session with the registration, return it. Otherwise return the form
    if(isset($_SESSION['nregistered'])) $re = $_SESSION['registered'];
    else {
      // keep the data from form fields (not need rewriting)
      $username = isset($_POST['username']) ? $_POST['username'] : '';
      $pass = isset($_POST['pass']) ? $_POST['pass'] : '';
      $pass2 = isset($_POST['pass2']) ? $_POST['pass2'] : '';
      $email = isset($_POST['email']) ? $_POST['email'] : '';
      $codev = $this->setCaptcha('codev');     // seteaza cod de verificare

      $re = sprintf($this->lsite['register']['regtxt'], $codev).
      '<form action="" method="post" id="reg_form">
       <label for="email">E-Mail: </label><input type="text" name="email" maxlength="55" id="email" value="'.$email.'" /><br/><br/>
       <label for="username">'.$this->lsite['users_logform']['name'].'</label><input type="text" name="username" maxlength="32" id="username" value="'.$username.'" /><br/><br/>
       <label for="pass">'.$this->lsite['users_logform']['pass'].'</label><input type="password" name="pass" maxlength="18" id="pass" value="'.$pass.'" /><br/><br/>
       <label for="pass2">'.$this->lsite['register']['pass2'].'</label><input type="password" name="pass2" maxlength="18" id="pass2" value="'.$pass2.'" /><br/><br/>
       <label for="codev">'.$this->lsite['codev'].'</label><input type="text" name="codev" size="5" maxlength="6" id="codev" /><br/><br/>
       <input type="submit" name="susr" value="'.$this->lsite['users_logform']['register'].'" />
      </form></div>';
      }
    return $re;
  }
}