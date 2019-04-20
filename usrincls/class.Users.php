<?php
// Base class
class Users extends Base {
  // properties
  protected $sid;                   // for the session ID
  protected $nr_logs = 1;            // for the current number of login attempts
  public $users = array('total'=>0, 'last'=>'', 'online'=>0);           // will store the total users, last registered, and online users
  public $loged = '';               // stores the login form or the "Welcome" message

  // constructor
  public function __construct($conn_data) {
    parent::__construct($conn_data);        // incluude the parent __construct() instructions

    $this->sid = session_id();             // add the session ID in $sid property

    // if no $_POST['isajax'], define the login form in "loged" property
    if(!isset($_POST['isajax'])) {
      $this->loged = '<form action="" method="post" id="log_form">'.$this->lsite['users_logform']['email'].': <input type="text" name="email" id="email" size="12" maxlength="55" /> <input type="submit" name="login"  class="submit" value="'.$this->lsite['users_logform']['login'].'" /><br/>'.$this->lsite['users_logform']['pass'].'<input type="password" name="pass" id="pass" size="12" maxlength="18" /><label for="rem" id="lrem"><input type="checkbox" name="rem" id="rem" />'.$this->lsite['users_logform']['rem'].'</label><hr/><a href="'. TOUSRF. '?rc=Recover" title="'.$this->lsite['users_logform']['recdat'].'" id="recdat">'.$this->lsite['users_logform']['recdat'].'</a> <a href="'. TOUSRF. '?susr='.$this->lsite['users_logform']['register'].'" title="'.$this->lsite['users_logform']['register'].'" id="linkreg">'.$this->lsite['users_logform']['register'].'</a>';

      // add text info for alternative login, if at least one is set
      if(FBCONN == 1 || YHCONN == 1 || GOCONN == 1) $this->loged .= '<hr/><div class="sb">'.$this->lsite['users_logform']['orlogw'].'</div>';

      // add Login with Facebook, if FBCONN is 1 (defined in 'config.php')
      if(FBCONN == 1) $this->loged .= '<img src="'. BASE .'icos/fblogin.png" alt="'.$this->lsite['users_logform']['fblogin'].'" width="61" height="21" id="fblogin" />';

      // add Login with Yahoo OpenID, if YhCONN is 1 (defined in 'config.php')
      if(YHCONN == 1) $this->loged .= '<img src="'. BASE .'icos/yhlogin.png" alt="'.$this->lsite['users_logform']['yhlogin'].'" width="61" height="22" id="yhlogin" />';

      // add Login with Google OpenID, if GOCONN is 1 (defined in 'config.php')
      if(GOCONN == 1) $this->loged .= '<img src="'. BASE .'icos/gologin.png" alt="'.$this->lsite['users_logform']['gologin'].'" width="61" height="22" id="gologin" />';

      $this->loged .= '</form>';
    }

    // if there is data from the login form, calls the getLogin() method
    if(isset($_POST['login']) && isset($_POST['email']) && isset($_POST['pass'])) {
      $this->getLogin($_POST);
    }
    // if request for logout ($_GET['lout']), calls logOut() method
    else if(isset($_GET['lout'])) $this->logOut();
    else $this->setLoged();        // else, calls setLoged() method that sets the $loged property

    if($this->nr_logs > 1) $this->logOut(2);        // $nr_logs>1 means two users loged with the same email. Logout the user

    // if $eror property isn't false, add it in $loged property
    if($this->eror !== false) $this->loged = $this->setEror($this->eror). $this->loged;
  }

  // set $loged with html code displayed in place of login form, after user logs in
  private function setHtmlLoged($userpglink) {
    $this->loged = '<div id="loged"><span class="sb">'. $userpglink .'<br/><br/><a href="'.$_SERVER['PHP_SELF'].'?lout=lo" title="'.$this->lsite['users_loged']['lout'].'">'.$this->lsite['users_loged']['lout'].'</a></span></div>';
  }

