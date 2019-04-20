<?php
// Class with useful functions needed in script
class Functions {
  // cleans data from POST and GET
  function cleanGP() {
    // removes, tags, and external whitespace from GET and POST
    if(isset($_GET)) {
      $_GET = array_map("strip_tags", $_GET);
      $_GET = array_map("trim", $_GET);
    }
    if(isset($_POST)) {
      $_POST = array_map("strip_tags", $_POST);             // delete tags
      $_POST = array_map("addslashes", $_POST);             // adds slashes to quotes
      $_POST = array_map("trim", $_POST);
    }
  }

  // Function to convert BBCODE in HTML tags
  function formatBbcode($str) {
    // characters that represents bbcode, and smiles
    $bbcode = array('/\[b\](.*?)\[\/b\]/is', '/\[i\](.*?)\[\/i\]/is', '/\[u\](.*?)\[\/u\]/is', '/\[block\](.*?)\[\/block\]/is', '/:\)/i', '/:\(/i', '/:P/i', '/:D/i', '/:S/i', '/:O/i', '/:=\)/i', '/:\|H/i', '/:X/i', '/:\-\*/i');

    // HTML code that replace bbcode, and smiles characters
    $htmlcode = array('<b>$1</b>', '<i>$1</i>', '<u>$1</u>', '<blockquote>$1</blockquote>',
    '<img src="icos/0.gif" alt=":)" border="0" />',
    '<img src="icos/1.gif" alt=":(" border="0" />',
    '<img src="icos/2.gif" alt=":P" border="0" />',
    '<img src="icos/3.gif" alt=":D" border="0" />',
    '<img src="icos/4.gif" alt=":S" border="0" />',
    '<img src="icos/5.gif" alt=":O" border="0" />',
    '<img src="icos/6.gif" alt=":=)" border="0" />',
    '<img src="icos/7.gif" alt=":|H" border="0" />',
    '<img src="icos/8.gif" alt=":X" border="0" />',
    '<img src="icos/9.gif" alt=":-*" border="0" />'
    );

    $str = preg_replace($bbcode, $htmlcode, $str);   // perform replaceament

    return $str;
  }

  // Function to convert HTML tags in BBCODE
  function formatHtml($str) {
    // characters that represents html tags
    $htmlcode = array('/\<b\>(.*?)\<\/b\>/is', '/\<i\>(.*?)\<\/i\>/is', '/\<u\>(.*?)\<\/u\>/is', '/\<blockquote\>(.*?)\<\/blockquote\>/is');

    // BBCODE that replace HTML tags
    $bbcode = array('[b]$1[/b]', '[i]$1[/i]', '[u]$1[/u]', '[block]$1[/block]');

    $str = preg_replace($htmlcode, $bbcode, $str);   // perform replaceament

    return $str;
  }

  // sets the <select> with user`s rank. Receives: MySQL table, Users rank, User`s id, ID of <select>, ID of tag for response
  function setRank($table, $rank, $idusr, $idsel, $reid) {
    $sel = "<select id=\"".$idsel."\" name=\"".$idsel."\" onchange=\"setRank('".$table."', ".$idusr.", this.id, '".$reid."');\">";
    for($i=-1; $i<=9; $i++) {
      if($i != $rank) $sel .= '<option value="'.$i.'">'.$i.'</option>';
      else $sel .= '<option value="'.$i.'" selected>'.$i.'</option>';
    }
    $sel .= '</select>';
    return $sel;
  }
}
// sets an object instance for this class
$functions = new Functions();