<?php
require("../common.inc");
checkadminstatus();

print_header("Servers");
print "  <h3><a href=\"../index.php\">Home</a> - <a href=\"index.php\">System administration</a> - Servers</h3>\n";

if (isset($_REQUEST['action'])) {
  if ($_REQUEST['action'] == "addserver") {
    if (execute("INSERT INTO Servers (Name, FullName, Address, Live, DNS, Mail, HTTP, DB, List, Shell, Radius) VALUES ('" . mysql_escape_string($_REQUEST['name']) . "', '" . mysql_escape_string($_REQUEST['fullname']) . "', '" . mysql_escape_string($_REQUEST['address']) . "', '" . mysql_escape_string($_REQUEST['live']) . "', '" . mysql_escape_string($_REQUEST['dns']) . "', '" . mysql_escape_string($_REQUEST['mail']) . "', '" . mysql_escape_string($_REQUEST['http']) . "', '" . mysql_escape_string($_REQUEST['db']) . "', '" . mysql_escape_string($_REQUEST['list']) . "', '" . mysql_escape_string($_REQUEST['shell']) . "', '" . mysql_escape_string($_REQUEST['radius']) . "');")) {
      print "  <p class=status>Server added successfully.</p>\n";
    } else {
      print "  <p class=error>Error adding server.</p>\n";
    }
  } elseif ($_REQUEST['action'] == "updateserver") { 
    if (execute("UPDATE Servers SET Name='" . mysql_escape_string($_REQUEST['name']) . "', FullName='" . mysql_escape_string($_REQUEST['fullname']) . "', Address='" . mysql_escape_string($_REQUEST['address']) . "', Live='" . mysql_escape_string($_REQUEST['live']) ."', DNS='" . mysql_escape_string($_REQUEST['dns']) . "', Mail='" . mysql_escape_string($_REQUEST['mail']) . "', HTTP='" . mysql_escape_string($_REQUEST['http']) . "', DB='" . mysql_escape_string($_REQUEST['db']) . "', List='" . mysql_escape_string($_REQUEST['list']) ."', Shell='" . mysql_escape_string($_REQUEST['shell']) ."', Radius='" . mysql_escape_string($_REQUEST['radius']) ."' WHERE ID='" . mysql_escape_string($_REQUEST['serverid']) . "';")) {
      print "  <p class=status>Server updated successfully.</p>\n";
    } else {
      print "  <p class=error>Error updating server.</p>\n";
    }
  } elseif ($_REQUEST['action'] == "deleteserver") {
    if (execute("DELETE FROM Servers WHERE ID='" . mysql_escape_string($_REQUEST['serverid']) . "';")) {
      print "  <p class=status>Server deleted successfully.</p>\n";
    } else {
      print "  <p class=error>Error deleting server.</p>\n";
    }
  }
}

$servers = execute("SELECT ID, Name, FullName, Address, Live, Updated, DNS, Mail, HTTP, DB, List, Shell, Radius FROM Servers ORDER BY Name;");
if ($servers) {
?>
  <table>
   <tr><th>Actions</th><th>Name</th><th>Full name</th><th>Address</th><th>Live</th><th>Last updated</th><th>Uses</th></tr>
<?php
  for($row = 0; $row < count($servers); $row++) {
    unset($uses);
    if ($servers[$row]['DNS']) { $uses[] = "DNS"; }
    if ($servers[$row]['Mail']) { $uses[] = "Mail"; }
    if ($servers[$row]['HTTP']) { $uses[] = "HTTP"; }
    if ($servers[$row]['DB']) { $uses[] = "Database"; }
    if ($servers[$row]['List']) { $uses[] = "List"; }
    if ($servers[$row]['Shell']) { $uses[] = "Shell"; }
    if ($servers[$row]['Radius']) { $uses[] = "Radius"; }
    print " <tr><td><div class=action><a href=\"servers.php?action=editserver&amp;serverid=" . $servers[$row]['ID'] . "#serverform\">edit</a> <a href=\"servers.php?action=deleteserver&amp;serverid=" . $servers[$row]['ID']. "\">delete</a></div></td><td>" . htmlspecialchars($servers[$row]['Name']) . "</td><td>" . htmlspecialchars($servers[$row]['FullName']) . "</td><td>" . htmlspecialchars($servers[$row]['Address']) . "</td><td>" . iif($servers[$row]['Live'], "Yes", "No") . "</td><td>" . $servers[$row]['Updated'] . "</td><td>" . join(", ", $uses) . "</td></tr>\n";
  }
?>
  </table>
<?php
} else {
  print "  <p>There are no servers</p>\n";
}

