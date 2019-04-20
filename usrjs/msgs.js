/* For Add Message */
var usr = location.search.match(/\?usr=([^&]+)/)[0];      // the GET with user name
var file_phpcm = 'usrincls/msgs.php';              // php file to which send data with ajaxSend
var getmsgs = new Array();                // array to store the accesed paginated comments

// adds in "getmsgs" the comments in current pagination (nr. in [<b>nr</b>]) (called in regEventsCmm())
function setGetMsgs() {
 var curentpg = $('.linkspg:first b').text();
 getmsgs[curentpg] = $('#coments').html();
}

// define, and sends data to ajaxSend
function toAjax(idp) {
  var reID = 'coments';
  var from_getmsg = 0;         // to indicate messages get from "getmsgs"

  // if the paginated coment already visited (stored in "getmsgs") add the comments from "getmsgs"
  if(getmsgs[idp]) {
    $('#'+reID).fadeOut(300, function() {
      $('#'+reID).animate({height: 'toggle'}, 800, 'swing');
      $('#'+reID).html(getmsgs[idp]);
      showImgBox();
      onclickPgLinkS();
    });
    from_getmsg = 1;
  }

  if(from_getmsg == 0) ajaxSend(file_phpcm+usr, '&nrp='+idp, reID);      // if no from "getmsgs", calls ajaxSend
}

// Add white-space for IE browser
 if(navigator.appName == "Microsoft Internet Explorer") {
   document.write("<style> .c_coms { white-space:pre; } </style>");
}

// check the number of characters allowed in textarea
function checkTxta(text) {
  var maxlength = 600;

  // If exceds allowed number, show alert, else show remaining allowed characters
  if (text.value.length>maxlength) {
    alert(texts['maxchrtxt']);
    return false
  }
  else {
    document.getElementById("countdown").innerHTML = texts['nrchrtxt']+'<b>'+(maxlength-text.value.length)+'</b>';
    return true
  }
}

/** Start - functions to Format text, and Smiles in textarea **/

// determine the coords of select in textarea (zon), and the selected text, for IE
function cursorPosition(star, en, zon){
var textarea = document.getElementById(zon);
textarea.focus();

var selection_range = document.selection.createRange().duplicate();

if (selection_range.parentElement() == textarea) { // Check that the selection is actually in our textarea
// Create three ranges, one containing all the text before the selection,
// one containing all the text in the selection (this already exists), and one containing all
// the text after the selection.
var before_range = document.body.createTextRange();
before_range.moveToElementText(textarea); // Selects all the text
before_range.setEndPoint("EndToStart", selection_range); // Moves the end where we need it

var after_range = document.body.createTextRange();
after_range.moveToElementText(textarea); // Selects all the text
after_range.setEndPoint("StartToEnd", selection_range); // Moves the start where we need it

var before_finished = false, selection_finished = false, after_finished = false;
var before_text, untrimmed_before_text, selection_text, untrimmed_selection_text, after_text, untrimmed_after_text;

// Load the text values we need to compare
before_text = untrimmed_before_text = before_range.text;
selection_text = untrimmed_selection_text = selection_range.text;
after_text = untrimmed_after_text = after_range.text;

// Check each range for trimmed newlines by shrinking the range by 1 character and seeing
// if the text property has changed. If it has not changed then we know that IE has trimmed
// a \r\n from the end.
do {
if (!before_finished) {
if (before_range.compareEndPoints("StartToEnd", before_range) == 0) {
before_finished = true;
} else {
before_range.moveEnd("character", -1)
if (before_range.text == before_text) {
untrimmed_before_text += "\n";
} else {
before_finished = true;
}
}
}
if (!selection_finished) {
if (selection_range.compareEndPoints("StartToEnd", selection_range) == 0) {
selection_finished = true;
} else {
selection_range.moveEnd("character", -1)
if (selection_range.text == selection_text) {
untrimmed_selection_text += "\r\n";
} else {
selection_finished = true;
}
}
}
if (!after_finished) {
if (after_range.compareEndPoints("StartToEnd", after_range) == 0) {
after_finished = true;
} else {
after_range.moveEnd("character", -1)
if (after_range.text == after_text) {
untrimmed_after_text += "\r\n";
} else {
after_finished = true;
}
}
}

} while ((!before_finished || !selection_finished || !after_finished));

// ** END Untrimmed success test

// Define into an array the start, and end of selected text, and the final text
var re = new Array();
re['startPos'] = untrimmed_before_text.length;
re['endPos'] = re['startPos'] + untrimmed_selection_text.length;
re['final_text'] = untrimmed_before_text +star+ untrimmed_selection_text +en+ untrimmed_after_text;

  return re;
}
}

