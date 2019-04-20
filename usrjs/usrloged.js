
var id_re = 'usrmod';        // the id of the tag in which the error wil be displayed
var regx_www = /^([a-zA-Z0-9_\+\.% -]+)/;            // RegExp for URL
var imgusr;

// to change Rank, receives: MySQL table, User`s id, ID of <select>, ID of tag for response
function setRank(table, idusr, idsel, reID) {
    // create the string with data to be send to PHP (name=value pairs) with usrAjaxSend()
    var datasend = 'usr='+idusr+'&rank='+document.getElementById(idsel).value+'&table='+table+'&susr=setrank';

	  ajaxSend(php_fileusr, datasend, reID);    // access ajaxSend() to Update the Rank in mysql
}

// for deleting users
function delUsers() {
  var tb_title = 'users';              // mysql table with the users
  var delidusr = new Array();            // stores the IDs of users to delete
  var ix = 0;                          // index of the elements added in "delidusr"

  // gets all checked "checkboxes", in '#setrankdel', or in '#allusers'
  var inpdel = document.getElementById('setrankdel') ? document.getElementById('setrankdel').getElementsByTagName('input') : document.getElementById('allusers').getElementsByTagName('input');
  var nr_idel = inpdel.length;

  // gets value and title (with mysql table) of all checked button, with class="delusr"
  for(var i=0; i<nr_idel; i++) {
    if(inpdel[i].className=='delusr' & inpdel[i].checked == true) {
      delidusr[ix] = inpdel[i].value;                // adds in "delidusr" the value of all checked button
      if(inpdel[i].title) tb_title = inpdel[i].title;     // redefine mysql table (needed for Facebook /Google)
      ix++;
    }
  }

  // if there are checked users added in delidusr, rquire confirm to delete, and calls ajaxSend() 
  if(delidusr.length > 0) {
    var datasend = 'delusr='+delidusr.join(',')+'&usr=admin&table='+tb_title+'&susr=delusr';
    var confdel = window.confirm(texts['confdel']);
    if(confdel) {
      ajaxSend(php_fileusr, datasend, adboxshow);
    }
  }
  else alert(texts['noselchk']);
}

// To add favorite link
function addFavLink(frm, idusr) {
  var eror = 0;    // to store errors
  // gets form fields, and check if they are completed correctly
  var favlnk = frm.favlnk.value;
  var favtitl = frm.favtitl.value;
  if(favlnk.search(regx_www)==-1) {
    alert(texts['urlformat']);
    eror = frm.favlnk;
  }
  else if(favtitl.length<3 || favtitl.length>110) {
    alert(texts['erortitl']);
    eror = frm.favtitl;
  }

  // if no error in 'eror', define data to send with ajaxSend(), else, select incorrect field,and returns false
  if (eror == 0) {
    var adfav = new Array();    // to store Link, and title, passed in json format to php
    var i = 0
    // gets all favorite links into "adfav"
    if(document.getElementById('favol')) {
      // gets all LI and add in "adfav" elements with Link and Title, in JSON string
      var favli = document.getElementById('favol').getElementsByTagName('li');
      var nrfavli = favli.length;
      for(i=0; i<nrfavli; i++) {
        adfav[i] = '["'+ favli[i].getElementsByTagName('span')[0].title+ '", "'+ favli[i].getElementsByTagName('span')[0].innerHTML+ '"]';
      }
    }
    adfav[i] = '["'+ favlnk+ '", "'+ favtitl+ '"]';       // adds as last element in "adfav" the data from form

    var datasend = 'usr='+frm.usr.value+'&idusr='+idusr+'&adfav=['+adfav+']&susr=adfav';
    ajaxSend(php_fileusr, datasend, 'favorites');
  }
  else {
    eror.focus();
    eror.select();
  }
  
  return false;
}

// To delete selected favorite link
function delFavLink(usr, idusr) {
  var chk = new Array();    // to store selected checkbox
  var ic = 0      // index for selected stored

  var adfav = new Array();    // to store Link, and title, passed in json format to php
  var i2 = 0      // index for remaining links
  // gets all favorite links into "adfav"
  if(document.getElementById('favol')) {
    // gets all LI and add in "adfav" elements, which are not selected, with Link and Title, in JSON string
    var favli = document.getElementById('favol').getElementsByTagName('li');
    var nrfavli = favli.length;
    for(var i=0; i<nrfavli; i++) {
      if(favli[i].getElementsByTagName('input')[0].checked == false) {
        adfav[i2] = '["'+ favli[i].getElementsByTagName('span')[0].title+ '", "'+ favli[i].getElementsByTagName('span')[0].innerHTML+ '"]';
        i2++;
      }
      else {
        chk[ic] = favli[i];        // store the LI with checked element, to hide after ajaxSend()
        ic++;
      }
    }
  }

  // if no checked link, alerts error, else sets and sets data with ajaxSend() to php
  if(chk.length == 0) alert(texts['noselchk']);
  else {
    var datasend = 'usr='+usr+'&idusr='+idusr+'&adfav=['+adfav+']&susr=adfav';
    ajaxSend(php_fileusr, datasend, 'favorites');
  }
}

