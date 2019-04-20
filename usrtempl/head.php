<?php
// sets the user name and the title
$usr = '';  $titlusr = 'KompFix - 24/7 Computer Repair Service';
if(isset($_GET['usr'])) {
  
  if(array_key_exists($_GET['usr'], $allusrpg)) {
    $usr = $allusrpg[$_GET['usr']];
    $titlusr = $lsite['allusr']. (($_GET['usr'] == '0') ? ' ( '.$objUsers->users['total'].' )' : $usr);
  }
  else {
    $usr = $_GET['usr'];
    $titlusr = $lsite['userpage']['title']. $usr;
  }
}

// the html code for the <head> area
$htmlhead = '<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>'.$titlusr.'</title>
<link href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400,600,700,900|Quicksand:400,700|Questrial" rel="stylesheet" />
	<link href="default.css" rel="stylesheet" type="text/css" media="all" />
	<link href="fonts.css" rel="stylesheet" type="text/css" media="all" />
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport"       content="width=device-width, initial-scale=1">
   <link rel="stylesheet" href="style/main.css">
<meta name="keywords" content="'.$usr.', users, register, login" />
<meta name="robots" content="ALL" />
<meta name="author" content="MarPlo" />
<link href="'.USRTEMPL.'style.css" rel="stylesheet" type="text/css" />
<link href="'.USRTEMPL.'usrpg.css" rel="stylesheet" type="text/css" />
<!--[if IE]><script src="'.USRJS.'html5.js"></script><![endif]-->
<script src="'.USRJS.'jquery_1.7.js" type="text/javascript"></script>
<script type="text/javascript" src="style/query.js"></script>

         <link rel="stylesheet" href="style/slicknav.css"> 
 
<script src="https://code.jquery.com/jquery-2.1.3.min.js"> 
</script> 
 
<script src="style/jquery.slicknav.min.js"></script> 
<script src="'.USRJS.'functions.js" type="text/javascript"></script>'.jsTexts($lsite);

if(isset($_GET['usr']) && $_GET['usr']!='0' && $_GET['usr']!='fb') $htmlhead .= '<script src="'. USRJS. 'msgs.js" type="text/javascript"></script>';

// If its the page of the logged user, or Admin, adds JS with admin functions
if(isset($_SESSION['usritspage']) && $_SESSION['usritspage']===1) $htmlhead .= '<script src="'.USRJS.'usrloged.js" type="text/javascript"></script>';

$htmlhead .= '</head>';

// sends head zone to browser
echo $htmlhead;
flush();

$htmlhead = '<body>
<div id="header-wrapper">
	<div id="header" class="container">
		<div id="logo">
        	<span class="icon icon-cog"></span>
			<h1><a href="#">KompFix</a></h1>
			<h1><a href="#">24/7 Computer Repair Service</a></h1>
		</div>
        <nav class="mobilemenu"></nav> 
            <nav class="nav_menu">
		<div id="menu">
			<ul>
            <li><a href="index.php" accesskey="1" title="">Homepage</a></li>   
				<li><a href="services.php" accesskey="2" title="">Service List</a></li>
				<li><a href="contact.php" accesskey="3" title="">Contact us</a></li>
			</ul>
		</div>
	</div>
</div>
</nav>
<header id="header">';


$htmlhead .= $login;          // adds login form / message

$htmlhead .= '<h2 id="titlusr">'. $titlusr. '</h2></header>';

echo $htmlhead;          // output the html code