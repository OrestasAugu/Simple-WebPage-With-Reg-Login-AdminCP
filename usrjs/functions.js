
var tag_reg = 're_c';            // id of tag used with Ajax, for registration
var log_form = '';               // will store the login form
var regx_chr = /^([A-Za-z0-9_-]+)$/;    // RegExp with the characters allowed in Name
var regx_mail = /^([a-zA-Z0-9]+[a-zA-Z0-9._%-]*@([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,4})$/;    // RegExp for e-mail address
var php_fileusr = 'users.php';             // the php file for users
var clslogin = '<span class="clslogin" onclick="objLogare.adLogInr();">X</span>';
var clsadbox = '<div class="clsadbox" onclick="remBox();">X</div>'; 
var adboxshow = 'adbox_show';         // id of element displayed in adBox
var fbloginpg = 'facebook/index.php';      // the page opened to login with Facebook
var openidloginpg = 'openid/index.php';      // the page opened to login with OpenID (Yahoo, Google)
var order_usr = 'all';        // used in orderUsr() to tell what list-users to select (0=registered, 'fb'=Facebook, 'yh'=Yahoo, 'go'=Google)

  /* Common functions */

// create the element that covers the page
function adBox(add){
  // if "adboxshow" exists, add the "add" into, else, create it
  if(document.getElementById(adboxshow)) document.getElementById(adboxshow).innerHTML = add;
  else {
    var el_adbox = document.createElement('div');
    el_adbox.id = 'adbox';
    el_adbox.style.height = '100%';
    el_adbox.innerHTML = '<div id="adbox_transp"></div><div id="'+adboxshow+'">'+clsadbox+add+'</div>';
    var p_baza = document.body;
    var repr = p_baza.childNodes[0];
    p_baza.insertBefore(el_adbox, repr);
  }
}
// delete the element created with adBox()
function remBox() {
  var child = document.getElementById('adbox');
  var parent = document.body;
  parent.removeChild(child);
}

// hides element with ID in "idh", shows "ids", focus on "idf"
function HideShow(idh, ids, idf) {
  $('#'+idh).slideUp(588, function() {
    if(ids != 0) $('#'+ids).fadeIn(788, function() {if(idf != 0) $('#'+idf).focus();});
  });
}

// Performs Ajax request
function ajaxSend(file_php, datasend, reID) {
  if(datasend != 'o') var datasend = datasend+ '&isajax='+reID;      // adds "isajax" in string with data to be send
  if(reID == adboxshow) adBox(texts['loading']);     // calls the function to create "adboxshow"

  // define and execute jQuery Ajax
  $.ajax({
    type: 'post',
    url: file_php,
    data: datasend,
    beforeSend: function() {
      // if "datasend" not 'o' (call from setInterval() to actualise online user)
      if(datasend != 'o') {
        // before send the request, animate the "height", displays "Loading..." where the response will be placed
        $('#'+reID).animate({height: 'toggle'}, 800, 'swing', function() {
          $('#'+reID).html(texts['loading']); $('#'+reID).fadeIn(300);
        });
      } 
    },
    timeout: 15000,        // sets timeout for the request (15 seconds)
    error: function(xhr, status, error) { alert('Error: '+ xhr.status+ ' - '+ error); },     // alert in case of error
    success: function(response) {
      // if "datasend" not 'o' (call from setInterval() to actualise online user)
      if(datasend != 'o') {
        // if in response there is "texts['lout']", perform Refresh, else, show response
        if(response.search(texts['lout'])>=0) window.location.reload(true);
        else {
          $('#'+reID).fadeOut(300, function() {
            $('#'+reID).slideDown(800);

            // if "reID" is "log_form" add "log_form" after response, if "adboxshow" adds "clsadbox"
            if(reID == 'log_form') $('#'+reID).html(response+log_form);
            else if(reID == adboxshow) $('#'+reID).html(clsadbox+response);
            else $('#'+reID).html(response);

            regEvents();
            if(typeof regEventsMsg == 'function') regEventsMsg();       // register events in regEventsMsg()
          });
        }
      }
    }
  });
  return false;
}

    /* Tab-effect */
var ar_el_idtitl = new Array();      // will store the elements for Tab-effect

// For Tabs effect, parse the elements stored in "ar_el_idtitl", hides each item, then makes visible "vidtitl"
function ftabEfect(ar_el_idtitl, vidtitl) {
  for(var i=0; i<ar_el_idtitl.length; i++) {  
    ar_el_idtitl[i].style.display = 'none';
  }
  vidtitl.style.display = 'block';

  // hides the form for Upload image and display a button in its place
  if(document.getElementById('usrupimg')) document.getElementById('usrupimg').style.display = 'none';
  if(document.getElementById('forupimg')) document.getElementById('forupimg').style.display = 'block';
}

// For Tabs effect
function tabEfect() {
  // if exist tag with id="ultabs"
  if(document.getElementById('ultabs')) {
    // gets the tag with id="ultabs" (an UL), and makes it visible
    var ultabs = document.getElementById('ultabs');
    ultabs.style.display = 'block';
    var litabs = ultabs.getElementsByTagName('li');        // gets all <li> from "ultabs"
    var nrlitabs = litabs.length;

    // traverse "litabs", gets the "title" and adds the element with the ID from title into "ar_el_idtitl"
    // register "onclick" event to each LI, that will call the ftabEfect() function
    for(var i=0; i<nrlitabs; i++) {
      ar_el_idtitl[i] = document.getElementById(litabs[i].title);
      litabs[i].onclick = function() {
        // change CSS class from all LI
        for(var i2=0; i2<nrlitabs; i2++) litabs[i2].className = 'etabh';
        this.className = 'etabvi';           // add CSS class for visible to clicked tab

        var vidtitl = document.getElementById(this.title);
        ftabEfect(ar_el_idtitl, vidtitl);
      }
    }

    // this function will hide al=l items in "ar_el_idtitl", and makes visible the first element
    ftabEfect(ar_el_idtitl, ar_el_idtitl[0]);
  }
}

// check fields in form (frm)
function checkForm(frm) {
  var eror = 0;    // to store errors, determine to return TRUE or FALSE

  // for fields Users
  if(frm.username) var username = frm.username.value;
  if(frm.pass) var pass = frm.pass.value;
  if(frm.pass2) var pass2 = frm.pass2.value;
  if(frm.passnew) var passnew = frm.passnew.value;
  if(frm.email) var email = frm.email.value;
  if(frm.codev) var codev = frm.codev.value;
  if(document.getElementById('codev0')) var codev0 = document.getElementById('codev0').innerHTML;

  // gets form fields values Message
  if(frm.emailc) {
    var email = frm.emailc.value;
    if(email == 'optional') { frm.emailc.value = ''; email = ''; }
  }
  if(frm.coment) var coment = frm.coment.value;

  // check fields value, if incorrect, sets error in "eror"
  // validate the email
  if(frm.email && email.search(regx_mail)==-1) {
    alert(texts['email']);
    eror = frm.email;
  }
  else if(frm.emailc && email.length>0 && email.search(regx_mail)==-1) {
    alert(texts['email']);
    eror = frm.emailc;
  }
  // validate user name
  else if(frm.username && (username.length<3 || username.length>32 || username.search(regx_chr) == -1)) {
    alert(texts['name']);
    eror = frm.username;
  }
  // Check password length and to contains only the characters from "regx_chr"
  else if (frm.pass && (pass.length<7 || pass.search(regx_chr) == -1)) {
    alert(texts['pass']);
    eror = frm.pass;
  }
  // Check if it's the same password in "Retype password"
  else if (frm.pass2 && pass2!=pass) {
    alert(texts['pass2']);
    eror = frm.pass2;
  }
  // Check the length of the new password (the form in "usrbody.php")
  else if (frm.passnew && (passnew.length<7 || passnew.search(regx_chr) == -1)) {
    alert(texts['passnew']);
    eror = frm.passnew;
  }
  // check the verification code
  else if (frm.codev && codev0 && codev!=codev0) {
    alert(texts['codev']);
    eror = frm.codev;
  }
  // check the message /comment
  else if(frm.coment && (coment.length<5 || coment.length>600)) {
    alert(texts['coment']);
    eror = frm.coment;
  }

  // if no error in 'eror', returns true, else, select incorrect field,and returns false
  if (eror==0) return true;
  else {
    eror.focus();
    eror.select();
    return false;
  }
}

  /* For Register /Login */

// create an object to work with login form
var objLogare = new Object();
  objLogare.adLogInr = function() {
    // if exists id="log_form"
    if(document.getElementById('log_form')) {
      // if log_form='' store the login form + Close button
      if(log_form=='') log_form = document.getElementById('log_form').innerHTML+ clslogin;

      // replace the form with "My Account" button
      document.getElementById('log_form').innerHTML = '<button id="jslog" onclick="objLogare.adLog_form();">'+texts['myacc']+'</button>';
    }
  }
  objLogare.adLog_form = function() {
    // add the form, and focus on "email"
    document.getElementById('log_form').innerHTML = log_form;
    document.getElementById('log_form').email.focus();
    regEvents();
  }
  objLogare.datLog = function(frm) {
    // gets data from login form, if checkForm() is true
    if(checkForm(frm)) {
      // gets form data and send them to ajaxSend()
      var email = frm.email.value;
      var pass = frm.pass.value;
      // if "Remember" checkbox is checked, adds it in "datasend"
      var dat_rem = (frm.rem.checked == true) ? '&rem=rem' : '';

      // create the string with data to be send to PHP (name=value pairs) with ajaxSend()
      var  datasend = 'login=Login&email='+email+'&pass='+pass+ dat_rem;
      ajaxSend(php_fileusr, datasend, 'log_form');
    }
    return false;
  }

// gets data from register form
// if checkForm() is true, processes and transfers the data to ajaxSend()
function datReg(frm) {
  if(checkForm(frm)) {
    // gets values from form fields
	  var username = frm.username.value;
    var pass = frm.pass.value;
    var pass2 = frm.pass2.value
    var email = frm.email.value;
    var codev = frm.codev.value;

    // create the string with data to be send to PHP (name=value pairs) with ajaxSend()
    var  datasend = 'username='+username+'&pass='+pass+'&pass2='+pass2+'&email='+email+'&codev='+codev+'&susr='+texts['register'];
	  ajaxSend(php_fileusr, datasend, adboxshow);
  }
  return false;
}

// gets data from 'Recover data' link and from the form for Recover / Confirm
function datReCon(frm) {
  // check data with checkForm(), if the result is true, send data to ajaxSend()
  if(checkForm(frm)) {
    // gets data from form fields
    var email = frm.email.value;
    var codev = frm.codev.value;
    var susr = frm.susr.value;

    // create the string with data to be send to PHP (name=value pairs) with ajaxSend()
    var  datasend = 'email='+email+'&codev='+codev+'&rc='+susr+'&susr='+susr;
    ajaxSend(php_fileusr, datasend, adboxshow);
  }
  return false;
}

// delete class from #dlcls (array with IDs), add class to #adcls (array with IDs)
function delAddClass(dlcls, adcls, cls) {
  // get number of elements in each parameter
  var nr_dlcls = dlcls.length;
  var nr_adcls = adcls.length;

  // traverse each array, delete "class" of $dlcls, add class from $cls to $adcls
  for(var i=0; i<nr_dlcls; i++) {
    if(document.getElementById(dlcls[i])) document.getElementById(dlcls[i]).className = '';
  }
  for(var i=0; i<nr_adcls; i++) {
    if(document.getElementById(adcls[i])) document.getElementById(adcls[i]).className = cls;
  }
}

// to order Users in All User`s page
function orderUsr(order, usr) {
  if(order_usr == 'all') order_usr = usr;    // set value for 'order_usr', if not has been changed

  // create the string with data to be send to PHP (name=value pairs) with usrAjaxSend()
  var datasend = 'usr='+order_usr+'&order='+order;
  ajaxSend(php_fileusr, datasend, 'allusers');    // access ajaxSend() to Update the Rank in mysql
}

// called from Radio buttons in AllUsers page, when it is request to find users by name oe email
function findUsr(rbtn, usr) {
  if(order_usr == 'all') order_usr = usr;    // set value for 'order_usr', if not has been changed

  if(document.getElementById('findusr')) {
    var findusr = document.getElementById('findusr');
    var findusr_v = findusr.value;

    // if text field #findusr has less than 3 characters (only leters, numbers, space, '-', '_', '.', '@'), and more than 55, alert message
    if(findusr_v.length<3 || findusr_v.length>55 || findusr_v.search(/^([A-Za-z0-9_\. @-]+)$/) == -1) {
      findusr.focus(); findusr.select();      // select the text
      rbtn.checked = false;      // uncheck the clicked radio button
      alert(texts['err_findusr']);
    }
    else {
      // else, create the string with data to be send to PHP (name=value pairs) with usrAjaxSend()
      var datasend = 'usr='+order_usr+'&order='+rbtn.value+'&findusr='+findusr_v;
      ajaxSend(php_fileusr, datasend, 'allusers');    // access ajaxSend() to Update the Rank in mysql
    }
  }
}

// register onclick to links-A for pagination in All users page
function onclickPgLinkA() {
  if(document.getElementById('allusers')) {
    // apply "onclick" to A in Divs in "allusers", with class="linkspg"
    var linkspg = document.getElementById('allusers').getElementsByTagName('div');
    var nr_linkspg = linkspg.length;
    for(var i=0; i<nr_linkspg; i++) {
      if(linkspg[i].className == 'linkspg') {
        var spanlinks = linkspg[i].getElementsByTagName('a');
        var nr_spanlinks = spanlinks.length;
        for(var i2=0; i2<nr_spanlinks; i2++) spanlinks[i2].onclick = function() {
          datasend = this.href.match(/\?([^"]+)/i)[1]+'&susr=alusr';
          this.style.visibility = 'hidden';
          ajaxSend(php_fileusr, datasend, 'allusers'); return false;
        }
      }
    }
  }
}

// register events (users)
function regEvents() {
  // to display the image in full window
  if(document.getElementById('imgusr')) document.getElementById('imgusr').onclick = function () {adBox('<img src="'+this.src+'" />');};
  // for the link "Re-Confirm"
  if(document.getElementById('reconfirm')) document.getElementById('reconfirm').onclick = function () {ajaxSend(php_fileusr, 'rc=Confirm', adboxshow); objLogare.adLogInr(); return false;};
  // for the link "Recover data"
  if(document.getElementById('recdat')) document.getElementById('recdat').onclick = function () {ajaxSend(php_fileusr, 'rc=Recover', adboxshow); objLogare.adLogInr(); return false;};
  // for the link "Register"
  if(document.getElementById('linkreg')) document.getElementById('linkreg').onclick = function () {ajaxSend(php_fileusr, 'susr='+texts['register'], adboxshow); objLogare.adLogInr(); return false;};
  // for user`s website
  if(document.getElementById('usrwebsite')) document.getElementById('usrwebsite').onclick = function() {window.open('http://'+document.getElementById('usrwebsite').innerHTML.replace(/(http:\/\/|https:\/\/)/i, ''));}
  // for link to "#allusr"
  if(document.getElementById('allusr')) document.getElementById('allusr').onclick = function() {window.location = 'users.php?usr=0'}
  // for "Registered Users" in "#allusr"
  if(document.getElementById('allregusr')) document.getElementById('allregusr').onclick = function() {
    delAddClass(['allfbusr', 'allyhusr', 'allgousr'], ['allregusr'], 'etabvi');
    order_usr = '0';     // set value for "order_usr" variable
    orderUsr('name', '0');
    return false;
  }
  // for "Facebook Users" in "#allusr"
  if(document.getElementById('allfbusr')) document.getElementById('allfbusr').onclick = function() {
    delAddClass(['allregusr', 'allyhusr', 'allgousr'], ['allfbusr'], 'etabvi');
    order_usr = 'fb';     // set value for "order_usr" variable
    orderUsr('name', '0');
    return false;
  }
  // for "Yahoo Users" in "#allusr"
  if(document.getElementById('allyhusr')) document.getElementById('allyhusr').onclick = function() {
    delAddClass(['allregusr', 'allfbusr', 'allgousr'], ['allyhusr'], 'etabvi');
    order_usr = 'yh';     // set value for "order_usr" variable
    orderUsr('name', '0');
    return false;
  }
  // for "Google Users" in "#allusr"
  if(document.getElementById('allgousr')) document.getElementById('allgousr').onclick = function() {
    delAddClass(['allregusr', 'allfbusr', 'allyhusr'], ['allgousr'], 'etabvi');
    order_usr = 'go';     // set value for "order_usr" variable
    orderUsr('name', '0');
    return false;
  }
  if(document.getElementById('allusers')) onclickPgLinkA();       // for pagination links in All users page

  // for Register form
  if(document.getElementById('reg_form')) document.getElementById('reg_form').onsubmit = function () {return datReg(this);};
  // for login form
  if(document.getElementById('log_form')) document.getElementById('log_form').onsubmit = function () {return objLogare.datLog(this);};
  // for Recover-data form
  if(document.getElementById('recov_form')) document.getElementById('recov_form').onsubmit = function () {return datReCon(this);};

  // for login with Facebook
  if(document.getElementById('fblogin')) document.getElementById('fblogin').onclick = function () { objLogare.adLogInr(); window.open(fbloginpg, 'fblogin', 'location=0, status=0, scrollbars=0, menubar=0, directories=0, resizable=1, top=88, left=100, width=850, height=480');};

  // for login with Yahoo OpenID
  if(document.getElementById('yhlogin')) document.getElementById('yhlogin').onclick = function () { objLogare.adLogInr(); window.open(openidloginpg+'?lgw=yahoo', 'yhlogin', 'location=0, status=0, scrollbars=0, menubar=0, directories=0, resizable=1, top=88, left=100, width=850, height=480');};

  // for login with Google OpenID
  if(document.getElementById('gologin')) document.getElementById('gologin').onclick = function () { objLogare.adLogInr(); window.open(openidloginpg+'?lgw=google', 'gologin', 'location=0, status=0, scrollbars=0, menubar=0, directories=0, resizable=1, top=88, left=100, width=850, height=480');};
}

// this function is used to access the function we need after loading page
function addLoadEvent(func) {
  var oldonload = window.onload; 

  // if the parameter is a function, calls it with "onload"
  // otherwise, adds the parameter into a function, and then call it
  if (typeof window.onload != 'function') window.onload = func;
  else { 
    window.onload = function() { 
      if (oldonload) { oldonload(); } 
      func();
    } 
  } 
} 

// access the addLoadEvent() function with the functions that must be executed after loading page
addLoadEvent(regEvents);      // this register the events
addLoadEvent(objLogare.adLogInr);
addLoadEvent(tabEfect);       // call the function that will access tabEfect() after loading the page

setInterval("ajaxSend(php_fileusr, 'o',0)", 500000);            // calls ajaxSend() every 5 minutes, to upload online users