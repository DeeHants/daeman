<?php
require("../common.inc");

checkadminstatus();

print_header("Configuration");
print "  <h3><a href=\"../index.php\">Home</a> - <a href=\"index.php\">System administration</a> - Configuration</h3>\n";


if (isset($_REQUEST['action'])) {
  if ($_REQUEST['action'] == "addsetting") {
    if (execute("INSERT INTO Config (Name, AccountID, Password, DBPassword, RealName, Enabled, Hosting, Admin, DB) VALUES ('" . mysql_escape_string($_REQUEST['name']) . "', '" . mysql_escape_string($nextaccountid) . "', '!!', '', '" . mysql_escape_string($_REQUEST['realname']) . "', '" . mysql_escape_string($_REQUEST['enabled']) . "', " . iif($_REQUEST['mode'] == "customer", "1", "0") . ", " . iif($_REQUEST['mode'] == "admin", "1", "0") . ", 0);")) {
      print "  <p class=status>Setting added successfully.</p>\n";
    } else {
      print "  <p class=error>Error adding setting. $DBError</p>\n";
    }
  } elseif ($_REQUEST['action'] == "updatesetting") {
    if (execute("UPDATE Config SET Name='" . mysql_escape_string($_REQUEST['name']) . "', AccountID='" . mysql_escape_string($_REQUEST['accountid']) . "', RealName='" . mysql_escape_string($_REQUEST['realname']) . "', Enabled='" . mysql_escape_string($_REQUEST['enabled']) . "', Hosting='" . mysql_escape_string($_REQUEST['hosting']) . "', Admin='" . mysql_escape_string($_REQUEST['admin']) . "', DB='" . mysql_escape_string($_REQUEST['db']) . "' WHERE ID='" . mysql_escape_string($_REQUEST['userid']) . "';")) {
      print "  <p class=status>Setting updated successfully.</p>\n";
    } else {
      print "  <p class=error>Error updating setting.</p>\n";
    }
  } elseif ($_REQUEST['action'] == "deletesetting") {
    if (execute("DELETE FROM Config WHERE ID='" . mysql_escape_string($_REQUEST['userid']) . "';")) {
      print "  <p class=status>Setting deleted successfully.</p>\n";
    } else {
      print "  <p class=error>Error deleting setting.</p>\n";
    }
  }
}

$settings = execute("SELECT ID, ServerID, Name, Value FROM Config WHERE ServerID=0 OR ServerID='" . mysql_escape_string($_REQUEST['serverid']) . "' ORDER BY Name, ServerID DESC;");
if ($settings) {
?>
  <table>
   <tr><th>Actions</th><th>Name</th><th>Value</th></tr>
<?php
  for($row = 0; $row < count($settings); $row++) {
    if (($settings[$row]['ServerID'] == $_REQUEST['serverid']) || (!isset($_REQUEST['serverid']))) {
      print "   <tr><td><div class=action><a href=\"config.php?action=editsetting&amp;settingid=" . urlencode($settings[$row]['ID']) . "#settingform\">edit</a> <a href=\"config.php?action=deletesetting&amp;settingid=" . urlencode($settings[$row]['ID']) . "\">delete</a></div></td><td>" . htmlspecialchars(formatname($settings[$row]['Name'])) . "</td><td>" . htmlspecialchars($settings[$row]['Value']) . "</td></tr>\n";
      $added[$settings[$row]['Name']] = 1;
    } else {
      if (!$added[$settings[$row]['Name']]) {
        print "   <tr><td><div class=action><a href=\"config.php?action=copysetting&amp;settingid=" . urlencode($settings[$row]['ID']) . "#settingform\">copy</a></div></td><td>" . htmlspecialchars(formatname($settings[$row]['Name'])) . "</td><td>" . htmlspecialchars($settings[$row]['Value']) . "</td></tr>\n";
      }
    }
  }
?>
  </table>
<?php
} else {
  print "  <p>There are no configured settings</p>\n";
}

/*
if ($_REQUEST['action'] == "editsetting") {
  $user = execute("SELECT Name, AccountID, RealName, Enabled, Hosting, DB, Admin FROM Users WHERE ID='" . mysql_escape_string($_REQUEST['userid']) . "';");
?>
  <a name=userform></a>
  <form action="users.php" method="POST">
   <input name="action" type="hidden" value="updateuser">
   <input name="userid" type="hidden" value="<?php print htmlspecialchars($_REQUEST['userid']); ?>">
   <table>
    <tr><td>User name</td><td><input name="name" value="<?php print htmlspecialchars($user[0]['Name']); ?>"></td></tr>
    <tr><td>User ID</td><td><input name="accountid" value="<?php print htmlspecialchars($user[0]['AccountID']); ?>"></td></tr>
    <tr><td>Real name</td><td><input name="realname" value="<?php print htmlspecialchars($user[0]['RealName']); ?>"></td></tr>
    <tr><td>Enabled</td><td><input type="checkbox" name="enabled" value=1<?php if ($user[0]['Enabled']) { print " checked"; } ?>></td></tr>
    <tr><td>Hosting</td><td><input type="checkbox" name="hosting" value=1<?php if ($user[0]['Hosting']) { print " checked"; } ?>></td></tr>
    <tr><td>Database access</td><td><input type="checkbox" name="db" value=1<?php if ($user[0]['DB']) { print " checked"; } ?>></td></tr>
    <tr><td>Administrator</td><td><input type="checkbox" name="admin" value=1<?php if ($user[0]['Admin']) { print " checked"; } ?>></td></tr>
    <tr><td colspan=2 align=center><input type="submit" value="Update user"></td></tr>
   </table>
  </form>
<?php
} else {
?>
  <a name=userform></a>
  <form action="users.php" method="POST">
   <input name="action" type="hidden" value="adduser">
   <table>
    <tr><td>User name</td><td><input name="name"></td></tr>
    <tr><td>User mode</td><td><select name="mode"><option value="service">Service<option value="user">User<option value="customer" selected>Customer<option value="admin">Administrator</select></td></tr>
    <tr><td>Real name</td><td><input name="realname"></td></tr>
    <tr><td>Enabled</td><td><input type="checkbox" name="enabled" value=1 checked></td></tr>
    <tr><td colspan=2 align=center><input type="submit" value="Add user"></td></tr>
   </table>
  </form>
<?php
}
*/
print_footer();

function formatname($name) {
  $name = ereg_replace("([a-z])([A-Z])", "\\1 \\2", $name);
  $name = ereg_replace("([A-Z][a-z])", " \\1", $name);
  return $name;
}
?>