// gets data from the form used to change user's email/password (usrModf)
// if checkForm() returns true, adds the values into a string and send it to usrAjaxSend()
function usrModf(frm) {
  if(checkForm(frm)) {
    // gets the values from each form field
    var pass = frm.pass.value;
    var passnew = frm.passnew.value
    var email = frm.email.value;
    var usr = frm.usr.value;

    // create the string with data to be send to PHP (name=value pairs) with usrAjaxSend()
    var datasend = 'usr='+usr+'&pass='+pass+'&passnew='+passnew+'&email='+email+'&susr=Modify';
	  ajaxSend(php_fileusr, datasend, id_re);
  }
  return false;
}

// gets data from the form used for optional user's data (usersDat)
// if checkNrChr() returns true, adds the values into a string and send it to usrAjaxSend()
function usersDat(frm) {
  if(checkNrChr(frm, 0, 0)) {
    // gets the value of each field
    var usr = frm.usr.value;
    var idusr = frm.idusr.value;
    var usrname = frm.usrname.value;
    var usrpronoun = frm.usrpronoun.value;
    var country = frm.usrcountry.value;
    var city = frm.usrcity.value;
    var adres = frm.usradres.value;
    var bday = frm.usrbday.value;
    var bmonth = frm.usrbmonth.value
    var byear = frm.usrbyear.value;
    var ym = frm.usrym.value
    var msn = frm.usrmsn.value;
    var site = frm.usrsite.value;
    var ocupation = frm.usrocupation.value;
    var interes = frm.usrinteres.value
    var transmit = frm.usrtransmit.value;

    HideShow(frm.id, 0, 0);        // hides the form

    // create the string with data to be send to PHP (name=value pairs) with usrAjaxSend()
    var datasend = 'usr='+usr+'&idusr='+idusr+'&usrname='+usrname+'&usrpronoun='+usrpronoun+'&usrcountry='+country+'&usrcity='+city+'&usradres='+adres+'&usrbday='+bday+'&usrbmonth='+bmonth+'&usrbyear='+byear+'&usrym='+ym+'&usrmsn='+msn+'&usrsite='+site+'&usrocupation='+ocupation+'&usrinteres='+interes+'&usrtransmit='+transmit+'&susr=Trimite';

	  ajaxSend(php_fileusr, datasend, id_re);
  }
  return false;
}

    /* Upload image */
var ar_ext = ['gif', 'jpg', 'jpe', 'png'];        // array with allowed extensions

// Check file extension, and enable Submit. Gets the field for upload (el), and ID of submit button (sbm)
function checkName(el, sbm) {
  // get the file name and split it to separe the extension
  var name = el.value;
  var ar_name = name.split('.');

  // for IE - separe dir paths (\) from name
  var ar_nm = ar_name[0].split('\\');
  for(var i=0; i<ar_nm.length; i++) var nm = ar_nm[i];

  // check the file extension
  var re = 0;
  for(var i=0; i<ar_ext.length; i++) {
    if(ar_ext[i] == ar_name[1]) {
      re = 1;
      break;
    }
  }

  // if re is 1, the extension is in the allowed list, enable submit
  if(re==1) document.getElementById(sbm).disabled = false;
  else {
    // delete the file name, disable Submit, Alert message
    el.value = '';
    document.getElementById(sbm).disabled = true;
    alert(texts['noupext'].replace(' %s ', ' '));
  }
}

// adds an iframe into the "ifrmup" element, to submit the image through this iframe
function uplImg(frm) {
  // if "frm" is a string
  if(typeof(frm)=='string') {
    // if there is "Error" into the response, adds the initial image, and Alert the error, else replace the image
    if(frm.search('Error')>=0) {
      document.getElementById('imgusr').src = imgusr;
      alert(frm);
    }
    else document.getElementById('imgusr').src = frm+'?'+Math.floor(Math.random()*11);
  }
  else {
    // display the "Loading..." image, adds the iframe to submit the image, and remove data in usrimg
    document.getElementById('imgusr').src = 'usrimgup/loading.gif';
    document.getElementById('ifrmup').innerHTML = '<iframe name="sendimg" id="sendimg" src="'+php_fileusr+'" width="400" height="150" />';
    document.getElementById('usrupimg').style.visibility = 'hidden';
    document.getElementById('forupimg').style.display = 'block';
  }
}

     /* Messages */

