<?php
require("common.inc");

checkstatus();

$ddetails = domaindetails($domainid, $domainname, $domaintitle);
if (!$ddetails){
  checkstatus();
  print "This is not a valid domain<br>\n";
  exit;
}
checkstatus();

print_header("Hosted domain: " . htmlspecialchars($domainname));
?>
  <h3><a href="index.php">Home</a> - <a href="user.php?userid=<?php print urlencode($userid); ?>">Account</a> - <a href="domains.php?userid=<?php print urlencode($userid); ?>">Domains</a> - <?php print htmlspecialchars($domainname); ?></h3>
<?php

if (isset($action)){
  if ($action == "addalias"){
/*
    if ($type == "address" && $data == "") {
      print "  <p class=error>You need to specifiy an address destination.</p>\n";
      $action = "correctalias";
      break;
    }
*/
    if (execute("INSERT INTO Aliases (DomainID, Name, Type, Data) VALUES ('" . mysql_escape_string($domainid) . "', '" . mysql_escape_string($name) . "', '" . mysql_escape_string($type) . "', '" . mysql_escape_string($data) . "');")){
      print "  <p class=status>Email alias added successfully.</p>\n";
    }else{
      print "  <p class=error>Error adding email alias.</p>\n";
    }
  }elseif ($action == "updatealias"){
    if (execute("UPDATE Aliases SET Name='" . mysql_escape_string($name) . "', Type='" . mysql_escape_string($type) . "', Data='" . mysql_escape_string($data) . "' WHERE ID='" . mysql_escape_string($aliasid) . "';")){
      print "  <p class=status>Email alias updated successfully.</p>\n";
    }else{
      print "  <p class=error>Error updating email alias.</p>\n";
    }
  }elseif ($action == "deletealias"){
    if (execute("DELETE FROM Aliases WHERE ID='" . mysql_escape_string($aliasid) . "';")){
      print "  <p class=status>Email alias deleted successfully.</p>\n";
    }else{
      print "  <p class=error>Error deleting email alias.</p>\n";
    }
  }elseif ($action == "addhost"){
    if (execute("INSERT INTO Hosts (DomainID, Name, Type, Data) VALUES ('" . mysql_escape_string($domainid) . "', '" . mysql_escape_string($name) . "', '" . mysql_escape_string($type) . "', '" . mysql_escape_string($data) . "');")){
      print "  <p class=status>Host added successfully.</p>\n";
    }else{
      print "  <p class=error>Error adding host.</p>\n";
    }
  }elseif ($action == "updatehost"){
    if (execute("UPDATE Hosts SET Name='" . mysql_escape_string($name) . "', Type='" . mysql_escape_string($type) . "', Data='" . mysql_escape_string($data) . "' WHERE ID='" . mysql_escape_string($hostid) . "';")){
      print "  <p class=status>Host updated successfully.</p>\n";
    }else{
      print "  <p class=error>Error updating Host.</p>\n";
    }
  }elseif ($action == "deletehost"){
    if (execute("DELETE FROM Hosts WHERE ID='" . mysql_escape_string($hostid) . "';")){
      print "  <p class=status>Host deleted successfully.</p>\n";
    }else{
      print "  <p class=error>Error deleting host.</p>\n";
    }
  }
}
?>
  <h2>Email aliases</h2>