if ($_REQUEST['action'] == "editserver") {
  $server = execute("SELECT Name, FullName, Address, Live, DNS, Mail, HTTP, DB, List, Shell, Radius FROM Servers WHERE ID='" . mysql_escape_string($_REQUEST['serverid']) . "';");
?>
  <a name=serverform></a>
  <form action="servers.php" method="POST">
   <input name="action" type="hidden" value="updateserver">
   <input name="serverid" type="hidden" value="<?php print htmlspecialchars($_REQUEST['serverid']); ?>">
   <table>
    <tr><td>Server name</td><td><input name="name" value="<?php print htmlspecialchars($server[0]['Name']); ?>"></td></tr>
    <tr><td>Fully qualified name</td><td><input name="fullname" value="<?php print htmlspecialchars($server[0]['FullName']); ?>"></td></tr>
    <tr><td>Address</td><td><input name="address" value="<?php print htmlspecialchars($server[0]['Address']); ?>"></td></tr>
    <tr><td>Live server</td><td><input type="checkbox" name="live" value=1<?php if ($server[0]['Live']) { print " checked"; } ?>></td></tr>
    <tr><td>DNS</td><td><input type="checkbox" name="dns" value=1<?php if ($server[0]['DNS']) { print " checked"; } ?>></td></tr>
    <tr><td>Mail</td><td><input type="checkbox" name="mail" value=1<?php if ($server[0]['Mail']) { print " checked"; } ?>></td></tr>
    <tr><td>HTTP</td><td><input type="checkbox" name="http" value=1<?php if ($server[0]['HTTP']) { print " checked"; } ?>></td></tr>
    <tr><td>Database</td><td><input type="checkbox" name="db" value=1<?php if ($server[0]['DB']) { print " checked"; } ?>></td></tr>
    <tr><td>List</td><td><input type="checkbox" name="list" value=1<?php if ($server[0]['List']) { print " checked"; } ?>></td></tr>
    <tr><td>Allow logins</td><td><input type="checkbox" name="shell" value=1<?php if ($server[0]['Shell']) { print " checked"; } ?>></td></tr>
    <tr><td>Radius authentication</td><td><input type="checkbox" name="radius" value=1<?php if ($server[0]['Radius']) { print " checked"; } ?>></td></tr>
    <tr><td colspan=2 align=center><input type="submit" value="Update server"></td></tr>
   </table>
  </form>
<?php
} else {
?>
  <a name=serverform></a>
  <form action="servers.php" method="POST">
   <input name="action" type="hidden" value="addserver">
   <table>
    <tr><td>Server name</td><td><input name="name"></td></tr>
    <tr><td>Fully qualified name</td><td><input name="fullname"></td></tr>
    <tr><td>Address</td><td><input name="address"></td></tr>
    <tr><td>Live server</td><td><input type="checkbox" name="live" value=1 checked></td></tr>
    <tr><td>DNS</td><td><input type="checkbox" name="dns" value=1></td></tr>
    <tr><td>Mail</td><td><input type="checkbox" name="mail" value=1></td></tr>
    <tr><td>HTTP</td><td><input type="checkbox" name="http" value=1></td></tr>
    <tr><td>Database</td><td><input type="checkbox" name="db" value=1></td></tr>
    <tr><td>List</td><td><input type="checkbox" name="list" value=1></td></tr>
    <tr><td>Allow logins</td><td><input type="checkbox" name="shell" value=1></td></tr>
    <tr><td>Radius authentication</td><td><input type="checkbox" name="radius" value=1></td></tr>
    <tr><td colspan=2 align=center><input type="submit" value="Add server"></td></tr>
   </table>
  </form>
<?php
}

print_footer()
?>
