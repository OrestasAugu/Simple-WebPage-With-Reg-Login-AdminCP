<?php
if(!defined('ISAJAX')) include('config.php');       
include('class.Msgs.php');
$objMsg = new Msgs($mysql);

// if request to get users messages
if(isset($_GET['usr'])) {
  // button to delete selected messages
  $delmsg = (isset($_SESSION['usritspage']) && $_SESSION['usritspage']===1) ? '<button onclick="delMsg(\'delsel\');">'.$lsite['delsel'].'</button><br class="clr"/>' : '';

  ob_start();           // start storing output in buffer (comments area)
  echo $delmsg.$objMsg->linkspgs;      // Afiseaza link-uri deasupra
  echo $objMsg->msgs;              // Afisare comentarii
  echo $delmsg.$objMsg->linkspgs;        // Afiseaza link-uri dedesubt
  if(isset($_SESSION['usritspage']) && $_SESSION['usritspage']===1) {
    include(USRTEMPL.'msgdel.php');     // if admin, or loged user in his page include form used to delete messages
  }
  $msgs = ob_get_contents();         // stores the buffer content
  ob_end_clean();

  // if not access from POST, with 'isajax', adds messages content in DIV, and form to add comment
  if(!isset($_POST['isajax'])) {
    ob_start();           // start seccond buffer

    // add messages content into a DIV
    $msgs = '<h2 id="coments_t"> &#1449; '.$lsite['msgs']['title'].' <i>('.$objMsg->totalrows.')</i></h2>
    <div id="coments">'.$msgs.'</div>';

    // if $_SESSION['username'] not defined, show message that only logged user can add comment
    // else, include the form
    if(!isset($_SESSION['username'])) echo '<h3 id="adcomm">'.$lsite['msgs']['allowmsg'].'</h3>';
    else include(USRTEMPL.'msgadd.php');          // Include formularul pt. adaugare comentarii

    $msgs .= ob_get_contents();         // adds seccond the buffer content
    ob_end_clean();
  }

  if(isset($_REQUEST['nrp'])) echo $msgs;      // output the content when Ajax with 'nrp'
}
else if(isset($_POST['sbmt'])) {   // if form submited to add /delete message
  if(isset($_POST['namec']) && isset($_POST['emailc']) && isset($_POST['coment']) && isset($_POST['codev'])) {
    // checks form data, if no error, calls addMsgs(), else, returns JS with error
    $objMsg->checkForm($_POST);
    if($objMsg->eror === false) $objMsg->addMsgs($_POST);
    else echo "<script type=\"text/javascript\">window.parent.resetMsg('".$objMsg->eror."');</script>";
  }
  else if(isset($_POST['id_dcm'])) echo "<script type=\"text/javascript\">window.parent.resetMsg('".$objMsg->delMsg($_POST)."');</script>";
  else echo $objMsg->setEror($site['msgs']['eror_form']);
}
else if(isset($_GET['unsub']) && preg_match('/^[0-9]+_[0-9]+$/', $_GET['unsub'])) {   // URL for unsubscribe
  echo $objMsg->unsubscribe($_GET['unsub']);
}