  // method that cals setHtmlLoged() to set $loged property, different for Facebook /Google login
  private function setLoged() {
    
    if(isset($_SESSION['username']) && isset($_SESSION['fbuserid'])) {
      $userpglink = '<a href="http://www.facebook.com/profile.php?id='. $_SESSION['fbuserid'] .'" target="_blank" title="Facebook '.$_SESSION['username'].'" id="idpp">FB: '.$_SESSION['username'].'</a>';
      $this->setHtmlLoged($userpglink);
    }
    else if(isset($_SESSION['username']) && isset($_SESSION['usropenid'])) {
      $userpglink = $_SESSION['username'];
      $this->setHtmlLoged($userpglink);
    }
    else {
      // If the user is stored in cookie, add the data in session
      if(isset($_COOKIE['cookmail']) && isset($_COOKIE['cookpass'])) {
        $_SESSION['email'] = $_COOKIE['cookmail'];
        $_SESSION['passenc'] = $_COOKIE['cookpass'];
      }

      // if the email and password are stored in session
      if(isset($_SESSION['email']) && isset($_SESSION['passenc'])) {
        // calls the confirmUser() method to confirm if the email and password are valid
        if($this->confirmUser($_SESSION['email'], $_SESSION['passenc']) === 0) {
          $userpglink = '<a href="'.TOUSRF.'?usr='.$_SESSION['username'].'" title="'.$_SESSION['username'].' Page" id="idpp">'.$this->lsite['users_loged']['userpage'].'</a>';
          $this->setHtmlLoged($userpglink);
        }
        else {
          // else, the variables are incorrect, calls logOut() to delete the session and cookies
          $this->logOut(0);
          $this->loged = $this->lsite['eror_users']['insession'];
        }
      }
    }

    // calls the method that gets and adds total users, last and registered users in $users property
    $this->usersOn();
  }

  // method for checking the existence of the allowed characters in string
  protected function checkStr($str) {
    $allow = '/^([A-Za-z0-9_-]+)$/';
    if(preg_match($allow, $str)) return true;
    else return false;
  }

  // method that checks data from the login form (passed in parameter) and from the database
  private function getLogin($ar_post) {
    // check for correct email address
    if(!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $ar_post['email']) || !$this->checkStr($ar_post['pass'])) $this->eror = $this->lsite['eror_email'];