// position the cursor in the element with ID of "zona" to Xpos coord
function set_xpos(zona, Xpos) {
  var txtarea = document.getElementById(zona);
  if(txtarea != null) {
  if(txtarea.createTextRange) {
    var range = txtarea.createTextRange();
    range.move('character', Xpos);
    range.select();
  }
  else {
    if(txtarea.selectionStart) {
    txtarea.focus();
      txtarea.setSelectionRange(Xpos, Xpos);
    }
    else {
      txtarea.focus();
    }
    }
  }
}

// Add tags: B, I, U in form (for IE uses cursorPosition(), and set_xpos)
function addTag(start, end, idadd) {
  var txtarea = document.getElementById(idadd);
  if (txtarea.selectionStart || txtarea.selectionStart==0) { // Mozilla, Opera
    // Define into an array the start, and end of selected text, and the final text in textarea
    var rezult = new Array();
    rezult['startPos'] = txtarea.selectionStart;
    rezult['endPos'] = txtarea.selectionEnd;
    rezult['final_text'] = txtarea.value.substring(0, rezult['startPos']) + start + txtarea.value.substring(rezult['startPos'], rezult['endPos']) + end + txtarea.value.substring(rezult['endPos'], txtarea.value.length);
  }
  else if (document.selection) {   // IE
    var rezult = cursorPosition(start, end,idadd);
 }

  // Add the new text in textarea, calls set_xpos() to position cursor to Xpos
  txtarea.value = rezult['final_text'];
  var Xpos = rezult['endPos']+start.length;
  set_xpos(idadd, Xpos);
}

// Add code for clicked smile in element with ID passed in "idadd"
function addSmile(smile, idadd) {
  var tarea_com = document.getElementById(idadd);
  tarea_com.value += smile;
  tarea_com.focus();
}

/* End - functions to add URL, Format text, and Smiles in textarea */

// Submit form (frm) via <iframe> created by adBox(), after checks form data
function sendForm(frm) {
  if(checkForm(frm)) {
    document.getElementById('coments').innerHTML = '<iframe src="'+file_phpcm+'" name="adbox_ifr" id="adbox_ifr" scrolling="no" />';

    // show 'Loading...' in <iframe> (till that page is loading), and submit form
    if(document.getElementById('adbox_ifr').contentWindow) {
      document.getElementById('adbox_ifr').contentWindow.document.write('<h1 style="text-align:center;">Loading...</h1>');
    }
    frm.submit();

    HideShow('formc', 'show_formc', 'show_formc');      // hides the form, show "Add ..."

    // actualise in Comments title the comments number
    document.getElementById('coments_t').getElementsByTagName('i')[0].innerHTML = '('+((document.getElementById('coments_t').getElementsByTagName('i')[0].innerHTML.match(/[0-9]+/i)*1)+1)+')';
  }
  return false;
}

// called from <iframe> after add comment, reset values in form that add comment, alert "msg", hide Form to add comments, calls ajaxSend() to reactualise comments
function resetMsg(msg) {
  if(document.getElementById('codev')) document.getElementById('codev').value = '';
  if(document.getElementById('formc').upimg) document.getElementById('formc').upimg.value = '';
  alert(msg);
  HideShow('formc', 'show_formc', 0);
  ajaxSend(file_phpcm+usr, 'nrp=9999', 'coments');
}

