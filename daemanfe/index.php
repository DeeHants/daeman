<?php
require("common.inc");

$loginfailed = false;

if ($_REQUEST['action'] == "logout"){
  $loggedin = false;
  $currentuserid = 0;
  session_unset();
}

if (isset($_REQUEST['loginusername']) && isset($_REQUEST['loginpassword'])) {
  $loginuserid = checkuserpass($_REQUEST['loginusername'], $_REQUEST['loginpassword']);
  if ($loginuserid){
    session_unset();
    $loggedin = true;
    $currentuserid = $loginuserid;
    session_register("loggedin", "currentuserid");
    setcookie("lastusername", $_REQUEST['loginusername']);
    if (isset($_REQUEST['url'])) {
      header("Location: " . $_REQUEST['url']);
      exit;
    }
  }else{
    $loggedin = false;
    $currentuserid = 0;
    session_unset();
    $loginfailed = true;
  }
}

if ($loggedin){
  $details = userdetails($currentuserid);
  print_header("Logged in: " . $details['RealName']);
?>
  <h3>Home</h3>
  <p><a href="user.php?userid=<?php print urlencode($currentuserid); ?>">Administer account</a></p>
  <p><a href="chpasswd.php?userid=<?php print urlencode($currentuserid); ?>">Change password</a></p>
<?php
  if (userisadmin($currentuserid)){
?>
  <p><a href="admin/">System administration</a></p>
<?php
  }
?>
  <p><a href="index.php?action=logout">Log out</a></p>
<?php
  print_footer();
}else{
  print_header("Login");
  if ($loginfailed){
?>
  <p class=error>Login failed</p>
<?php
  }
?>
  <form action="index.php" method="POST">
<?php
if (isset($_REQUEST['url'])) { print "   <input type=hidden name=\"url\" value=\"" . htmlspecialchars($_REQUEST['url']) . "\">\n"; }
?>
   <table>
    <tr><td>User name</td><td><input name="loginusername" value="<?php print htmlspecialchars($_COOKIE['lastusername']); ?>"></td></tr>
    <tr><td>Password</td><td><input name="loginpassword" type="password"></td></tr>
    <tr><td colspan=2 align=center><input type=submit value="Login"></td></tr>
   </table>
  </form>
<?php
  print_footer();
}
?>
