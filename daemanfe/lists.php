<?php
require("common.inc");

checkstatus();

print_header("Mailing lists: " . htmlspecialchars($details['RealName']));
?>
  <h3><a href="index.php">Home</a> - <a href="user.php?userid=<?php print urlencode($details['ID']); ?>">Account</a> - Mailing lists</h3>
  <h2>Lists</h2>
<?php
if (isset($_REQUEST['action'])) {
  if ($_REQUEST['action'] == "addlist") {
    if (execute("INSERT INTO Lists (UserID, Name, Owner, Public, ServerID) VALUES ('" . mysql_escape_string($details['ID']) . "', '" . mysql_escape_string($_REQUEST['name']) . "', '" . mysql_escape_string($_REQUEST['owner']) . "', '" . mysql_escape_string($_REQUEST['public']) . "', '" . mysql_escape_string($_REQUEST['serverid']) . "');")) {
      print "   <p class=status>" . htmlspecialchars($_REQUEST['name']) . " successfully added.</p>";
    } else {
      print "   <p class=error>There was an error adding " . htmlspecialchars($_REQUEST['name']) . ".</p>";
    }
  } elseif ($_REQUEST['action'] == "updatelist") {
    if (execute("UPDATE Lists SET Owner='" . mysql_escape_string($_REQUEST['owner']) . "', Public='" . mysql_escape_string($_REQUEST['public']). "', ServerID='" . mysql_escape_string($_REQUEST['serverid']). "' WHERE ID='" . mysql_escape_string($_REQUEST['listid']) . "';")) {
      print "  <p class=status>Mailing list updated successfully.</p>\n";
    } else {
      print "  <p class=error>Error updating mail list.</p>\n";
    }
  } elseif ($_REQUEST['action'] == "deletelist") {
    if (execute("DELETE FROM Lists WHERE ID='" . mysql_escape_string($_REQUEST['listid']) . "';")) {
      print "  <p class=status>Mailing list deleted successfully.</p>\n";
    } else {
      print "  <p class=error>Error deleting mailing list.</p>\n";
    }
  }
}

$lists = execute("SELECT ID, Name, Owner, Public FROM Lists WHERE UserID='" . mysql_escape_string($details['ID']) . "';");

if ($lists) {
?>
  <table>
   <tr><th>Actions</th><th>Name</th><th>Owner</th><th>Public</th></tr>
<?php
  for($row = 0; $row < count($lists); $row++) {
    print "  <tr><td><div class=action><a href=\"?action=editlist&amp;userid=" . urlencode($details['ID']) . "&amp;listid=" . urlencode($lists[$row]['ID']) . "\">edit</a> <a href=\"?action=deletelist&amp;userid=" . urlencode($details['ID']) . "&amp;listid=" . urlencode($lists[$row]['ID']) . "\">delete</a></div></td><td>" . htmlspecialchars($lists[$row]['Name']) . "</td><td>" . htmlspecialchars($lists[$row]['Owner'])."</td><td>" . iif($lists[$row]['Public'], "Yes", "No") . "</td></tr>\n";
  }
?>
  </table>
<?php
} else {
  print "  <p>There are no mailing lists</p>\n";
}

if ($_REQUEST['action'] == "editlist") {
  $list = execute("SELECT Name, Owner, Public, ServerID FROM Lists WHERE ID = " . mysql_escape_string($_REQUEST['listid']) . " ORDER BY Name;");
?>
  <form action="lists.php" method="POST">
   <input name="action" type="hidden" value="updatelist">
   <input name="userid" type="hidden" value="<?php print htmlspecialchars($details['ID']); ?>">
   <input name="listid" type="hidden" value="<?php print htmlspecialchars($_REQUEST['listid']); ?>">
   <table>
    <tr><td>List name</td><td><?php print htmlspecialchars($list[0]['Name']); ?></td></tr>
    <tr><td>Owner</td><td><input name="owner" value="<?php print htmlspecialchars($list[0]['Owner']); ?>"></td></tr>
    <tr><td>Public</td><td><input type="checkbox" name="public" value=1<?php if ($list[0]['Public']) { print " checked"; } ?>></td></tr>
    <tr><td>Server</td><td><select name="serverid"><?php $servers = execute("SELECT ID, Name FROM Servers WHERE List=1;"); for ($serverid = 0; $serverid < count($servers); $serverid++) { print "<option value=\"" . htmlspecialchars($servers[$serverid]['ID']) . "\"" . iif($website[0]['ServerID'] == $servers[$serverid]['ID'], " selected", "") . ">" . htmlspecialchars($servers[$serverid]['Name']); } ?></select></td></tr>
    <tr><td colspan=2 align=center><input type="submit" value="Update list"></td></tr>
   </table>
  </form>
<?php
} else {
?>
  <form action="lists.php" method="POST">
   <input name="action" type="hidden" value="addlist">
   <input name="userid" type="hidden" value="<?php print htmlspecialchars($details['ID']); ?>">
   <table>
    <tr><td>List name</td><td><input name="name"></td></tr>
    <tr><td>Owner</td><td><input name="owner"></td></tr>
    <tr><td>Public</td><td><input type="checkbox" name="public" value=1></td></tr>
    <tr><td>Server</td><td><select name="serverid"><?php $servers = execute("SELECT ID, Name FROM Servers WHERE List=1;"); for ($serverid = 0; $serverid < count($servers); $serverid++) { print "<option value=\"" . htmlspecialchars($servers[$serverid]['ID']) . "\">" . htmlspecialchars($servers[$serverid]['Name']); } ?></select></td></tr>
    <tr><td colspan=2 align=center><input type="submit" value="Add new list"></td></tr>
   </table>
  </form>
<?php
}

print_footer();
?>
