<?php
require("common.inc");
checkstatus();

print_header("Change password: " . htmlspecialchars($details['RealName']));
print "  <h3><a href=\"index.php\">Home</a> - Password</h3>\n";
$changed=false;
if ($_REQUEST['action'] == "setpassword") {
  if ($_REQUEST['newpassword'] != $_REQUEST['newpasswordrepeat']) {
    print "   <p class=error>The passwords do not match. Please retype them.</p>";
  } else {
    if (isset($_REQUEST['accountid'])) {
      $sql="UPDATE Accounts SET Password=Encrypt('" . mysql_escape_string($_REQUEST['newpassword']) . "', Concat('$1$', Round(Rand()*100000))) WHERE UserID='" . mysql_escape_string($details['ID']) . "' AND ID='" . mysql_escape_string($_REQUEST['accountid']) . "';";
    } else {
      $sql="UPDATE Users SET Password=Encrypt('" . mysql_escape_string($_REQUEST['newpassword']) . "', Concat('$1$', Round(Rand()*100000))), DBPassword = Password('" . mysql_escape_string($_REQUEST['newpassword']) . "'), PasswordChanged=Now() WHERE ID='" . mysql_escape_string($details['ID']) . "';";
    }
    if (execute($sql)) {
      print "   <p class=status>Password successfully changed.</p>";
      $changed=true;
    } else {
      print "   <p class=error>There was an error changing the password.</p>";
    }
  }
}

if (!$changed) {
?>
  <form action="chpasswd.php" method="POST">
   <input name="action" type="hidden" value="setpassword">
   <input name="userid" type="hidden" value="<?php print htmlspecialchars($details['ID']); ?>">
<?php
  if (isset($_REQUEST['accountid'])) {
?>
   <input name="accountid" type="hidden" value="<?php print htmlspecialchars($_REQUEST['accountid']); ?>">
<?php
  }
?>
   <table border=0>
    <tr><td>New password</td><td><input name="newpassword" type="password"></td></tr>
    <tr><td>Retype new password</td><td><input name="newpasswordrepeat" type="password"></td></tr>
    <tr><td colspan=2 align=center><input type="submit" value="Change password"></td></tr>
   </table>
  </form>
<?php
}
print_footer();
?>
