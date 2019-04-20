<?php
class Msgs extends Base {
  public $table = 'msgs';           
  public $user;              // to store the user name
  public $msgs = '';        // to store the messages posted in user`s page

  public function __construct($conn_data) {
    parent::__construct($conn_data);        // include the parent __construct() instructions
    if(isset($_REQUEST['usr'])) $this->user = $_REQUEST['usr'];

    // get messages, or calls method to add messages, delete messages, or GET for Unsubscribe
    if(isset($_GET['usr'])) {
      // sets number of rows and calls method that adds messages in $msgs
      $this->rowsperpage = ROWSPAGE;
      $this->setMsgs();
    }
  }

  // sets the content with the messages of the user page in $msgs, accordin to pagination
  protected function setMsgs() {
    if($list = $this->getMysqlRows('*', $this->table, "WHERE `user`='$this->user' AND LENGTH(`msg`)>4", 'ORDER BY `id` ASC')) {
      $nrmsg = $this->startrow;              // to show the message number

      for($i=0; $i<$this->affected_rows; $i++) {
        
        $name = $list[$i]['name'];
        $nametitle_id = '';
        if($list[$i]['fbuserid'] != '0') $nametitle_id = ' title="'. $list[$i]['fbuserid'] .'"';
        else if($list[$i]['social'] != '0') $nametitle_id = ' title="'. $list[$i]['social'] .'"';

        $email = (intval($list[$i]['amail'])===1 || intval($list[$i]['amail'])===3) ? $list[$i]['email'] : '';  // show e-mail only if `amail` is 1 or 3
        $msg = $list[$i]['msg'];
        $data = date('j-M-Y, &\n\b\sp; G:i ', $list[$i]['dt']);
        $id = $list[$i]['id'];
        $ip = $list[$i]['ip'];
        $nrmsg++;

        
        if(isset($_SESSION['usritspage']) && $_SESSION['usritspage']===1)  {
          GLOBAL  $functions;      // gets the object with functions

          // html code with comments, for admin
          $this->msgs .= "\r\n".'<div class="coms'.($nrmsg%2).'"><div class="n_coms"'. $nametitle_id .'> &#1440; '.$name.'</div><q>- IP: '.$ip.'</q>
           <div class="nr_coms"> <input type="checkbox" name="delcomm[]" value="'.$id.'" class="delsel" id="dcm'.$id.'" /><label for="dcm'.$id.'">'.$this->lsite['delete'].'</label></div>
          <span id="e'.$id.'" class="e_coms">'. $email. '</span>
          <em class="d_coms">'. $data. '</em> &nbsp; &nbsp; &nbsp;
          <br/><div id="c'.$id.'" class="c_coms">'. $msg. '</div></div><hr class="linie_coms"/>'."\r\n";
        }
        else {    // html code with comments, for visitors
          $this->msgs .= "\r\n".'<div class="coms'.($nrmsg%2).'"><div class="n_coms"'. $nametitle_id .'> &#1440; '.$name.'</div> <div class="nr_coms"> '.$nrmsg.' </div><span class="e_coms">'. $email. '</span><em class="d_coms">'. $data. '</em> &nbsp; &nbsp; &nbsp;
          <br/><div class="c_coms">'. $msg. '</div></div><hr class="linie_coms"/>'."\r\n";
        }
      }
      $this->setLinkspgs();           // sets the pagination links in $linkspgs
    }
    else $this->msgs = '<h3>'.$this->lsite['msgs']['nomsg'].'</h3>';
  }