    // check the length of the password
    else if(strlen($ar_post['pass'])<7 || strlen($ar_post['pass'])>18) $this->eror = $this->lsite['eror_users']['pass'];
    else {
      // Check and register with logAttempt() method the log in attempt
      $continu = $this->logAttempt($ar_post['email']);

      if($continu == 'continue') {
        $passenc = md5($ar_post['pass']);    // encript the password
        $re = $this->confirmUser($ar_post['email'], $passenc);      // uses confirmUser() to check if email and password are correct

        // sets 'eror' if email or password are incorrect
        if($re == 1) $this->eror = sprintf($this->lsite['eror_users']['eror_regmail'], $ar_post['email']);
        else if($re == 2) $this->eror = $this->lsite['eror_users']['inpass'];
        else if($re == 3) {
          exit(sprintf($this->lsite['eror_users']['unconfirmed'], $ar_post['email'], TOUSRF));
        }
        else if($re == 4) $this->eror = sprintf($this->lsite['eror_users']['ban'], $ar_post['email']);
        else {
          // user email and password are correct
          // if the "Remember" checkbox is checked, sets 2 cookies, for email and password (expires in 100 days)
          if(isset($ar_post['rem'])) {
            setcookie("cookmail", $_SESSION['email'], time()+60*60*24*100, "/");
            setcookie("cookpass", $_SESSION['passenc'], time()+60*60*24*100, "/");
          }

          // UPDATE `ip_visit` with the user's IP, and the `visits` number
          $sql = "UPDATE `users` SET `ip_visit`='$this->ip', `dtvisit`=".time().", `visits`=`visits`+1 WHERE `email`='". $_SESSION['email']. "' LIMIT 1";
          $this->sqlExecute($sql);

          $this->setLoged(1);        // calls the method that sets $loged property
        }
      }
      else {
        // Sets a message with the remaining time to a new allowed authentication attempt
        $continu = floor($continu/60). ' '.$this->lsite['datetime']['min'].', '. ($continu%60). ' '.$this->lsite['datetime']['sec'];
        $this->eror = sprintf($this->lsite['eror_users']['logattempt'], $continu);
      }
    }
  }
 
  // this method checks the email and password in the database,
  // if they are correct returns 0, otherwise 1 to 4, indicating the error
  private function confirmUser($email, $passenc) {
    // Check if the email is in the database
    $sql = "SELECT `id`, `name`, `passenc`, `rank`, `dtvisit` FROM `users` WHERE `email`='$email' LIMIT 1";
    $row = $this->sqlExecute($sql);
    if(!$row || $this->affected_rows<1) return 1;         // Indicates email not registered
    else {
      if($row[0]['rank'] < 0) return 4;               // User banned
      else if($row[0]['rank'] == 0) return 3;         // Registration not confirmed
      else if($passenc == $row[0]['passenc']) {       // if the password is the same as that found in the database
        // adds in session name, email, passenc, id, rank, and last-visit-date (if isn't added)
        $_SESSION['username'] = $row[0]['name'];
        $_SESSION['email'] = $email;
        $_SESSION['passenc'] = $passenc;
        $_SESSION['idusr'] = $row[0]['id'];
        $_SESSION['rank'] = $row[0]['rank'];
        if(!isset($_SESSION['dtvisit'])) $_SESSION['dtvisit'] = $row[0]['dtvisit']>11111111 ? date('F S, Y, H:i', $row[0]['dtvisit']) : date('F S, Y, H:i', time());
        return 0;          // email and password confirmed
      }
      else return 2;    // indicating incorrect password
    }
  }

  // this method deletes rows in the 'logattempt' table, older than 10 minutes
  // if the user already tried 3 times to log in, blocks that email for 10 minutes
  private function logAttempt($email) {
    $dt = time();
    $timexpir = $dt-600;

    // deletes rows in the 'logattempt' table, older than 10 minutes
    $sql = "DELETE FROM `logattempt` WHERE `dt`<$timexpir";
    $this->sqlExecute($sql);

    // add / increment number of attempt by 1, and updates the date-time
    $sql = "INSERT INTO `logattempt` (`email`, `ip`, `dt`) VALUES ('$email', '$this->ip', $dt) ON DUPLICATE KEY UPDATE `nri`=`nri`+1";
    $this->sqlExecute($sql);

    // check if it was performed UPDATE (existing names) [was affected more than 1 row]
    if($this->affected_rows > 1) {
      // select to get the number of attempts
      $sql = "SELECT `nri`, `dt` FROM `logattempt` WHERE `email`='$email' LIMIT 1";
      $redb = $this->sqlExecute($sql);

      if(!$redb || $this->affected_rows<1) return 'continue';
      else {
        // check if attempts number exceeded
        if($redb[0]['nri'] < 3) return 'continue';
        else return 600 - ($dt - $redb[0]['dt']);    // returns the number of seconds to wait
      }
    }
    else return 'continue';         // otherwise, it was performed INSERT
  }

  // this method gets the total number of users, last registered user, and online users
  private function usersOn($sl=0) {
    $re = array('total'=>0, 'last'=>'', 'online'=>0);        // this array will be added in $users property
    $dt = time();
    $timexpir = $dt-120;         // Current time minus 2 minutes

    // deletes rows in the 'useron' table, older than 2 minutes
    $sql = "DELETE FROM `useron` WHERE `dt`<$timexpir";
    $this->sqlExecute($sql);


    if(isset($_SESSION['email']) && isset($_SESSION['username']) && !isset($_SESSION['fbuserid']) && !isset($_SESSION['usropenid'])) {
      // add the users, or if already in the table, update date-time
      $upd_sid = ($sl==1) ? ", `sid`='$this->sid'" : '';       //if $sl is 1 (the user is logging) sets to update the SID too
      $sql = "INSERT INTO `useron` (`email`, `name`, `sid`, `dt`) VALUES ('".$_SESSION['email']."', '".$_SESSION['username']."', '$this->sid', $dt) ON DUPLICATE KEY UPDATE `dt`=$dt". $upd_sid;
      $this->sqlExecute($sql);
    }

    // select that gets the total number of users, last registered user, and online users
    $sql = "SELECT `useron`.`name`, `useron`.`sid`, (SELECT count(*) FROM `users`) AS nrusers, (SELECT `name` FROM `users` WHERE `rank`>0 ORDER BY `id` DESC LIMIT 1) AS last FROM `useron`";

    // if the select returns at least one row
    if(($redb = $this->sqlExecute($sql)) && $this->affected_rows > 0) {
      // parse each row from result set
      for($i=0; $i<$this->affected_rows; $i++) {
        $useron = $redb[$i]['name'];
        if($useron !== NULL) $numeon[] = '<a href="'.$this->protocol.$this->site.TOUSRF.'?usr='.$useron.'" title="'.$useron.'">'.$useron.'</a>';

        // if $_SESSION['username'] exists and the SID from table is different from $sid, increment $nr_logs
        if(isset($_SESSION['username'])) {
          if(strtolower($useron)==strtolower($_SESSION['username']) && $redb[$i]['sid']!==$this->sid) $this->nr_logs++;
        }

        // adds the total number of users, last registered user, and online users in $nrusers property
        $re['total'] = $redb[$i]['nrusers'];
        $re['last'] = '<a href="'.$this->protocol.$this->site.TOUSRF.'?usr='.$redb[$i]['last'].'" title="'.$redb[$i]['last'].'">'.$redb[$i]['last'].'</a><br/><span id="allusr">'.$this->lsite['allusr'].'</span>';
        $re['online'] = implode('<br/>', $numeon);
      }
    }
    else {
      // if 0 returned rows, perform another Select for total users (when 'useron' is empty, the "nrusers" also returns 0)
      $sql = "SELECT `name` AS last, (SELECT count(*) FROM `users`) AS nrusers FROM `users` WHERE `rank`>0 ORDER BY `id` DESC LIMIT 1";
      $redb = $this->sqlExecute($sql);
      if($this->affected_rows > 0) {
        $re['total'] = $redb[0]['nrusers'];
        $re['last'] = '<a href="'.$this->protocol.$this->site.TOUSRF.'?usr='.$redb[0]['last'].'" title="'.$redb[0]['last'].'">'.$redb[0]['last'].'</a><br/><span id="allusr">'.$this->lsite['allusr'].'</span>';
      }
    }

    $this->users = $re;          // adds in the $users property the array stored in $re
  }

  // returns html table wit paginated list with total users, receives the column name for Order By
  public function allUsers($order='name', $findusr='') {
    // start html table
    $usrtable = '<table><thead><tr><th>Nr</th><th>'.$this->lsite['name'].'</th><th>'.$this->lsite['allusers']['dtreg'].'</th><th>'.$this->lsite['userpage']['dtvisit'].'</th><th>'.$this->lsite['allusers']['visit'].'</th><th>'.$this->lsite['rank'].'</th>';            // start table that will contain data with users to return

    // sets mysql table in which to select (0=Registered, else: 'fb'=Facebook users, 'yh'=Yahoo, 'go'=Google users)
    if(isset($_REQUEST['usr'])) {
      if($_REQUEST['usr'] == '0') $table = 'users';
      else $table = $_REQUEST['usr'].'users';

      // sets WHERE, when it is request to find users by name oe email
      $where = (strlen($findusr) > 2) ? "WHERE `". $order ."` LIKE '%". $findusr ."%'" : '';

      // sets Sql order by, adding COLLATE for string columns (to select case-insensitive)
      $orderby = in_array($order, array('name', 'email')) ? 'ORDER BY `'.$order.'` COLLATE utf8_general_ci ASC' : 'ORDER BY `'.$order.'` ASC';
      // if $order not 'id', add to order by second column 'id'
      if($order != 'id') $orderby .= ', `id` ASC';

      // gets html table with users list, and calls the method that sets $linkspgs property with pagination links
      if($rows = $this->getMysqlRows('*', $table, $where, $orderby)) {
        $usrtable .= $this->setAllUsers($rows, $table);

        // data added in pagination links when is request to find user by name or email (else, empty)
        $pgs_fusr = (strlen($findusr) > 2) ? '&amp;findusr='. $findusr : '';
        $this->setLinkspgs(USRFILE.'?usr='. $_REQUEST['usr'] .'&amp;order='.$order.$pgs_fusr.'&amp;nrp=');   // pagination links
      }
    }

    // if Admin (Rank 9), returns with buttons to delete selected user/s
    if(isset($_SESSION['rank']) && $_SESSION['rank']==9) {
      return '<div class="btndel"><button onclick="delUsers();">'.$this->lsite['delsel'].'</button></div>'. $usrtable .'</tbody></table><div class="btndel"><button onclick="delUsers();">'.$this->lsite['delsel'].'</button></div>';
    }
    else return $usrtable.'</tbody></table>';
  }

  // sets /returns the rows and columns for html table with all users (different for admin, and visitors)
  // receives the rows with users from mysql database, the table-name (to add in SetRank and Delete)
  private function setAllUsers($rows, $table) {
    $nr_usr = $this->startrow;              // to show the user number, and alternate class

    // if Admin (Rank 9), adds columns for changing rang, and checkbox for delete user
    if(isset($_SESSION['rank']) && $_SESSION['rank']==9) {
      $usrtable = '<th>E-mail</th><th>'.$this->lsite['allusers']['usrid'].'</th><th>'.$this->lsite['userpage']['setrank'].'</th><th>'.$this->lsite['delusr'].'</th></tr></thead><tbody>';
    }
    else $usrtable = '</tr></thead><tbody>';        // else, closes the row with titles

    for($i=0; $i<$this->affected_rows; $i++) {
      // sets the link for user page, default Registered; chage it for Facebook users, and no link for Google users
      $userpage = '<a href="'.$this->protocol.$this->site.TOUSRF.'?usr='.$rows[$i]['name'].'" title="'.$rows[$i]['name'].'">'.$rows[$i]['name'].'</a>';

      if(isset($_REQUEST['usr']) && $_REQUEST['usr'] == 'fb') $userpage = '<a href="http://www.facebook.com/profile.php?id='.$rows[$i]['fbuserid'].'" target="_blank" title="'.$rows[$i]['name'].'">'.$rows[$i]['name'].'</a>';
      else if(isset($_REQUEST['usr']) && ($_REQUEST['usr'] == 'yh' || $_REQUEST['usr'] == 'go')) $userpage = $rows[$i]['name'];

      $dtvisit = $rows[$i]['dtvisit']>11111111 ? date('j-M-Y, H:i', $rows[$i]['dtvisit']) : $this->lsite['userpage']['notloged'];           // Set Not logged if no logged time registered
      $nr_usr++;

      // if Admin (Rank 9), show to set user`s rank, and checkbox to delete, else, links to user page
      if(isset($_SESSION['rank']) && $_SESSION['rank']==9) {
        GLOBAL  $functions;      // gets the object with functions (for setRank())

        $usrtable .= '<tr class="tr'.($nr_usr%2).'"><td>'.$nr_usr.'</td>
        <td>'. $userpage .'</td>
        <td>'.date('j-M-Y, H:i', $rows[$i]['dtreg']).'</td><td>'.$dtvisit.'</td><td>'.$rows[$i]['visits'].'</td><td>'.$rows[$i]['rank'].'</td><td>'.$rows[$i]['email'].'</td><td>'.$rows[$i]['id'].'</td>
        <td id="rerank'.$i.'">'.$this->lsite['userpage']['setrank'].': '. $functions->setRank($table, $rows[$i]['rank'], $rows[$i]['id'], 'setrank'.$i,  'rerank'.$i). '</td><td><input type="checkbox" name="delusr" value="'.$rows[$i]['id'].'" id="delusr'.$i.'" class="delusr" title="'. $table .'" /><label for="delusr'.$i.'">'.$this->lsite['delete'].'</label></td></tr>';
      }
      else {
        $usrtable .= '<tr class="tr'.($nr_usr%2).'"><td>'.$nr_usr.'</td><td>'. $userpage .'</td>
        <td>'.date('j-M-Y, H:i', $rows[$i]['dtreg']).'</td><td>'.$dtvisit.'</td><td>'.$rows[$i]['visits'].'</td><td>'.$rows[$i]['rank'].'</td></tr>';
      }
    }

    return $usrtable;
  }

  // the method for LogOut
  private function logOut($rd=1) {
    // if there are cookies for email and password, sets to remove them
    if(isset($_COOKIE['cookmail']) && isset($_COOKIE['cookpass'])){
      setcookie("cookmail", "", time()-60*60*24*100, "/");
      setcookie("cookpass", "", time()-60*60*24*100, "/");
    }

    // if $rd parameter different from 2, delete the user from the online-users
    if($rd!==2 && isset($_SESSION['email'])) {
      $sql = "DELETE FROM `useron` WHERE `email`='".$_SESSION['email']."'";
      $this->sqlExecute($sql);
    }

    // delete Session
    if(isset($_SESSION)) $_SESSION = null;
    session_destroy();

      // if $rd=1, auto-redirect to avoid resending data on refresh
    if($rd == 1) echo '<meta http-equiv="Refresh" content="1;url=/"><script type="text/javascript">alert("'.$this->lsite['users_loged']['alertlout'].'");</script>';
    else if($rd == 2) echo '<meta http-equiv="Refresh" content="1;url=/"><script type="text/javascript">alert("'.$this->lsite['users_loged']['forcelout'].'");</script>';
    exit;
  }
}