<?php
if ($ddetails['Mail'] == "primary") {
  $aliases = execute("SELECT ID, Name, Type, Data FROM Aliases WHERE DomainID='" . mysql_escape_string($domainid) . "' ORDER BY Name;");

  if ($aliases){
?>
  <table backcolour=red>
   <tr><th>Actions</th><th>Address</th><th>Type</th><th>Destination</th></tr>
<?php
    for($row = 0; $row < count($aliases); $row++){
      print "   <tr><td><div class=action><a href=\"?action=editalias&amp;domainid=" . urlencode($domainid) . "&amp;aliasid=" . urlencode($aliases[$row]['ID']) . "#aliasform\">edit</a> <a href=\"?action=deletealias&amp;domainid=" . urlencode($domainid) . "&amp;aliasid=" . urlencode($aliases[$row]['ID']) . "\">delete</a></div></td><td>";
      if ($aliases[$row]['Name'] == ""){
        print "Default";
      }else{
        print "<a href=\"mailto:" . urlencode($aliases[$row]['Name']) . "@" . urlencode($domainname) . "\">" . htmlspecialchars($aliases[$row]['Name']) ."</a>";
      }
      print "</td><td>";
      if ($aliases[$row]['Type'] == "account"){
        print "Mail account</td><td>" . htmlspecialchars($aliases[$row]['Data']) . "</td></tr>\n";
      }elseif ($aliases[$row]['Type'] == "address"){
        print "Email address</td><td>";
        $emails = split(",", $aliases[$row]['Data']);
        for ($email = 0; $email < count($emails); $email++){
          $emails[$email] = str_replace(" ", "", $emails[$email]);
          if (!strstr($emails[$email], "@")) { $emails[$email] = $emails[$email] ."@" . $domainname; }
          print "<a href=\"mailto:" . urlencode($emails[$email]) . "\">" . htmlspecialchars($emails[$email]) . "</a> ";
        }
        print "</td></tr>\n";
      }
    }
?>
  </table>
<?php
  } else {
    print "  <p>There are no email aliases</p>\n";
  }

  if ($action == "editalias"){
    $alias = execute("SELECT Name, Type, Data FROM Aliases WHERE ID='" . mysql_escape_string($aliasid) . "';");
?>
  <a name=aliasform>
  <form action="domain.php" method="POST">
   <input name="action" type="hidden" value="updatealias">
   <input name="domainid" type="hidden" value="<?php print htmlspecialchars($domainid); ?>">
   <input name="aliasid" type="hidden" value="<?php print htmlspecialchars($aliasid); ?>">
   <table>
    <tr><td>Address name</td><td><input name="name" value="<?php print htmlspecialchars($alias[0]['Name']); ?>"> <a href="help.php#aliasname">?</a></td></tr>
    <tr><td>Address type</td><td><select name="type"><option value="account"<?php if($alias[0]['Type']=="account"){ print " selected";} ?>>Mail account<option value="address"<?php if ($alias[0]['Type']=="address"){ print " selected";} ?>>Email address</select> <a href="help.php#aliastype">?</a></td></tr>
    <tr><td>Destination</td><td><input name="data" value="<?php print htmlspecialchars($alias[0]['Data']); ?>"> <a href="help.php#aliasdata">?</a></td></tr>
    <tr><td colspan=2 align=center><input type="submit" value="Update email address"></td></tr>
   </table>
  </form>
<?php
  }else{
?>
  <a name=aliasform>
  <form action="domain.php" method="POST">
   <input name="action" type="hidden" value="addalias">
   <input name="domainid" type="hidden" value="<?php print htmlspecialchars($domainid); ?>">
   <table>
    <tr><td>Address name</td><td><input name="name"> <a href="help.php#aliasname">?</a></td></tr>
    <tr><td>Address type</td><td><select name="type"><option value="account">Mail account<option value="address" selected>Email address</select> <a href="help.php#aliastype">?</a></td></tr>
    <tr><td>Destination</td><td><input name="data"> <a href="help.php#aliasdata">?</a></td></tr>
    <tr><td colspan=2 align=center><input type="submit" value="Add email address"></td></tr>
   </table>
  </form>
<?php
  }
} elseif ($ddetails['Mail'] == "secondary") {
  print "  <p>We are not the primary Mail server for this domain.</p>\n";
} else {
  print "  <p>Email is not enabled for this domain.</p>";
}

print "  <h2>DNS hosts</h2>";