// checks the number of characters
function checkNrChr(text, maxlength, countchr) {
  // if maxlength, and countchr different from 0, it's a call from "onkeydown"/"onkeyup"
  // if their value is 0, it's a call from "onsubmit"
  if(maxlength!=0 && countchr!=0) {
    // check if the maximum numbers of characters is exceded
    if (text.value.length>maxlength) alert(maxlength+texts['maxoptdata']);
    // Show the number of characters left (into the tag with id passed in "countchr")
    else document.getElementById(countchr).innerHTML = texts['nrchrtxt']+'<b style="color:blue">'+(maxlength-text.value.length)+'</b>';
  }
  else if(maxlength==0 && countchr==0) {
    // gets the number of characters in: usrocupation, usrinteres si usrtransmit
    // check if it was exceeded the maximum number of characters in each
    if(text.usrocupation.value.length>500) {
      alert('500'+texts['maxoptdata']);
      text.usrocupation.focus();
      return false;
    }
    else if(text.usrinteres.value.length>500) {
      alert('500'+texts['maxoptdata']);
      text.usrinteres.focus();
    }
    else if(text.usrtransmit.value.length>1000) {
      alert('1000'+texts['maxoptdata']);
      text.usrtransmit.focus();
      return false;
    }
  }
  return true;
}

// for deleting comments
var regx_src = /src="([^"]+)/;    // RegExp to match the url in src="..."
function delMsg(cls) {
  var id_dcm = new Array();            // stores the IDs for comments to delete
  var img_dcm = new Array();           // stores the images of the coments to delete
  var ix = 0;                          // index of the elements added in "id_dcm"
  var i2x = 0;                          // index of the elements added in "img_dcm"

  // gets all checked <input> width class in "cls", in '#coments'
  var inpdel = document.getElementById('coments').getElementsByTagName('input');
  var nr_idel = inpdel.length;

  // gets value of all checked button
  for(var i=0; i<nr_idel; i++) {
    if(inpdel[i].className==cls && inpdel[i].checked==true) {
      id_dcm[ix] = inpdel[i].value;                // adds in "id_dcm" the value of all checked button

      // gets the DIV with class="upimg" in comment of the checked button
      if(document.getElementById('c'+id_dcm[ix]).getElementsByTagName('div')) {
        var div_dcm = document.getElementById('c'+id_dcm[ix]).getElementsByTagName('div');
        var nr_dv = div_dcm.length;
        for(var i2=0; i2<nr_dv; i2++) {
          // adds in "img_dcm" the SRC address in DIV with class="upimg"
          if(div_dcm[i2].className=='upimg') {
            img_dcm[i2x] = div_dcm[i2].innerHTML.match(regx_src)[1];
            i2x++;
          }
        }
      }
      ix++;
    }
  }

  // if there are checked comments added ib id_dcm
  // adds the IDs and Img-src of deleted comments to be send with ajaxSend() to php
  if(id_dcm.length > 0) {
    var datasend = 'id_dcm='+id_dcm.join(',')+'&img_dcm='+img_dcm.join(',')+'&sbmt=delcmm';
    var confdel = window.confirm(texts['confdel']);
    if(confdel) {
      ajaxSend(file_phpcm, datasend, 'coments');

      // actualise in Comments title the comments number
      document.getElementById('coments_t').getElementsByTagName('i')[0].innerHTML = '('+(document.getElementById('coments_t').getElementsByTagName('i')[0].innerHTML.match(/[0-9]+/i)-ix)+')';
    }
  }
  else alert(texts['noselchk']);
}

// register events
function regEventsUsr() {
  // for the button that shows the form for upload image
  if(document.getElementById('forupimg')) {
    document.getElementById('forupimg').onclick = function () {
      // hides the button and makes the Upload form visible, empty "file" field
      this.style.display = 'none';
      document.getElementById('usrupimg').style.visibility = 'visible';
      document.getElementById('usrimg').value = '';
      document.getElementById('usrupimg').style.display = 'block';
    };
  }

  // for the Upload form
  if(document.getElementById('usrupimg')) document.getElementById('usrupimg').onsubmit = function () {return uplImg(this);};
  // for the form used to add user's optional data
  if(document.getElementById('usrform2')) document.getElementById('usrform2').onsubmit = function () {return usersDat(this);};
}

// instruction that must be performed first after page load
function afterLoad() {
  if(document.getElementById('imgusr')) imgusr = document.getElementById('imgusr').src;       // gets the initial image in user page

  // gets all checked <input> width class in "delsel", in '#center'
  // make sure all checkboxes for delete comments are unchecked on reload
  var inpdel = document.getElementById('center').getElementsByTagName('input');
  var nr_idel = inpdel.length;
  for(var i=0; i<nr_idel; i++) {
    if(inpdel[i].className=='delsel') inpdel[i].checked = false;
  }
}

addLoadEvent(afterLoad);
addLoadEvent(regEventsUsr);      // to execute the function that registers events