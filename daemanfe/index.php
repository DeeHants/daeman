<?php
require("common.inc");

$loginfailed = false;

if ($_REQUEST['action'] == "logout") {
  $loggedin = false;
  $currentuserid = 0;
  $userisadmin = 0;
  session_unset();
}

if (isset($_REQUEST['loginusername']) && isset($_REQUEST['loginpassword'])) {
  $user = execute("SELECT ID, Admin FROM Users WHERE Name='" . mysql_escape_string($_REQUEST['loginusername']) . "' AND (Password=encrypt('" . mysql_escape_string($_REQUEST['loginpassword']) . "', Password));");
  if (count($user) > 0) {
    session_unset();
    $loggedin = true;
    $currentuserid = $user[0]['ID'];
    $userisadmin = ($user[0]['Admin'] == 1);
    session_register("loggedin", "currentuserid", "userisadmin");
    $_SESSION['loggedin'] = $loggedin;
    $_SESSION['currentuserid'] = $currentuserid;
    $_SESSION['userisadmin'] = $userisadmin;

    setcookie("lastusername", $_REQUEST['loginusername'], time() + 31536000);
    if (isset($_REQUEST['url'])) {
      header("Location: " . $_REQUEST['url']);
      exit;
    }
  } else {
    $loggedin = false;
    $currentuserid = 0;
    $userisadmin = 0;
    session_unset();
    $loginfailed = true;
  }
}

if ($_SESSION['loggedin']) {
  $details = userdetails($_SESSION['currentuserid']);
  print_header("Logged in: " . $details['RealName']);
?>
  <h3>Home</h3>
<?php
  if ($details['Hosting'] == 1) {
?>
  <p><a href="user.php?userid=<?php print urlencode($_SESSION['currentuserid']); ?>">Administer account</a></p>
<?php
  }
?>
  <p><a href="chpasswd.php?userid=<?php print urlencode($_SESSION['currentuserid']); ?>">Change password</a></p>
  <p><a href="tools/">Tools</a></p>
<?php
  if ($_SESSION['userisadmin']) {
?>
  <p><a href="admin/">System administration</a></p>
<?php
  }
?>
  <p><a href="help.php">Help</a></p>
  <p><a href="index.php?action=logout">Log out</a></p>
<?php
  print_footer();
} else {
  print_header("Login");
  if ($loginfailed) {
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
