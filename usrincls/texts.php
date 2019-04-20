<?php
// Texts added in script 
$en_site = array(
  'max'=>'Maximum',
  'code'=>'Code: ',
  'rank'=>'Rank',
  'title'=>'Title: ',
  'name'=>'Name',
  'pronoun'=>'Pronoun: ',
  'favorites'=>'Favorites',
  'birthday'=>'Birthday: ',
  'location'=>'Location: ',
  'country'=>'Country: ',
  'city'=>'City: ',
  'address'=>'Address: ',
  'day'=>'Day: ',
  'month'=>'Month: ',
  'year'=>'Year: ',
  'pass'=>'Password: ',
  'site'=>'Your website: ',
  'loading'=>'Loading...',
  'modify'=>'Modify',
  'delete'=>'Delete',
  'delsel'=>'Delete selected',
  'delusr'=>'Delete User',
  'confdel'=>'The selected element will be deleted without possibility of recovery.\nIf you want to delete, click OK',
  'close'=>'Close',
  'codev'=>'Verification code: ',
  'codev0'=>'Add this verification code: ',
  'delok'=>' - Successfully deleted',
  'unsubscribe'=>'Unsubscribe',
  'prev'=>'Previous',
  'first'=>'First',
  'next'=>'Next',
  'last'=>'Last',
  'send'=>'Send',
  'allusr'=>'All Users',
  'selby'=>'Select by',
  'eror_name'=>'The Name must contain between 3 and 32 characters. \n Only letters, numbers, - and _',
  'eror_pass'=>'Incorrect password',
  'eror_email'=>'Add a correct e-mail address',
  'eror_codev'=>'Incorrect verification code',
  'eror_noselchk'=>'Not selected element to delete',
  'eror_delfile'=>'Unable to delete the file: ',
  'eror_rnkdeladmin'=>'The first Admin can not be deleted nor his rank changed',
  'eror_base'=>array(
    'construct'=>'The argument in accesing the class must be an Array',
    'setconn'=>'Unable to connect to MySQL: ',
    'sqlexecute'=>'Can`t execute the sql query',
    'upext'=>'Error: The file %s has not an allowed extension type',
    'upmaxsize'=>'Error: The file %s exceeds the allowed size %s KB',
    'upimgwh'=>'Error: image width and height must be maximum %s x %s',
    'upfiledb'=>'Error: The file path could not be added in database: ',
    'upfile'=>'Error: Unable to Upload the file: %s'
  ),
  'msgs'=>array(
    'title'=>'Messages',
    'title2'=>'Messages posted by visitors',
    'usrpg'=>'%s page',
    'allowmsg'=>'To add messages you must be logged in',
    'nomsg'=>'No messages',
    'addmsg'=>'Add Message',
    'fcamail'=>'Notify me when new message',
    'fcupimg'=>'Optional, you can include a picture',
    'fcshowmail'=>'Display my e-mail',
    'nrchrtxt'=>'Remaining characters: ',
    'chrmsg'=>'Write your message (<span id="countdown">Maximum 600 characters</span>)',
    'jsadd'=>'Thank you %s \\n Your message was added.',
    'jsdelete'=>'Message(s) successfully deleted',
    'notifysub'=>'New message added on %s',
    'notifymsg'=>'Hi,<br/>
      Email sent from %s <br/>
      New message added on the website %s .<br/>
      You can see the new message on the page: %s .<br/><br/><br/>
      If you want not to receive other notifications when new messages are added on that page, to unsubscribe click on this link:<br/>
      %s , or copy and access it in your browser.<br/><br/>
      <i>With respect,<br/>
      Admin</i>',
    'unstitl'=>'Unsubscribe notification on new messages',
    'unsmsg'=>'To unsubscribe, add your e-mail address',
    'unsubscribe'=>'Succesfully unsubscribed',
    'noreset'=>'No new messages after the last reset',
    'lastreset'=>'Last reset was done on the message added on',
    'newmsg'=>'Latest Messages Posted to all Users',
    'newmsgad'=>'Since then were added the following messages:',
    'resetdt'=>'Reset date of the last check',
    'eror_form'=>'Not all form fields are received',
    'eror_sesadd'=>'You already added a message in the last 5 minutes',
    'eror_msg'=>'The message must contain between 5 and 600 characters (including the tags)',
    'eror_logadmin'=>'Incorrect admin Name or Password',
    'eror_maxchrtxt'=>'You can add maximum 600 characters',
    'eror_delete'=>'Unable to delete the messages in database, ',
    'eror_resetcheck'=>'Reseting time of the last check failed',
    'eror_sesunsub'=>'You already unsubscribed',
    'eror_unsubscribe'=>'Incorrect URL address to unsubscribe',
    'eror_unsub'=>'Error on update data for Unsubscribe notification'
  ),
  'datetime'=>array(
    'sec'=>'second/s',
    'min'=>'minute/s',
    'd'=>'day/s',
    'm'=>'month/s',
    'h'=>'hour/s',
    'y'=>'year/s'
  ),
  // for Users class
  'users_logform'=>array(
    'email'=>'E-Mail',
    'name'=>' Name: ',
    'pass'=>' Password: ',
    'rem'=>'Remember',
    'recdat'=>'Forgot your password?',
    'orlogw'=>'Or Log In with:',
    'fblogin'=>'Login with Facebook',
    'yhlogin'=>'Login with Yahoo',
    'gologin'=>'Login with Google',
    'myacc'=>'My Account',
    'login'=>'LOGIN',
    'register'=>'Register'
  ),
  'users_loged'=>array(
    'userpage'=>'Personal page',
    'lout'=>'LogOut',
    'alertlout'=>'Logged Out',
    'forcelout'=>'Logged Out\nThere is another Login with this account.\nYou can re-login'
  ),
  'register'=>array(
    'regmsg'=>'<center><h1>Succes!</h1>
    <font size="4">Thank you <b><font color="blue">%s</font></b>, the registration has been completed successfully.</font><br/><br/>You can Log in.</center>',
    'mailsubject'=>'Confirm the registration on: ',
    'mailmsg'=>"               Hi, <br/>
    You received this message because you have to confirm your registration on the website %s <br/><br/>
To confirm the registration, click on the following link (<i>or copy it in the address bar of your browser</i>):<br/><br/>
      <center><b> %s </b></center><br/><br/>
    Your login data:<br/><br/>
      E-Mail = %s <br/>
      Password = %s <br/><br/><br/>
<i>Thanks, respectfully,<br/> Admin</i><br/>",
    'mailsent'=>'<center><h3>Registration performed successfully</h3>A message with a link to confirm your registration will be send to the e-mail<u> %s </u>.<br/><br/> If you have not received the email, check the Spamm folder, too.<br/><br/> After confirmation you can log in.</center>',
    'regtxt'=>'<h2>Registration</h2><div id="form_re">
      <p><br/><b> - Add your registration data and this code: <span  id="codev0">%s</span></b></p>- <i>You must use a valid e-mail address, you will receive a message to confirm the registration.</i><hr style="width:88px;" /><br/>',
    'pass2'=>'Retype password: ',
  ),
  'recov'=>array(
    'eror_re'=>'<div class="eror">Error when checking your data.<br/>Try again.</div><br/>',
    'eror_confirm'=>'Confirmation failed, error: ',
    'formcodev'=>'<div id="form_re">
  <p>Add the e-mail address you used for registration and this verification code: <span id="codev0">%s</span></p><br/>',
    'mailsubj1'=>'Recovery registration data',
    'mailmsg1'=>"               Hy<br/> \n
          You received this email due to a request to recover your registration data on %s \n\n",
    'mailsubj2'=>'Registration Confirmation',
    'mailmsg2'=>"               Hi<br/> \n
          You received this email due to a request to resend the link for registration confirmation.<br/> \n\n
      To confirm the registration on %s , click on the following link:<br/><br/> \n
      %s <br/> \n\n",
    'mailmsgld'=>"<br/>Your login data are:<br/><br/> \n
              E-Mail = %s <br/> \n
              Password = %s <br/><br/> \n\n
        <br/>Have a good day<br/> \n
        With respect, Admin",
    're'=>'<center>The requested data are sent to: <b> %s </b>.<br/>
          Check the Spamm folder, too. If you have not received the email, please contact the site administrator.
          <br/><br/>Thank you</center>',
    'reconfirm'=>'<center><h2 style="color:blue;">Confirmation approved</h2><h4>Now you can log on the site. <a href="/">Home Page</a></h4></center>',
    'reunconfirm'=>'<center><font color="red"><h2>Confirmation Unapproved</h2></font><h4>The URL for confirmation is incorrect</h4><br/><br/> - To request a new e-mail with the link for confirmation: %s <br/><br/><i>Or contact the site administrator.</i></center>'
  ),
  'userpage'=>array(
    'title'=>'Members Area: ',
    'setrankmsg'=>'Change Rank, from -1 to 9 (<i>-1 = banned, 0 = unconfirmed, 9 = Administrator</i>)',
    'setrank'=>'Set Rank',
    'setrankok'=>'<b style="color:blue;">Rank updated</b>',
    'dtreg'=>'Registered date',
    'dtvisit'=>'Last logged date',
    'notloged'=>'Not logged yet',
    'visits'=>'Visits number: ',
    'usrdata'=>'Additional Data',
    'modfdata'=>'Your data successfully updated',
    'adimg'=>'Add image:',
    'forupimg'=>'Upload / Change image',
    'totalusr'=>'Total registered users: ',
    'newusr'=>'Newest User: ',
    'online'=>'Online users:',
    'changeep'=>'Change E-mail /Password',
    'editopt'=>'Edit optional data',
    'ocupation'=>'Occupation:',
    'interes'=>'Interests / Hobbies:',
    'editreg'=>'Edit registration data',
    'chgmail'=>'If you change the e-mail address, you will receive a link to the new e-mail address, to confirm it.',
    'pass'=>'Current password:',
    'passnew'=>'New password:',
    'transmit'=>'Things I want to say:',
    'aditionals'=>'Aditional Data',
    'optionals'=>'Optional Data',
    'nofav'=>'Not favorite links',
    'adfav'=>'Add Favorite link (without http://)<br/><i>Each Link, and Name can have maximum 110 characters</i>',
    'favhave'=>'You can have maximum 12 Favorite links',
    'adfavok'=>'Favorite link successfully registered',
    'adfavbt'=>'Add Favorite',
    'max500chr'=>'You can add maximum 500 characters',
    'max1000chr'=>'You can add maximum 1000 characters',
    'maxoptdata'=>' Maximum characters allowed',
    'usetags'=>'In the last text area you can use these BBCODE for HTML format:<br/>[b]text[/b] = <i>&lt;b&gt;text&lt;/b&gt;</i> / [i]text[/i] = <i>&lt;i&gt;text&lt;/i&gt;</i><br/>[u]text[/u] = <i>&lt;u&gt;text&lt;/u&gt;</i><br/>[block]text[/block] = <i>&lt;blockquote&gt;text&lt;/blockquote&gt;</i>',
    'mailsubject'=>'Registration data updated',
    'mailmsg'=>"            Hi,<br/><br/>
              Your new registration data on the website %s :<br/> %s <br/>
        E-mail = %s <br/>
        Password = %s <br/><br/><br/>
  <i>Respectfully,<br/> Admin</i><br/><center>",
    'mailsent'=>'\n An email with your new data is sent to: %s',
    'regdata'=>'Your data was successfully registered',
    'eror_urlformat'=>'Incorrect URL address.\n Add an URL address without http:// ',
    'eror_notusr'=>'<h2>User "<i>%s</i>" not registered</h2>',
    'eror_erortitl'=>'The Title must contain between 3 and 110 characters',
    'eror_moddata'=>'Error: Incomplete fields from form',
    'eror_pass'=>'Error: Incorrect current password',
    'eror_modmp'=>'Error: Accessing modfMP with incorrect data',
    'eror_regdata'=>'Error: Your optional data could not be saved: '
  ),
  'allusers'=>array(
    'allyhusr'=>'Yahoo Users',
    'allgousr'=>'Google Users',
    'allfbusr'=>'Facebook Users',
    'allregusr'=>'Registered Users',
    'findusr'=>'&nbsp; &nbsp; // Or, Find Users with: %s in: ',
    'usrid'=>'User ID',
    'dtreg'=>'Registered Date',
    'visit'=>'Visits',
  ),
  'eror_users'=>array(
    'eror_regmail'=>'<div class="eror">There is no registration with this e-mail:<br/><i><u> %s </u></i></div><br/>',
    'username'=>'The Name must contain between 3 and 32 characters. \n Only letters, numbers, - and _',
    'insession'=>'Incorrect data logging session',
    'inpass'=>'Incorrect password',
    'ban'=>'The account: <b><em>%s</em></b> is banned.<br/>Contact the Website Admin',
    'datachr'=>'The data should contain only letters, numbers, - and _',
    'pass'=>'The Password must contain between 7 and 18 characters. \n Only letters, numbers, - and _',
    'unconfirmed'=>'<center><h4 class="eror">Registration for <u>%s</u> is unconfirmed.</h4>Check your e-mail used for registration (including in Spamm directory), for the message with the confirmation link.<br/><br/>If you want to request a new confirmation mail <a href="%s?rc=Confirm" id="reconfirm">Re-Confirm</a></center>',
    'logattempt'=>'Exceeding number of login attempts.<br/>You can retry after:<br/><b>%s</b>',
    'findusr'=>'The Text field must contain between 3 and 55 characters. \n Only letters, numbers, space, dot, @, - and _'
  ),
  'eror_reg'=>array(
    'nofields'=>'Incorrect form fields',
    'construct'=>'The first parameter should be an array',
    'sendmailreg'=>'The email with the confirmation link can`t be sent',
    'register'=>'<h4>Error:</h4><i> %s </i><br/>Unable to perform your registration for the E-Mail: <b> %s </b>.',
    'pass2'=>'You must write the same password in the field. Retype password',
    'passnew'=>'The New Password must contains minimum 7 characters. \n Only letters, numbers, - and _',
    'namexist'=>"The name: <u> %s </u> already registered, please choose other name",
    'mailexist'=>"The e-mail: <u> %s </u> is already used for registration",
    'ipexist'=>'There is already a registration with your IP.<br/>If you think that is an error, contact the administrator'
  )
);
// Sets an json object for JavaScript with text messages according to language set
function jsTexts($lsite) {
  // define the JavaScript json object
$texts = 'var texts = {
 "loading":"<h4 id=\"loading\">'.$lsite['loading'].'</h4>",
 "lout":"'.$lsite['users_loged']['lout'].'",
 "username":"'.$lsite['eror_users']['username'].'",
 "pass":"'.$lsite['eror_users']['pass'].'",
 "pass2":"'.$lsite['eror_reg']['pass2'].'",
 "passnew":"'.$lsite['eror_reg']['passnew'].'",
 "myacc":"'.$lsite['users_logform']['myacc'].'",
 "register":"'.$lsite['users_logform']['register'].'",
 "urlformat":"'.$lsite['userpage']['eror_urlformat'].'",
 "erortitl":"'.$lsite['userpage']['eror_erortitl'].'",
 "maxchrtxt":"'.$lsite['msgs']['eror_maxchrtxt'].'",
 "maxoptdata":"'.$lsite['userpage']['maxoptdata'].'",
 "nrchrtxt":"'.$lsite['msgs']['nrchrtxt'].'",
 "name":"'.$lsite['eror_name'].'",
 "email":"'.$lsite['eror_email'].'",
 "coment":"'.$lsite['msgs']['eror_msg'].'",
 "codev":"'.$lsite['eror_codev'].'",
 "noupext":"'.$lsite['eror_base']['upext'].'",
 "noselchk":"'.$lsite['eror_noselchk'].'",
 "confdel":"'.$lsite['confdel'].'",
 "err_findusr":"'.$lsite['eror_users']['findusr'].'"
};';

  return '<script type="text/javascript"><!--'.PHP_EOL.
  $texts.PHP_EOL.
  '//-->
  </script>';
}