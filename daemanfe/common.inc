<?php
require("ui.inc");
require("config");
session_start();

$loggedin = $_SESSION['loggedin'];
$currentuserid = $_SESSION['currentuserid'];
$action = $_REQUEST['action'];

$DBUser = $DBUsername;
$DBPass = $DBPassword;
require("mysql.inc");

/****************/
/* User details */
/****************/

function checkstatus(){
  global $loggedin, $currentuserid, $details;

  // Redirect back to the index page if they aren't logged in
  if (!$loggedin){ header("location: /errors/notloggedin.php?url=" . rawurlencode($_SERVER['REQUEST_URI'])); exit(); }

  // If there isn't a specific account ID, use the currently logged in ID
  if (!isset($_REQUEST['userid'])) { $_REQUEST['userid'] = $currentuserid; }

  // If the manually specified account does not belong to this user, redirect back to the index
  if (!(($_REQUEST['userid'] == $currentuserid) || userisadmin($currentuserid))) { header("location: errors/invaliddetails.php"); exit(); }

  // If there are no account details for this account, redirect to the index
  $details = userdetails($_REQUEST['userid']);

  if (!$details) { header("location: /errors/invaliddetails.php"); exit(); }
}

function checkadminstatus(){
  global $loggedin, $currentuserid;

  // Redirect back to the index page if they aren't logged in
  if (!$loggedin){ header("location: /errors/notloggedin.php?url=" . rawurlencode($_SERVER['REQUEST_URI'])); exit(); }

  // If the logged in user isn't an admin, redirect back to the index
  if (!(userisadmin($currentuserid))){ header("location: /errors/invaliddetails.php"); exit(); }
}

function checkuserpass($username,$password){
  $ret=execute("SELECT ID FROM Users WHERE Name='" . mysql_escape_string($username) . "' AND (Password=encrypt('" . mysql_escape_string($password) . "', Password));");
  if (count($ret)==0){
    return false;
  }else{
    return $ret[0]['ID'];
  }
}

function userisadmin($userid){
  static $lastuserid, $lastresult;

  if ($userid != $lastuserid) {
    $lastuserid = $userid;
    $users = execute("SELECT Admin FROM Users WHERE ID='" . mysql_escape_string($userid) . "';");
    if (count($users) > 0){
      $lastresult = ($users[0]['Admin'] == 1);
    }else{
      $lastresult = false;
    }
  }
  return $lastresult;
}

function userdetails($userid) {
  $users = execute("SELECT ID, Name, AccountID, RealName, Hosting FROM Users WHERE ID='" . mysql_escape_string($userid) . "';");
  if (count($users) > 0){
    return $users[0];
  }else{
    return false;
  }
}

function domaindetails($domainid, $domainname){
  $sql = "SELECT ID, UserID, DomainName, Name, DNS, Mail FROM Domains WHERE ";
  if (isset($domainid)){
    $sql .= "ID='" . mysql_escape_string($domainid) . "';";
  }else{
    if (isset($domainname)){
      $sql .= "Name='" . mysql_escape_string($domainname) . "'";
    }else{
      return false;
    }
  }

  $domains = execute($sql);
  if (count($domains) > 0){
    $_REQUEST['userid'] = $domains[0]['UserID'];
    return $domains[0];
  }else{
    return false;
  }
}

function websitedetails($websiteid, $websitename){
  $sql = "SELECT ID, UserID, Name, Trial, Logging FROM Websites WHERE ";
  if (isset($websiteid)){
    $sql .= "ID='" . $websiteid . "';";
  }else{
    if (isset($websitename)){
      $sql .= "Name='" . mysql_escape_string($websitename) . "'";
    }else{
      return false;
    }
  }

  $websites = execute($sql);
  if (count($websites) > 0){
    $_REQUEST['userid'] = $websites[0]['UserID'];
    return $websites[0];
  }else{
    return false;
  }
}

function iif($expression, $truevalue, $falsevalue){
  if ($expression){
    return $truevalue;
  }else{
    return $falsevalue;
  }
}
?>