  // checks form data
  public function checkForm($frm) {
    $re = '';             // will store the errors to return

    // checks the name, email, comments, and verification code
    if (!preg_match('/^[a-zA-Z0-9 _-]{3,32}$/', $frm['namec'])) $re .= $this->lsite['msgs']['eror_name'].'<br/>';
    if($frm['emailc']!='no_email' && $frm['emailc']!='' && !preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/', $frm['emailc'])) $re .= $this->lsite['eror_email'].'<br/>';
    if(strlen($frm['coment'])<5 || strlen($frm['coment'])>600) $re .= $this->lsite['msgs']['eror_msg'].'<br/>';
    // checks verification code
    if(isset($_POST['codev'])) if($_POST['codev'] !== $_SESSION['codev'])  $re .= $this->lsite['eror_codev'].'<br/>';

    // if $re is empty, returns true; else, returns error
    if($re == '') return true;
    else { $this->eror = $re; return $re; }
  }

  // the method to add the comment
  public function addMsgs($frm) {
    $dt = time();              // store current time
    // sets output that will be returned (a call to JS function)
    $reout = "<script type=\"text/javascript\">window.parent.resetMsg('%s');</script>";

    // if session, or cookie "addmsg" exists, and their value is higher then time()+300
    // sets to return error message, else, add the comments
    if((isset($_SESSION['addmsg']) && ($_SESSION['addmsg']+300)>$dt) || (isset($_COOKIE['addmsg']) && ($_COOKIE['addmsg']+300)>$dt)) {
      echo sprintf($reout, $this->lsite['msgs']['eror_sesadd']);
    }
    else {
      GLOBAL $functions;         // the object with functions

      // sets email to empty if 'no_email' from form
      if($frm['emailc']=='no_email') $frm['email'] = '';
      $fbuserid = isset($_SESSION['fbuserid']) ? $_SESSION['fbuserid'] : '0';     // set to add 'fbuserid', if loged with Facebook
      $social = isset($_SESSION['usropenid']) ? 'social' : '0';     // set to add '0'; or 'social', if loged with Yahoo/Google

      // sets the value for 'amail' column (1=show the email, 2=notify when comments are added, 3= 1 and 2)
      $amail = 0;
      // if notiffy-email, sets $amail 2, if show-email, increment $amail (this way can be 1 or 3)
      if(isset($_POST['amail']) && $_POST['amail']==2) $amail = 2;
      if(isset($_POST['showmail']) && $_POST['showmail']==1) $amail++;

      // format bbcode in 'coment', and replace new line with BR tag
      $frm['coment'] = $functions->formatBbcode($frm['coment']);
      $frm['coment'] = str_replace(array('\r\n','\n'), '<br/>', $frm['coment']);

      // if image is added (a string with atleast 4 characters [minimum extension])
      // sets data for uploading, and calls uploadFile() to upload it
      $eror_upimg = '';           // in case of errors, store them to be aded in JS alert()
      if(isset($_FILES['upimg']) && strlen($_FILES['upimg']['name'])>4) {
        GLOBAL $imguprule;          // array with permissions for image
        $fileup = $imguprule['dir'].$_SESSION['codev'].$_FILES['upimg']['name'];    // path and image name (with an aleator code, to be unique)

        // if upload without errors, sets the code that show the image
        if($this->uploadFile($_FILES['upimg'], $imguprule, BASE.$fileup)) {
          $frm['coment'] = '<div class="upimg"><img width="125" alt="'.$_FILES['upimg']['name'].'" src="'.$fileup.'" /></div>'.$frm['coment'].'<br class="clr" />';
        }
        else {     // store errors in $eror_upimg and reset $eror to false
          $eror_upimg = $this->eror;
          $this->eror = false;
        }
      }

      // gets all e-mails to which to send mail notifications, adds them into array with key=['id_dt']
      // "id_dt" is ussed in link for unsubscribe
      $tosend = array();
      $sql = "SELECT `id`, `dt`, `email` FROM `$this->table` WHERE `user`='$this->user' AND LENGTH(`email`)>4 AND `amail`>1 LIMIT 10";
      $resql = $this->sqlExecute($sql);
      if($this->affected_rows > 0) {
        for($i=0; $i<$this->affected_rows; $i++) { $tosend[$resql[$i]['id'].'_'.$resql[$i]['dt']] = $resql[$i]['email']; }
      }

      // add comments data in database
      $sql = "INSERT INTO `$this->table` (user, idusr, name, email, fbuserid, social, msg, dt, ip, amail) VALUES ('$this->user', (SELECT id FROM users WHERE name='$this->user' LIMIT 1), '".$frm['namec']."', '".$frm['emailc']."', '$fbuserid', '$social', '".$frm['coment']."', $dt, '".$this->ip."', ".$amail.")";

      if($this->sqlExecute($sql)) {
        // set session and cookie with the time when added comment,
        // that is checked to not let adding another comment in 5 minutes
        $_SESSION['addmsg'] = $dt;
        setcookie("addmsg", $_SESSION['addmsg'], $dt+60*5, "/");

        echo sprintf($reout, sprintf($this->lsite['msgs']['jsadd'], $frm['namec'])."\\n \\n $eror_upimg");      // confirm comment added
        flush();       // transmit the output to browser, than execute the rest of instructions

        // define the URL of the pages with comment, and calls resetMsg() JS function
        $pguser = $this->protocol.$this->site. dirname(str_replace(basename(dirname($_SERVER['PHP_SELF'])).'/', '', $_SERVER['PHP_SELF'])).'/users.php?usr='. $this->user;

        if(count($tosend) > 0) {
          $tosend = array_unique($tosend);           // remove duplicate e-mails
          $this->notifyMail($tosend, $pguser);     // sends mail notifications
        }
      }
      else echo sprintf($reout, $this->eror."\\n \\n $eror_upimg");
    }
  }

  // delete comment
  public function delMsg($frm) {
    // if form data with fields with IDs, and img of the comments to delete
    if(isset($frm['id_dcm']) && isset($frm['img_dcm'])) {
      // delete comments in database
      $sql = "DELETE FROM `$this->table` WHERE `id` IN(".$frm['id_dcm'].")";
      if($this->sqlExecute($sql)) {
        $delfile = $this->lsite['msgs']['jsdelete'];            // messages for detetting files

        // if "img_dcm" not empty, gets each img adress (separatted by comma), and delete it
        if($frm['img_dcm'] != '') {
          $imgs = explode(',', $frm['img_dcm']);
          $nr_imgs = count($imgs);
          if($nr_imgs > 0) {
            for($i=0; $i<$nr_imgs; $i++) {
              if(unlink(BASE.$imgs[$i])) $delfile .= '\n'.$imgs[$i]. $this->lsite['delok'];
              else $delfile .= '\n'. $this->lsite['eror_delfile']. $imgs[$i];
            }
          }
        }
        
        return $delfile;
      }
      else return $this->lsite['msgs']['eror_delete']. $this->eror;
    }
  }

  // receive array with [id_dt]=>e-mails for notification, and comment page URL. Calls the method to send emails
  protected function notifyMail($tosend, $pguser) {
    // sets subject, parse $tosend, to create the link for unsubscribe, and calls method to send e-mail
    $subject = sprintf($this->lsite['msgs']['notifysub'], $this->site);
    $to = array();          // store e-mail address
    $msgs = array();        // to store message to be send, associated by $i to each e-mail
    $i = 0;

    foreach($tosend AS $unsub=>$e) {
      $to[$i] = $e;
      $pguns = $this->protocol.$this->site.$_SERVER['PHP_SELF'].'?unsub='.$unsub;
      $msgs[$i] = sprintf($this->lsite['msgs']['notifymsg'], $this->site, $this->site, '<a href="'.$pguser.'" title="Message">'.$pguser.'</a>', '<a href="'.$pguns.'" title="Unsubscribe">'.$pguns.'</a>');
      $i++;
    }
    $this->sendMail($to, ADMINMAIL, $this->site, $subject, $msgs);
  }

  // unsubscribe the notification, receive the "ID_DT", select the user row where that ID and DT
  // then decrease 'amail' with 2 where that user, e-mail, and 'amail'>1
  public function unsubscribe($unsub) {
    // sets variable to return an JS alert, and redirect to homepage
    $reout = "%s <script type=\"text/javascript\"><!-- 
      alert('%s');
      window.location = '/';
    --></script>";
    $iddt = explode('_', $unsub);          // separe 'id' and 'dt'

    $sql = "SELECT `user`, `email` FROM `$this->table` WHERE `id`=".$iddt[0]." AND `dt`=".$iddt[1]." LIMIT 1";
    if($resql = $this->sqlExecute($sql)) {
      if($this->affected_rows > 0) {
        // if session that unsubscribed from the user with "id", return message, else perform subscription
        if(isset($_SESSION['unsub']) && $_SESSION['unsub'] == $resql[0]['user']) {
          return sprintf($reout, $this->lsite['msgs']['eror_sesunsub'], $this->lsite['msgs']['eror_sesunsub']);
        }
        else {
          $sql = "UPDATE `$this->table` SET `amail`=`amail`-2 WHERE `user`='".$resql[0]['user']."' AND `email`='".$resql[0]['email']."' AND `amail`>1";
          if($this->sqlExecute($sql)) {
            $_SESSION['unsub'] = $resql[0]['user'];      // set session to know that unsubscribed from the page
            return sprintf($reout, $this->lsite['msgs']['unsubscribe'], $this->lsite['msgs']['unsubscribe']);
          }
          else return $this->lsite['msgs']['eror_unsub'];
        }
      }
      else return $this->lsite['msgs']['eror_unsubscribe'];
    }
    else return $this->lsite['msgs']['eror_unsubscribe'].$this->eror;
  }
}