// to show image in full screen, with adBox(), and to add link to name
function showImgBox() {
  if(document.getElementById('coments') && document.getElementById('coments').getElementsByTagName('div')) {
    // gets the IMG in Divs with class="upimg"
    var dvtg = document.getElementById('coments').getElementsByTagName('div');
    var nr_dvtg = dvtg.length;
    for(var i=0; i<nr_dvtg; i++) {
      if(dvtg[i].className == 'upimg') {
        var imgad = dvtg[i].getElementsByTagName('img')[0];
        imgad.onclick = function() {adBox('<img src="'+this.src+'" />')};
      }

      // add link to name
      if(dvtg[i].className == 'n_coms') {
        dvtg[i].onclick = function(){
          var usermsg = this.innerHTML.match(/[a-z0-9_-]+/i);       // get username who added the message
          // set to open user page (Facebook if '.n_coms' tag has "title": with Facebook ID, or 'social' string)
          if(this.title) {
            // if "title" not 'social', sets $usrmsgpage for Facebook user page
            if(this.title != 'social') var usrmsgpage = 'http://www.facebook.com/profile.php?id='+ this.title;
          }
          else {
            // sets user page on website
            var usrmsgpage = window.location.toString().replace(/usr=[a-z0-9_-]+/i, 'usr='+usermsg);
          }

          // if defined $usrmsgpage, open it, else, mesage 'loged with Google OpenID'
          if(usrmsgpage) window.location = usrmsgpage;
          else alert('Loged with Yahoo, or Google OpenID');
        };
      }
    }
  }
}

// register onclick to links-Span for pagination
function onclickPgLinkS() {
  // for pagination links in messages
  if(document.getElementById('coments')) {
    // apply "onclick" to all Span in Divs in "coments", with class="linkspg"
    var linkspg = document.getElementById('coments').getElementsByTagName('div');
    var nr_linkspg = linkspg.length;
    for(var i=0; i<nr_linkspg; i++) {
      if(linkspg[i].className == 'linkspg') {
        var spanlinks = linkspg[i].getElementsByTagName('span');
        var nr_spanlinks = spanlinks.length;
        for(var i2=0; i2<nr_spanlinks; i2++) spanlinks[i2].onclick = function() {this.style.visibility = 'hidden'; toAjax(this.innerHTML.match(/[0-9]+/i));}
      }
    }
  }
}

// events that must be registered on page load, or after Ajax request
function regEventsMsg() {
  // gets all <img> in "formc" with class: "addsmile", "addtag", and register onclick
  if(document.getElementById('formc')) {
    var formcimg = document.getElementById('formc').getElementsByTagName('img');
    var nr_t = formcimg.length;
    for (var i=0; i<nr_t; i++) {
      if(formcimg[i].className == 'addsmile') formcimg[i].onclick = function() {addSmile(this.title, 'coment');};
      else if(formcimg[i].className == 'addtag') formcimg[i].onclick = function() {addTag('['+this.title+']','[/'+this.title+']', 'coment');};
    }
  }

  // for favorite links
  if(document.getElementById('favol')) {
    // gets all LI and set "onclick" to <span> in each LI
    var favli = document.getElementById('favol').getElementsByTagName('li');
    var nrfavli = favli.length;
    for(var i=0; i<nrfavli; i++) {
      favli[i].getElementsByTagName('span')[0].onclick = function() {window.open('http://' + this.title, 'fav');}
    }
  }
  onclickPgLinkS();       // for pagination links in messages
  showImgBox();       // to show image in full screen, with adBox()

  // to show / hide form for adding comments
  if(document.getElementById('show_formc')) document.getElementById('show_formc').onclick = function() {HideShow(this.id, 'formc', 'addcomm');};
  if(document.getElementById('formc_cls')) document.getElementById('formc_cls').onclick = function() {HideShow('formc', 'show_formc', 0);};

  // keyup /keydown on textarea in form to add comments
  if(document.getElementById('coment')) {
    document.getElementById('coment').onkeyup = function() {checkTxta(this);};
    document.getElementById('coment').onkeydown = function() {checkTxta(this);};
  }

  // for Submit form that adds comments
  if(document.getElementById('formc')) document.getElementById('formc').onsubmit = function() {return sendForm(this);};
  
  setGetMsgs();       // add current comments in "getmsgs" array
}

addLoadEvent(regEventsMsg);      // this register the events