<?php
require("../common.inc");

// Redirect back to te index page if they aren't logged in
if (!$loggedin){header("location: ../index.php"); exit();}

// If the logged in user isn't an admin, redirect back to the index
if (!(userisadmin($currentuserid))){header("location: ../index.php"); exit();}

print_header("Servers");
print "  <h3><a href=\"../index.php\">Home</a> - <a href=\"index.php\">System administration</a> - Servers</h3>\n";

if (isset($action)){
  if ($action == "addserver"){
    if (execute("INSERT INTO Servers (Name, FullName, Address, DNS, Mail, HTTP, DB) VALUES ('" . mysql_escape_string($name) . "', '" . mysql_escape_string($fullname) . "', '" . mysql_escape_string($address) . "', '" . mysql_escape_string($dns) . "', '" . mysql_escape_string($mail) . "', '" . mysql_escape_string($http) . "', '" . mysql_escape_string($db) . "');")){
      print "  <p class=status>Server added successfully.</p>\n";
    }else{
      print "  <p class=error>Error adding server.</p>\n";
    }
  }elseif ($action == "updateserver"){ 
    if (execute("UPDATE Servers SET Name='" . mysql_escape_string($name) . "', FullName='" . mysql_escape_string($fullname) . "', Address='" . mysql_escape_string($address) . "', DNS='" . mysql_escape_string($dns) . "', Mail='" . mysql_escape_string($mail) . "', HTTP='" . mysql_escape_string($http) . "', DB='" . mysql_escape_string($db) . "' WHERE ID='" . mysql_escape_string($serverid) . "';")){
      print "  <p class=status>Server updated successfully.</p>\n";
    }else{
      print "  <p class=error>Error updating server.</p>\n";
    }
  }elseif ($action == "deleteserver"){
    if (execute("DELETE FROM Servers WHERE ID='" . mysql_escape_string($serverid) . "';")){
      print "  <p class=status>Server deleted successfully.</p>\n";
    }else{
      print "  <p class=error>Error deleting server.</p>\n";
    }
  }
}

$servers = execute("SELECT ID, Name, FullName, Address, DNS, Mail, HTTP, DB FROM Servers ORDER BY Name;");
if ($servers){
?>
  <table backcolour=red>
   <tr><th>Actions</th><th>Name</th><th>Full name</th><th>Address</th><th>DNS</th><th>Mail</th><th>HTTP</th><th>DB</th></tr>
<?php
  for($row = 0; $row < count($servers); $row++){
    print "   <tr><td><div class=action><a href=\"servers.php?action=editserver&amp;serverid=" . $servers[$row]['ID'] . "#serverform\">edit</a> <a href=\"servers.php?action=deleteserver&amp;serverid=" .  $servers[$row]['ID']. "\">delete</a></div></td><td>" .htmlspecialchars($servers[$row]['Name']) . "</td><td>" . htmlspecialchars($servers[$row]['FullName']) . "</td><td>" . htmlspecialchars($servers[$row]['Address']) . "</td><td>" . iif($servers[$row]['DNS'], "Yes", "No") . "</td><td>" . iif($servers[$row]['Mail'], "Yes", "No") . "</td><td>" . iif($servers[$row]['HTTP'], "Yes", "No") . "</td><td>" . iif($servers[$row]['DB'], "Yes", "No") . "</td></tr>\n";
  }
?>
  </table>
<?php
}else{
  print "  <p>There are no servers</p>\n";
}

if ($action == "editserver"){
  $server = execute("SELECT Name, FullName, Address, DNS, Mail, HTTP, DB FROM Servers WHERE ID='" . mysql_escape_string($serverid) . "';");
?>
  <a name=serverform>
  <form action="servers.php" method="POST">
   <input name="action" type="hidden" value="updateserver">
   <input name="serverid" type="hidden" value="<?php print htmlspecialchars($serverid); ?>">
   <table>
    <tr><td>Server name</td><td><input name="name" value="<?php print htmlspecialchars($server[0]['Name']); ?>"> <a href="../help.php#servername">?</a></td></tr>
    <tr><td>Fully qualified name</td><td><input name="fullname" value="<?php print htmlspecialchars($server[0]['FullName']); ?>"> <a href="../help.php#serverfullname">?</a></td></tr>
    <tr><td>Address</td><td><input name="address" value="<?php print htmlspecialchars($server[0]['Address']); ?>"> <a href="../help.php#serveraddress">?</a></td></tr>
    <tr><td>DNS</td><td><input type="checkbox" name="dns" value=1<?php if ($server[0]['DNS']) { print " checked"; } ?>> <a href="../help.php#serverdns">?</a></td></tr>
    <tr><td>Mail</td><td><input type="checkbox" name="mail" value=1<?php if ($server[0]['Mail']) { print " checked"; } ?>> <a href="../help.php#servermail">?</a></td></tr>
    <tr><td>HTTP</td><td><input type="checkbox" name="http" value=1<?php if ($server[0]['HTTP']) { print " checked"; } ?>> <a href="../help.php#serverhttp">?</a></td></tr>
    <tr><td>Database</td><td><input type="checkbox" name="db" value=1<?php if ($server[0]['DB']) { print " checked"; } ?>> <a href="../help.php#serverdb">?</a></td></tr>
    <tr><td colspan=2 align=center><input type="submit" value="Update server"></td></tr>
   </table>
  </form>
<?php
}else{
?>
  <a name=serverform>
  <form action="servers.php" method="POST">
   <input name="action" type="hidden" value="addserver">
   <table>
    <tr><td>Server name</td><td><input name="name"> <a href="../help.php#servername">?</a></td></tr>
    <tr><td>Fully qualified name</td><td><input name="fullname"> <a href="../help.php#serverfullname">?</a></td></tr>
    <tr><td>Address</td><td><input name="address"> <a href="../help.php#serveraddress">?</a></td></tr>
    <tr><td>DNS</td><td><input type="checkbox" name="dns" value=1> <a href="../help.php#serverdns">?</a></td></tr>
    <tr><td>Mail</td><td><input type="checkbox" name="mail" value=1> <a href="../help.php#servermail">?</a></td></tr>
    <tr><td>HTTP</td><td><input type="checkbox" name="http" value=1> <a href="../help.php#serverhttp">?</a></td></tr>
    <tr><td>Database</td><td><input type="checkbox" name="db" value=1> <a href="../help.php#serverdb">?</a></td></tr>
    <tr><td colspan=2 align=center><input type="submit" value="Add server"></td></tr>
   </table>
  </form>
<?php
}

print_footer()
?>
