<?php
// Start Base class
class Base {
  static protected $conn = false;            // stores the connection to mysql
  protected $conn_data = array();            // to store data for connecting to database
  public $affected_rows = 0;        // number of affected, or returned rows in SQL query
  public $last_insertid;            // stores the last ID in an AUTO_INCREMENT column, after Insert query
  public $lsite;                    // store the texts for site according to language set
  public $site;                    // website name
  public $protocol;                // website protocol ("http://" or "https")
  public $ip;                    // the user IP
  public $eror = false;          // to store and check for errors

  public $rowsperpage = 15;             // number of paginated rows
  public $range = 3;           // range number of links around the current
  public $currentpage = 1;  // the number pagination of the current page
  protected $startrow;           // the row from which start to select the content
  protected $totalpages = 0;     // number of total pages
  public $linkspgs = '';      // will contain the pagination links
  public $totalrows = 0;       // to store total number of rows for current page

  // constructor (receives data for connecting to mysql)
  public function __construct($conn_data) {
    $this->lsite = $GLOBALS['lsite'];     // store in property the text for language

    // if the parameter is an array, sets properties IP, Site, Protocol
    if(is_array($conn_data)) {
      $this->conn_data = $conn_data;        // stores data for connection
      $this->ip = isset($_COOKIE['ip']) ? $_COOKIE['ip'] : $_SERVER['REMOTE_ADDR'];

      // if $_COOKIE['ip'] not exists, create it
      if(!isset($_COOKIE['ip']) && !headers_sent()) setcookie('ip', $this->ip, time()+60*60*24*100, '/');

      $this->site = $_SERVER['SERVER_NAME'] .($_SERVER['SERVER_PORT'] !=80 ? ':'. $_SERVER['SERVER_PORT'] : '');
      $this->protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') ? 'https://' : 'http://';
    }
    else $this->setEror($this->lsite['eror_base']['construct']);
  }

  // for connecting to mysql
  protected function setConn($conn_data) {
    try {
      // Connect and create the PDO object
      self::$conn = new PDO("mysql:host=".$conn_data['host']."; dbname=".$conn_data['bdname'], $conn_data['user'], $conn_data['pass']);

      // Sets to handle the errors in the ERRMODE_EXCEPTION mode
      self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      // Sets transfer with encoding UTF-8
      self::$conn->exec('SET character_set_client="utf8",character_set_connection="utf8",character_set_results="utf8"; ');
    }
    catch(PDOException $e) {
      $this->setEror($this->lsite['eror_base']['setconn']. $e->getMessage());
    }
  }

  // Performs SQL queries
  public function sqlExecute($sql) {
    if(self::$conn===false OR self::$conn===NULL) $this->setConn($this->conn_data);      // sets the connection to mysql
    $re = true;           // the value to be returned

    // if there is a connection set ($conn property not false)
    if(self::$conn !== false) {
      // gets the first word in $sql, to determine whenb SELECT query
      $ar_mode = explode(' ', trim($sql), 2);
      $mode = strtolower($ar_mode[0]);

      // performs the query and get returned data
      try {
        if($sqlprep = self::$conn->prepare($sql)) {
          // execute query
          if($sqlprep->execute()) {
            // if $mode is 'select', gets the result_set to return
            if($mode == 'select') {
              $re = array();
              // if fetch() returns at least one row (not false), adds the rows in $re for return
              if(($row = $sqlprep->fetch(PDO::FETCH_ASSOC)) !== false){
                do {
                  // check each column if it has numeric value, to cenvert it from "string"
                  foreach($row AS $k=>$v) {
                    if(is_numeric($v)) $row[$k] = $v + 0;
                  }
                  $re[] = $row;
                }
                while($row = $sqlprep->fetch(PDO::FETCH_ASSOC));
              }
              $this->affected_rows = count($re);                   // number of returned rows
            }
            else $this->affected_rows = $sqlprep->rowCount();      // affected rows for Insert, Update, Delete

            // if Insert query, stores the last insert ID
            if($mode == 'insert') $this->last_insertid = self::$conn->lastInsertId();
          }
          else $this->setEror($this->lsite['eror_base']['sqlexecute']);
        }
        else {
          $eror = self::$conn->errorInfo();
          $this->setEror('Error: '. $eror[2]);
        }
      }
      catch(PDOException $e) {
        $this->setEror($e->getMessage());
      }
    }

    // sets to return false in case of error
    if($this->eror !== false) $re = false;
    return $re;
  }

