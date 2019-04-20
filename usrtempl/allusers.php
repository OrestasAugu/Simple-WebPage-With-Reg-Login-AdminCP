<?php
// for All Users page, "usr" in URL
if(isset($_REQUEST['usr'])) {
  if(!isset($objUsers)) $objUsers = new Users($mysql);       // create instance of Users class, if not created
  $objUsers->rowsperpage = 30;               // number of users paginated in the page with all users
  $order = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'name';
  $findusr = (isset($_REQUEST['findusr']) && strlen($_REQUEST['findusr']) > 2) ? $_REQUEST['findusr'] : '';
  $usr = $_REQUEST['usr'];
  $usrtable = $objUsers->allUsers($order, $findusr);                   // table with users

  // if not request from Ajax, sets to output all "center" page content, else, only links and table
  if(ISAJAX === 0) {
    // the select list to select users by various columns (adds 'email' for Admin (rank 9))
    $optmail = (isset($_SESSION['rank']) && $_SESSION['rank']==9) ? '<option value="email">E-mail</option>' : '';
    $orderusr = '<label for="selalllusr">'. $lsite['selby'].'</label> : <select id="selalllusr" onchange="orderUsr(this.value, \''. $usr .'\')"><option value="name">'.$lsite['name'].'</option><option value="dtreg">'.$lsite['allusers']['dtreg'].'</option><option value="dtvisit">'.$lsite['userpage']['dtvisit'].'</option><option value="visits">'.$lsite['allusers']['visit'].'</option><option value="rank">'.$lsite['rank'].'</option>'.$optmail.'</select>';

    // text field and radio buttons for find users by name or email
    $findusrby = sprintf($lsite['allusers']['findusr'], '<input type="text" name="findusr" id="findusr" maxlength="55" />') .'<input type="radio" name="findusrby" id="rfusrn" value="name" onclick="findUsr(this, \''. $usr .'\');" /><label for="rfusrn">'. $lsite['name'] .'</label>';
    // adds to find user by email, if admin loged
    if(isset($_SESSION['rank']) && $_SESSION['rank']==9) $findusrby .= ' / <input type="radio" name="findusrby" id="rfusre" value="email" onclick="findUsr(this, \''. $usr .'\');" /><label for="rfusre">'. $lsite['users_logform']['email'] .'</label>';

    // set class for changing style of current tab list
    $regusrcls = ($_REQUEST['usr'] == '0') ? ' class="etabvi"' : '';
    $fbusrcls = ($_REQUEST['usr'] == 'fb') ? ' class="etabvi"' : '';
    $yhusrcls = ($_REQUEST['usr'] == 'yh') ? ' class="etabvi"' : '';
    $gousrcls = ($_REQUEST['usr'] == 'go') ? ' class="etabvi"' : '';

    echo '<div id="getallusr">
    <a href="'. $_SERVER['PHP_SELF'] .'?usr=0" title="'. $lsite['allusers']['allregusr'] .'" id="allregusr"'. $regusrcls .'>'. $lsite['allusers']['allregusr'] .'</a>'.
    ((FBCONN === 1) ? '<a href="'. $_SERVER['PHP_SELF'] .'?usr=fb" title="'. $lsite['allusers']['allfbusr'] .'" id="allfbusr"'. $fbusrcls .'>'. $lsite['allusers']['allfbusr'] .'</a>' : '').
    ((YHCONN === 1) ? '<a href="'. $_SERVER['PHP_SELF'] .'?usr=yh" title="'. $lsite['allusers']['allyhusr'] .'" id="allyhusr"'. $yhusrcls .'>'. $lsite['allusers']['allyhusr'] .'</a>' : '').
    ((GOCONN === 1) ? '<a href="'. $_SERVER['PHP_SELF'] .'?usr=go" title="'. $lsite['allusers']['allgousr'] .'" id="allgousr"'. $gousrcls .'>'. $lsite['allusers']['allgousr'] .'</a>' : '').
    '</div>
    <section id="center"><div id="dvfindusr">'. $orderusr. $findusrby .'</div><div id="allusers">'.$objUsers->linkspgs.$usrtable.$objUsers->linkspgs.'</div></section>';
  }
  else echo $objUsers->linkspgs.$usrtable.$objUsers->linkspgs;
}