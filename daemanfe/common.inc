<?php
require("ui.inc");
require("config");
session_start();

$loggedin = $HTTP_SESSION_VARS['loggedin'];
$currentuserid = $HTTP_SESSION_VARS['currentuserid'];
$currentusername = $HTTP_SESSION_VARS['currentusername'];

$DBUser = $DBUsername;
$DBPass = $DBPassword;
require("mysql.inc");

/****************/
/* User details */
/****************/

function checkuserpass($username,$password){
  $ret=execute("SELECT ID FROM Users WHERE Name='" . mysql_escape_string($username) . "' AND (Password=encrypt('" . mysql_escape_string($password) . "', Password));");
  if (count($ret)==0){
    return false;
  }else{
    return $ret[0]['ID'];
  }
}

function userisadmin($userid){
  $sql = "SELECT Admin FROM Users WHERE ID='" . mysql_escape_string($userid) . "';";
  $users = execute($sql);
  if (count($users) > 0){
    return ($users[0]['Admin'] == 1);
  }else{
    return false;
  }
}

function checkstatus(){
  global $loggedin, $currentuserid, $userid, $username, $details;

  // Redirect back to the index page if they aren't logged in
  if (!$loggedin){header("location: errors/notloggedin.php"); exit();}

  // If there isn't a specific account ID, use the currently logged in ID
  if (!isset($userid) && !isset($username)){ $userid = $currentuserid;}
  // If there are no account details for this account, redirect to the index
  $details = userdetails($userid, $username);
  if (!$details){header("location: errors/invaliddetails.php"); exit();}
  // If the manually specified account does not belong to this user, redirect back to the index
  if (!(($userid == $currentuserid) || userisadmin($currentuserid))){header("location: errors/invaliddetails.php"); exit();}

  return $userid;
}

function userdetails(&$userid, &$username){
  $sql = "SELECT ID, Name, AccountID, Password, RealName FROM Users WHERE ";
  if (isset($userid)){
    $sql .= "ID='" . mysql_escape_string($userid) . "';";
  }else{
    if (isset($username)){
      $sql .= "Name='" . mysql_escape_string($username) . "'";
    }else{
      return false;
    }
  }

  $users = execute($sql);
  if (count($users) > 0){
    $userid = $users[0]['ID'];
    $username = $users[0]['Name'];
    return $users[0];
  }else{
    return false;
  }
}

function domaindetails(&$domainid, &$domainname, &$domaintitle){
global $userid;
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
    $domainid = $domains[0]['ID'];
    $domainname = $domains[0]['DomainName'];
    $domaintitle = $domains[0]['Name'];
    $userid = $domains[0]['UserID'];
    return $domains[0];
  }else{
    return false;
  }
}

function websitedetails(&$websiteid, &$websitename){
global $userid;
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
    $websiteid = $websites[0]['ID'];
    $websitename = $websites[0]['Name'];
    $userid = $websites[0]['UserID'];
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