  // this method Upload files, save in database its name and path, and return it
  protected function uploadFile($filedata, $frule, $fileup, $sql=false) {
    $err = '';        // will store the errors
    $reout = '';      // data returned by this method

    // gets file extension
    $splitimg = explode('.', strtolower($filedata['name']));
    $ext = end($splitimg);

    list($width, $height) = getimagesize($filedata['tmp_name']);     // gets image width and height

    // checks the file to match allowed rules
    if(!in_array($ext, $frule['allowext'])) $err .= sprintf($this->lsite['eror_base']['upext'], $filedata['name']);
    if(isset($frule['maxsize']) AND $filedata['size']>=($frule['maxsize']*1000)) $err .= sprintf($this->lsite['eror_base']['upmaxsize'], $filedata['name'], $frule['maxsize']);
    if((isset($frule['width']) AND isset($frule['height'])) AND ($width>=$frule['width'] OR $height>=$frule['height'])) $err .= sprintf($this->lsite['eror_base']['upimgwh'], $frule['width'], $frule['height']);

    // if no error, performs Upload, otherwise sets $eror and returns false
    if($err == '') {
      if(move_uploaded_file($filedata['tmp_name'], $fileup)) {
        $reout .= $fileup;

        // if $sql to add the file name in database, performs the query
        if($sql AND !$this->sqlExecute($sql)) $reout .= $this->lsite['eror_base']['upfiledb']. $this->eror;
      }
      else $reout .= sprintf($this->lsite['eror_base']['upfile'], $filedata['name']);
    }
    else {
      $this->eror = $err;
      $reout = false;
    }

    return $reout;
  }

  // the method  to send e-mail (with html code, and utf-8 encoding)
  protected function sendMail($to, $from, $from_name, $sub, $msgs){
    $eol = "\r\n";             // Used for new line
    $re = true;                // variable to return
    if(!is_array($to)) $to = array($to);     // makes sure $to is array
    if(!is_array($msgs)) $msgs = array($msgs);     // makes sure $msg is array
    $nrto = count($to);

    // if USEGMAIL is 0, set $headers to send mail with mail() function, else, use gmailSender()
    if(USEGMAIL == 0) {
      // Sets headers for email, end subject ($sub) with base for utf-8
      $headers = "From: $from_name <". $from . ">".$eol;
      $headers .= "MIME-Version: 1.0". $eol;
      $headers .= "Content-type: text/html; charset=utf-8". $eol;
      $headers .="Content-Transfer-Encoding: 8bit";
      $sub = "=?utf-8?B?".base64_encode($sub)."?=";

      // traverse $to and send email to each e-mail address in $to
      for($i=0; $i<$nrto; $i++) {
        // pause 1 sec on each 11 e-mail, maximum 50 mails
        if(($i%11) === 0) sleep(1);
        else if($i > 50) break;

        $msg = isset($msgs[$i]) ? $msgs[$i] : $msgs[0];    // gets current mesage in $msgs, or firs if no $msg[$i]

        // if the mail cant be sent, sets $re to false, and stop for()
        if(!mail($to[$i], $sub, $msg, $headers)) { $re = false; break; }
      }
    }
    else $re = $this->gmailSender($to, $from, $from_name, $sub, $msgs);

    return $re;
  }

  // uses PHPMailer class to send email via SMTP with GMail account
  protected function gmailSender($to, $from, $from_name, $sub, $msgs) {
    include('phpmailer/class.phpmailer.php');
    $re = true;                  // variable to return;
    $nrto = count($to);         // $to is array, gets number of mails to send

    // traverse $to and create instance to PHPMailer() to send email to each e-mail address in $to
    for($i=0; $i<$nrto; $i++) {
      $mail             = new PHPMailer();
      $mail->IsSMTP();                           // telling the class to use SMTP
      $mail->Host       = "smtp.gmail.com";      // SMTP server
      $mail->SMTPAuth   = true;                  // enable SMTP authentication
      $mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
      $mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
      $mail->Port       = 465;                   // set the SMTP port for the GMAIL server
      $mail->Username   = GMAILUSER;             // GMAIL username
      $mail->Password   = GMAILPASS;             // GMAIL password

      $mail->SetFrom($from, $from_name);
      $mail->AddReplyTo($from, $from_name);
      $mail->Subject = $sub;

      // pause 1 sec on each 11 e-mail, maximum 50 mails
      if(($i%11) === 0) sleep(1);
      else if($i > 50) break;

      $msg = isset($msgs[$i]) ? $msgs[$i] : $msgs[0];    // gets current mesage in $msgs, or firs if no $msg[$i]
      $msg = preg_replace("#\\\#",'',$msg);
      $mail->MsgHTML($msg);                 // to send with HTML tags
      $mail->AddAddress($to[$i], 'User');

      if(!$mail->Send()) { $re = false; break; }
    }
    return $re;
  }

