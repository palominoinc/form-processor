<?php

// this was me, Jerry

/*
 *
 */

use \BaseController;
use \Redirect;

namespace FormProcessor\Controllers;

class FormProcessor extends \BaseController
{
  public function sendEmail() {
    // First, make sure the form was posted from a browser. 

    if(!isset($_SERVER['HTTP_USER_AGENT'])){ 
      die("Forbidden - You are not authorized to view this page"); 
      exit; 
    } 

    // Make sure the form was indeed POST'ed: 

    if(!$_SERVER['REQUEST_METHOD'] == "POST"){ 
      die("Forbidden - You are not authorized to view this page"); 
      exit;     
    } 

    // Host names from where the form is authorized 

    $authHosts = array("webpal.net"); 

    // Where have we been posted from? 
    $fromArray = parse_url(strtolower($_SERVER['HTTP_REFERER']));

    // Test to see if the $fromArray used www to get here. 
    // $wwwUsed = strpos($fromArray['host'], "www."); 

    // Make sure the form was posted from an approved host name. 
//     if(!in_array(($wwwUsed === false ? $fromArray['host'] : substr(stristr($fromArray['host'], '.'), 1)), $authHosts)){     
//       $this->logBadRequest(); 
//       header("HTTP/1.0 403 Forbidden"); 
//       exit;     
//     } 

    // Attempt to defend against header injections and spam: 

    $badStrings = array("Content-Type:", 
                        "MIME-Version:", 
                        "Content-Transfer-Encoding:", 
                        "bcc:", 
                        "cc:",
                        "/r",
                        "/n",
                        "%0a",
                        "url=http",
                        "url=",
                        "href",
                        "<?",
                        "<a",
                        "mailto",
                        "insomnia",
                        ".txt",
                        ".pdf",
                        "skynata",
                        "warcraft",
                        "xanax",
                        "viagara",
                        "cialis",
                        "vitorin",
                        "dubai",										 
                        "%0d"); 

    // Loop through each POST'ed value and test if it contains 
    // one of the $badStrings: 
    foreach($_POST as $k => $v){ 
      foreach($badStrings as $v2){ 
        if(strpos($v, $v2) !== false){ 
          $this->logBadRequest(); 
          header("HTTP/1.0 403 Forbidden"); 
          exit; 
        } 
      } 
    }     

    // Made it past spammer test, free up some memory 
    // and continue rest of script:     
    unset($k, $v, $v2, $badStrings, $authHosts, $fromArray, $wwwUsed); 


    // ---- Begin Form Processing -----  //

    // Define CSS Styles

    $CSSclasses = array (
      'cssTable'   => "border:1px solid #CCCCCC; font-family: Arial, Helvetica, sans-serif; width: 400px;",
      'cssHeader'  => "font-size: 16px; 	font-weight: bold; color: #333333; background:#CCCCCC;text-align: center;",
      'cssTDleft'  => "font-size: 14px; color: #333333; font-weight: bold; background:#EFEFEF;text-align: left;width: 200px;",
      'cssTDright' => "font-size: 14px; color: #333333; background:#EFEFEF;text-align: left;width: 200px;"
    );

    // ---- Send Mail Process -----  //

    $to = 'info-nowhere@webpal.net';

    $subject = "{$_POST['subject']}";
    $message = nl2br($this->processPost($_POST, $CSSclasses, 'Submit'));
    $from = 'no-reply@webpal.net' ;

    mail($to, $subject,''.$message.'',
         "From: ".$from."\n" .
         "MIME-Version: 1.0\n" .
         "Content-type: text/html; charset=iso-8859-1");
    
    return \Redirect::to('/thank-you');
  }



  private function logBadRequest()
  {
    die("Your input did not meet the criteria for this form, please try again.");
  }


  /***/

  private function addTD($val, $style){
    $output = "<td {$style}>{$val}</td>";
    return $output;
  }

  private function processKeyValue($keys3,$vals3, $CSSclasses=NULL)
  {
    $output = "<tr>";
    $keyStyle = 'style="'.$CSSclasses['cssTDleft'].'"';
    $output .= $this->addTD($keys3, $keyStyle);
    $valStyle = 'style="'.$CSSclasses['cssTDright'].'"';
    $output .= $this->addTD($vals3, $valStyle);
    $output .= "</tr>";
    return $output;
  }

  private function processPost($stuff, $CSSclasses=NULL)
  {
    $output = "<table style='".$CSSclasses['cssTable']."' border='1'><tr><td colspan='2' style='".$CSSclasses['cssHeader']."'>{$_POST['subject']}</td></tr>";
    foreach($stuff as $key2=>$val2)
    {
      if($key2 != 'subject'){
        $output .= $this->processKeyValue($key2,$val2, $CSSclasses);
      }
    }
    $output .= "</table>";
    return $output;
  }
}