if ($ddetails['DNS'] == "primary") {
  $hosts = execute("SELECT ID, Name, Type, Data FROM Hosts WHERE DomainID='" . mysql_escape_string($domainid) . "' ORDER BY Name;");

  if ($hosts){
?>
  <table backcolour=red>
   <tr><th>Actions</th><th>Address</th><th>Type</th><th>Destination</th></tr>
<?php
    for($row = 0; $row < count($hosts); $row++){
      print "   <tr><td><div class=action><a href=\"?action=edithost&amp;domainid=" . urlencode($domainid) . "&amp;hostid=" . urlencode($hosts[$row]['ID']) . "#hostform\">edit</a> <a href=\"?action=deletehost&amp;domainid=" . urlencode($domainid) . "&amp;hostid=" . urlencode($hosts[$row]['ID']) . "\">delete</a></div></td><td>";
      if ($hosts[$row]['Name'] == ""){
        print "<a href=\"http://" . urlencode($domainname) . "\" target=\"_blank\">Default</a>";
      }else{
        print "<a href=\"http://" . urlencode($hosts[$row]['Name']) . "." . urlencode($domainname) . "\" target=\"_blank\">" . htmlspecialchars($hosts[$row]['Name']) . "</a>";
      }
      print "</td><td>";
      if ($hosts[$row]['Type'] == "website"){
        print "Hosted site</td><td><a href=\"website.php?websiteid=" . urlencode($hosts[$row]['Data']) . "\">";
        $webname = execute("SELECT Name FROM Websites WHERE ID='" . mysql_escape_string($hosts[$row]['Data']) . "';");
        if ($webname) {
          print htmlspecialchars($webname[0]['Name']);
        }
        print "</a>";
      }elseif ($hosts[$row]['Type'] == "a"){
        print "IP address</td><td>" . htmlspecialchars($hosts[$row]['Data']);
      }elseif ($hosts[$row]['Type'] == "cname"){
        print "Pointer</td><td>" . htmlspecialchars($hosts[$row]['Data']);
      }elseif ($hosts[$row]['Type'] == "subdomain"){
        print "Subdomain</td><td>" . iif($hosts[$row]['Data'] != "", htmlspecialchars($hosts[$row]['Data']), "local");
      }
      print "</td></tr>\n";
    }
?>
  </table>
<?php
  }else{
    print "  <p>There are no hosts</p>\n";
  }

  if ($action == "edithost"){
    $host = execute("SELECT Name, Type, Data FROM Hosts WHERE ID='" . mysql_escape_string($hostid) . "';");
?>
  <a name=hostform>
  <form action="domain.php" method="POST">
   <input name="action" type="hidden" value="updatehost">
   <input name="domainid" type="hidden" value="<?php print htmlspecialchars($domainid); ?>">
   <input name="hostid" type="hidden" value="<?php print htmlspecialchars($hostid); ?>">
   <table>
    <tr><td>Host name</td><td><input name="name" value="<?php print htmlspecialchars($host[0]['Name']); ?>"> <a href="help.php#hostname">?</a></td></tr>
    <tr><td>Host type</td><td><select name="type"><option value="website"<?php if($host[0]['Type']=="website"){ print " selected";} ?>>Hosted site<option value="a"<?php if($host[0]['Type']=="a"){ print " selected";} ?>>IP Address<option value="cname"<?php if($host[0]['Type']=="cname"){ print " selected";}?>>Pointer<option value="subdomain"<?php if($host[0]['Type']=="subdomain"){ print " selected";}?>>Subdomain</select> <a href="help.php#hosttype">?</a></td></tr>
    <tr><td>Data</td><td><input name="data" value="<?php print htmlspecialchars($host[0]['Data']); ?>"> <a href="help.php#hostdata">?</a></td></tr>
    <tr><td colspan=2 align=center><input type="submit" value="Update host"></td></tr>
   </table>
  </form>
<?php
  }else{
?>
  <a name=hostform>
  <form action="domain.php" method="POST">
   <input name="action" type="hidden" value="addhost">
   <input name="domainid" type="hidden" value="<?php print htmlspecialchars($domainid); ?>">
   <table>
    <tr><td>Host name</td><td><input name="name"> <a href="help.php#hostname">?</a></td></tr>
    <tr><td>Host type</td><td><select name="type"><option value="website">Hosted site<option value="a">IP Address<option value="cname" selected>Pointer<option value="subdomain">Subdomain</select> <a href="help.php#hosttype">?</a></td></tr>
    <tr><td>Data</td><td><input name="data"> <a href="help.php#hostdata">?</a></td></tr>
    <tr><td colspan=2 align=center><input type="submit" value="Add host"></td></tr>
   </table>
  </form>
<?php
  }
} elseif ($ddetails['DNS'] == "secondary") {
  print "  <p>We are not the primary DNS server for this domain.</p>\n";
}else{
  print "  <p>DNS is not enabled for this domain.</p>";
}
print_footer()
?>