  // sets and returns a verification code (captcha)
  public function setCaptcha($ses) {
    $datestr = date("j-F-Y, g:i");              // string with current date-time
    $datestr = md5($datestr);                   // encode the $datestr

    // if seesion exists, delete it and sets session with a code from $datestr
    if(isset($_SESSION[$ses])) { unset($_SESSION[$ses]); }
    $_SESSION[$ses] = substr($datestr, 3, 5);

    return $_SESSION[$ses];        // returns the session with captcha
  }

       /* Methods for pagination */

  // Select the $selcol in $table with condition $where, by $order. Returns an Array with the rows
  public function getMysqlRows($selcol, $table, $where, $order) {
    $reout = false;              // the variable that will be returned

    // SELECT to set the total number of pages ($totalpages)
    $sql = "SELECT COUNT(*) AS totalrows FROM `$table` $where";

    // perform the query, then Selects the rows
    $resql = $this->sqlExecute($sql);
    // if the $resql contains at least one row, takes and sets $totalpages
    if($this->affected_rows > 0) {
      // sets totalrows, totalpages, currentpage, and the $startrow
      $this->totalrows = $resql[0]['totalrows'];
      $this->totalpages = ceil($this->totalrows / $this->rowsperpage);
      $this->currentpage = isset($_REQUEST['nrp']) ? intval($_REQUEST['nrp']) : $this->totalpages;
      if($this->currentpage > $this->totalpages) $this->currentpage = $this->totalpages;
      $this->startrow = ($this->currentpage - 1) * $this->rowsperpage;

      // Define the SELECT to get the rows for the current page
      $sql = "SELECT $selcol FROM `$table` $where $order LIMIT $this->startrow, $this->rowsperpage";
      $reout = $this->sqlExecute($sql);
    }

    return $reout;
  }

  // method that sets the links ($geturl - the file and GET part for URL in "href")
  protected function setLinkspgs($geturl='') {
    $re_links = '';         // the variable that will contein the links to be added in $linkspgs
    // sets tag for the paginated links
    $pglink = ($geturl == '') ? ' <span>%s</span> ' : '<a href="'.$geturl.'%s">%s</a>';

    // if $totalpages>0 and totalpages higher or equal to $currentpage
    if($this->totalpages>0 && $this->totalpages >= $this->currentpage) {
      // links to first and previous page, if it isn't the first links-range
      if ($this->currentpage > $this->range) {
        // show << for link to 1st page
        $re_links .= sprintf($pglink, 1, $this->lsite['first'].' &lt;&lt;');
       
        $prevpage = $this->currentpage - 1;          // the number of the previous page
        // show < for link to previous page, if higher then 1
        if($prevpage>1) $re_links .= sprintf($pglink, $prevpage, $this->lsite['prev'].' &lt;');
      }

      // sets the links in the range of the current page
      for($x = ($this->currentpage - $this->range); $x <= ($this->currentpage + $this->range); $x++) {  
        // if it's a number between 0 and last page
        if (($x > 0) && ($x <= $this->totalpages)) {
          // if it's the number of current page, show the number without link, otherwise add link
          if ($x == $this->currentpage) $re_links .= ' [<span class="sb">'. $x. '</span>] ';
          else $re_links .= sprintf($pglink, $x, $x);
        }
      }

      // If the current page is not final, adds link to next and last page
      if ($this->currentpage != $this->totalpages) {
        $nextpage = $this->currentpage + 1;
        // show > for next page (if higher then $this->range and less then totalpages)
        if($nextpage>$this->range && $nextpage<$this->totalpages) $re_links .= sprintf($pglink, $nextpage, '&gt; '.$this->lsite['next']);
        //  show >> for last page, if higher than $this->range
        if($this->totalpages > $this->range) $re_links .= sprintf($pglink, $this->totalpages, '&gt;&gt; '.$this->lsite['last']." ( $this->totalpages )");
      }
    }

    // adds all links into a DIV and store them in $linkspgs property
    if(strlen($re_links)>1) $re_links = '<div class="linkspg">'. $re_links. '</div>';
    $this->linkspgs = $re_links;
  }

  // sets the $eror property
  public function setEror($eror) {
    $this->eror = '<div class="eror">'. $eror. '</div>';
    return $this->eror;
  }
}