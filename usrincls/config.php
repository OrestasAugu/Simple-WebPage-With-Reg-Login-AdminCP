<?php
// start session (if isn't started), and header for utf-8
if(!isset($_SESSION)) session_start();
if(!headers_sent()) header('Content-type: text/html; charset=utf-8');

      

//data for connecting to MySQL database (MySQL server, user, password, database name)
$mysql['host'] = 'localhost';
$mysql['user'] = '21321323';
$mysql['pass'] = '21321323';
$mysql['bdname'] = '21321323';

//administrator login data, and e-mail
define('ADMINNAME', 'admin');                      // the admin name (at least 3 characters)
define('ADMINPASS', 'westlondon123');                    // the admin password (at least 7 characters)
define('ADMINMAIL', 'default@default.net');         // Here add the Administrator e-mail

//GMail account for sending mails to user (Link confirmation, Recovery data)
//GMail Username and Password to GMAILUSER, and GMAILPASS
define('USEGMAIL', 0);
define('GMAILUSER', 'username@gmail.com');
define('GMAILPASS', 'gmailpass');

date_default_timezone_set('Europe/London');  // set timezone

// FaceBook ID Developer (APP ID), and the secret key (APP Secret) Not used because it shouts an php error and no time to fix it.
define('FBCONN', 0);
if(FBCONN == 1) {
  define('FBID', 'none');
  define('FBSK', 'none');
}

// Value of 1 include button Connect with Yahoo, 0 removes it Not used because it shouts an php error and no time to fix it.
define('YHCONN', 0);

// Value of 1 include button Connect with Google, 0 removes it Not used because it shouts an php error and no time to fix it.
define('GOCONN', 0);

include('texts.php');             // file with the texts for different languages
$lsite = $en_site;               // Gets the language for site ($en_site for English, $ro_site for Romana)


// if RANK is 0, the script will send a link to the user's e-mail, to confirm the registration 
// if RANK > 0, the user can log in immediately after registration
define('RANK', 1);
define('ACCOUNT', 2);  // If the value is different from 1, allow to create multiple accounts with same IP

define('ALLOWIMG', 1);                             // allows upload images in message (1), not allow (0)
define('ALLOWMAIL', 1);                            // allows mail notification when new message (1), not allow (0)
define('ROWSPAGE', 12);                            // numbers of messages displayed in the page

//edit the permissions for the image uploded by User
$imguprule = array(
  'dir' => 'usrimgup/',                // directory to store uploded images
  'allowext' => array('gif', 'jpg', 'jpe', 'png'),        // allowed extensions
  'maxsize' => 200,       // maximum allowed size for the image file, in KiloBytes
  'width' => 800,         // maximum allowed width, in pixeli
  'height' => 600         // maximum allowed height, in pixeli
);

// sets $_SESSION['username'] with the session that script uses to keep logged users
if(isset($_SESSION['username'])) $nameusr = $_SESSION['username'];

// define directories with files used in this script (BASE to start include)
if(basename(dirname($_SERVER['PHP_SELF']))=='usrincls') {
  define('BASE', '../');
}
else define('BASE', '');
define('USRINCLS', BASE.'usrincls/');                        // classes for register /login
define('USRTEMPL', BASE.'usrtempl/');                        // for templates
define('USRJS', BASE.'usrjs/');                              // for .js files

include('functions.php');             // file with functions
$functions->cleanGP();                       // calls the function to clean data sent via GET or POST
include('class.Base.php');              // the main class from which the others are extended

// values for 'usr=' in URL reserved for page with all users lists
$allusrpg = array('0'=>$lsite['allusr'], 'fb'=>' Facebook', 'yh'=>' Yahoo', 'go'=>' Google');

// define a constant used to check if Ajax request
define('ISAJAX', isset($_REQUEST['isajax']) ? $_REQUEST['isajax'] : 0);
include('class.Users.php');         // Include the Users class