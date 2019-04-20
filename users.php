<?php

include('usrincls/config.php');

define('USRFILE', basename(__FILE__));            // stores the name of this file
define('TOUSRF', rtrim(dirname($_SERVER['SCRIPT_NAME']), DIRECTORY_SEPARATOR).'/'.USRFILE);       // path_name of this file

// If no Submit, Recovery request, or select/order AllUsers, create object instance to work with Users class
if(isset($_REQUEST['susr']) || isset($_REQUEST['rc']) || isset($_REQUEST['order']) || isset($_REQUEST['order'])) {
  $login = '';
}
else {
  $objUsers = new Users($mysql);
  $login = $objUsers->loged;           // get the login form or loged message

  // if no ajax request, adds JS "texts", and "functions.js" to $login (if not dirrect access to this file)
  if(ISAJAX === 0) {
    if(basename($_SERVER['PHP_SELF']) != USRFILE) $login .= jsTexts($lsite).'<script src="'.USRJS.'functions.js" type="text/javascript"></script>';
  }
  else echo $login;
}

// sets Session to recognize user logged is on its page, or Admin ($_SESSION['rank'] is 9)
$_SESSION['usritspage'] = ((isset($_SESSION['username']) && isset($_REQUEST['usr']) && strtolower($_SESSION['username'])==strtolower($_REQUEST['usr'])) || (isset($_SESSION['rank']) && $_SESSION['rank']==9)) ? 1 : 0;

// if direct access on this file, data via GET['usr'] or POST with index 'susr', 'mp', 'rc' (Confirm / Recover)
if(basename($_SERVER['PHP_SELF'])==USRFILE && (isset($_REQUEST['usr']) || isset($_REQUEST['susr']) || isset($_GET['mp']) || isset($_REQUEST['rc']))) {
  if(ISAJAX === 0) include(USRTEMPL.'head.php');        // if not Ajax request, include head.php (from USRTEMPL)
  include(USRINCLS.'class.UsersReg.php');        // include the class for Register (used also for Recovery data)

  ob_start();           // start storing output in buffer

  // if 'susr'=Register, create objet of UsersReg (for Register)
  // if 'rc' or 'mp' (for Recover-Confirma), uses UsersRecov class
  // if 'usr', create object of UserPage class (for user page data)
  if(isset($_REQUEST['susr']) && $_REQUEST['susr']==$lsite['users_logform']['register']) {
    $objRe = new UsersReg($mysql);
    echo $objRe->result;
  }
  else if(isset($_REQUEST['rc']) || isset($_GET['mp'])) {
    include(USRINCLS.'class.UsersRecov.php');
    $objRe = new UsersRecov($mysql);
  }
  else if(isset($_REQUEST['usr'])) {
    // if "usr" not key in $allusrpg (defined in 'config.php'), include to show user page, else, show list with total users
    if(!array_key_exists($_REQUEST['usr'], $allusrpg)) {
      include(USRINCLS.'class.UserPage.php');
      $objUsrPg = new UserPage($mysql);

      // if not Submit, calls the getUser() method, that returns an Array with user data (or false), and getFav()
      if(!isset($_REQUEST['susr'])) {
        if($usrdat = $objUsrPg->getUser($_REQUEST['usr'])) $favorites = $objUsrPg->getFav($usrdat['fav']);
        else echo sprintf($lsite['userpage']['eror_notusr'], $_REQUEST['usr']);
      }
    }
    else include(USRTEMPL.'allusers.php');      
  }

  $reout = ob_get_contents();         // stores the buffer content
  ob_end_clean();
  echo $reout;

  // if user exists, $usrdat is array, GET['usr'] not in $allusrpg, and not Ajax, include body user page
  if(isset($usrdat) && is_array($usrdat) && isset($_GET['usr']) && !array_key_exists($_GET['usr'], $allusrpg) && ISAJAX===0) include(USRTEMPL.'usrbody.php');
  if(ISAJAX === 0) include(USRTEMPL.'footer.php');       // if not Ajax request, include footer.php (from templ/)
}