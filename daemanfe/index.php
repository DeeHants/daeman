<?php
require("common.inc");

$loginfailed = false;

if (isset($action)){
  if ($action=="logout"){
    $loggedin=false;
    $currentuserid=false;
    $currentusername="";
    session_unset("loggedin", "currentuserid", "currentusername");
  }
}

if (isset($loginusername) && isset($loginpassword)){
  $loginuserid = checkuserpass($loginusername, $loginpassword);
  if ($loginuserid){
    $loggedin=true;
    $currentuserid=$loginuserid;
    $currentusername=$loginusername;
    session_register("loggedin", "currentuserid", "currentusername");
    setcookie("lastusername", $loginusername);
  }else{
    $loggedin=false;
    $currentuserid=false;
    $currentusername="";
    session_unset();
    $loginfailed = true;
  }
}

if ($loggedin){
  print_header("Logged in: " . $currentusername);
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
  <form method="POST" action="<?php print $PHP_SELF; ?>">
   <table>
    <tr><td>User name</td><td><input name="loginusername" value="<?php print htmlspecialchars($HTTP_COOKIE_VARS['lastusername']); ?>"></td></tr>
    <tr><td>Password</td><td><input name="loginpassword" type="password"></td></tr>
    <tr><td colspan=2 align=center><input type=submit value="Login"></td></tr>
   </table>
  </form>
<?php
  print_footer();
}
?>
