<?php
require("common.inc");

checkstatus();

$ddetails = domaindetails($_REQUEST['domainid'], $_REQUEST['domainname']);
if (!$ddetails) {
  checkstatus();
  print "This is not a valid domain<br>\n";
  exit;
}
checkstatus();

print_header("Hosted domain: " . htmlspecialchars($ddetails['DomainName']));
?>
  <h3><a href="index.php">Home</a> - <a href="user.php?userid=<?php print urlencode($details['ID']); ?>">Account</a> - <a href="domains.php?userid=<?php print urlencode($details['ID']); ?>">Domains</a> - <?php print htmlspecialchars($ddetails['DomainName']); ?></h3>
<?php

if (isset($_REQUEST['action'])) {
  if ($_REQUEST['action'] == "addalias") {
/*
    if ($_REQUEST['type'] == "address" && $_REQUEST['data'] == "") {
      print "  <p class=error>You need to specifiy an address destination.</p>\n";
      $_REQUEST['action'] = "correctalias";
      break;
    }
*/
    if (execute("INSERT INTO Aliases (DomainID, Name, Type, Data) VALUES ('" . mysql_escape_string($ddetails['ID']) . "', '" . mysql_escape_string($_REQUEST['name']) . "', '" . mysql_escape_string($_REQUEST['type']) . "', '" . mysql_escape_string($_REQUEST['data']) . "');")) {
      print "  <p class=status>Email alias added successfully.</p>\n";
    } else {
      print "  <p class=error>Error adding email alias.</p>\n";
    }
  } elseif ($_REQUEST['action'] == "updatealias") {
    if (execute("UPDATE Aliases SET Name='" . mysql_escape_string($_REQUEST['name']) . "', Type='" . mysql_escape_string($_REQUEST['type']) . "', Data='" . mysql_escape_string($_REQUEST['data']) . "' WHERE ID='" . mysql_escape_string($_REQUEST['aliasid']) . "';")) {
      print "  <p class=status>Email alias updated successfully.</p>\n";
    } else {
      print "  <p class=error>Error updating email alias.</p>\n";
    }
  } elseif ($_REQUEST['action'] == "deletealias") {
    if (execute("DELETE FROM Aliases WHERE ID='" . mysql_escape_string($_REQUEST['aliasid']) . "';")) {
      print "  <p class=status>Email alias deleted successfully.</p>\n";
    } else {
      print "  <p class=error>Error deleting email alias.</p>\n";
    }
  } elseif ($_REQUEST['action'] == "addhost") {
    if (execute("INSERT INTO Hosts (DomainID, Name, Type, Data) VALUES ('" . mysql_escape_string($ddetails['ID']) . "', '" . mysql_escape_string($_REQUEST['name']) . "', '" . mysql_escape_string($_REQUEST['type']) . "', '" . mysql_escape_string($_REQUEST['data']) . "');")) {
      print "  <p class=status>Host added successfully.</p>\n";
    } else {
      print "  <p class=error>Error adding host.</p>\n";
    }
  } elseif ($_REQUEST['action'] == "updatehost") {
    if (execute("UPDATE Hosts SET Name='" . mysql_escape_string($_REQUEST['name']) . "', Type='" . mysql_escape_string($_REQUEST['type']) . "', Data='" . mysql_escape_string($_REQUEST['data']) . "' WHERE ID='" . mysql_escape_string($_REQUEST['hostid']) . "';")) {
      print "  <p class=status>Host updated successfully.</p>\n";
    } else {
      print "  <p class=error>Error updating Host.</p>\n";
    }
  } elseif ($_REQUEST['action'] == "deletehost") {
    if (execute("DELETE FROM Hosts WHERE ID='" . mysql_escape_string($_REQUEST['hostid']) . "';")) {
      print "  <p class=status>Host deleted successfully.</p>\n";
    } else {
      print "  <p class=error>Error deleting host.</p>\n";
    }
  }
}
?>
  <h2>Email aliases</h2>
<?php
if ($ddetails['Mail'] == "primary") {
  $aliases = execute("SELECT ID, Name, Type, Data FROM Aliases WHERE DomainID='" . mysql_escape_string($ddetails['ID']) . "' ORDER BY Name;");

  if ($aliases) {
?>
  <table>
   <tr><th>Actions</th><th>Address</th><th>Type</th><th>Destination</th></tr>
<?php
    for($row = 0; $row < count($aliases); $row++) {
      print "   <tr><td><div class=action><a href=\"?action=editalias&amp;domainid=" . urlencode($ddetails['ID']) . "&amp;aliasid=" . urlencode($aliases[$row]['ID']) . "#aliasform\">edit</a> <a href=\"?action=deletealias&amp;domainid=" . urlencode($ddetails['ID']) . "&amp;aliasid=" . urlencode($aliases[$row]['ID']) . "\">delete</a></div></td><td>";
      if ($aliases[$row]['Name'] == "") {
        print "Default";
      } else {
        print "<a href=\"mailto:" . urlencode($aliases[$row]['Name']) . "@" . urlencode($ddetails['DomainName']) . "\">" . htmlspecialchars($aliases[$row]['Name']) ."</a>";
      }
      print "</td><td>";
      if ($aliases[$row]['Type'] == "account") {
//        print "Mail account</td><td><a href=\"account.php?accountid=" . urlencode($aliases[$row]['Data']) . "\">";
        print "Mail account</td><td>";
        $accountname = execute("SELECT Name FROM Accounts WHERE ID='" . mysql_escape_string($aliases[$row]['Data']) . "';");
        if ($accountname) {
          print htmlspecialchars($accountname[0]['Name']);
        } else {
          print htmlspecialchars($aliases[$row]['Data']);
        }
//        print "</a>";

      } elseif ($aliases[$row]['Type'] == "address") {
        print "Email address</td><td>";
        $emails = split(",", $aliases[$row]['Data']);
        for ($email = 0; $email < count($emails); $email++) {
          $emails[$email] = str_replace(" ", "", $emails[$email]);
          if ($email != 0) { print ", "; }
          print "<a href=\"mailto:" . urlencode(iif(strstr($emails[$email], "@"), $emails[$email], $emails[$email] . "@" . $ddetails['DomainName'])) . "\">" . htmlspecialchars($emails[$email]) . "</a>";
        }

      } elseif ($aliases[$row]['Type'] == "list") {
        print "Mailing list</td><td>";
        $listname = execute("SELECT Name FROM Lists WHERE ID='" . mysql_escape_string($aliases[$row]['Data']) . "';");
        if ($listname) {
          print htmlspecialchars($listname[0]['Name']);
        } else {
          print htmlspecialchars($aliases[$row]['Data']);
        }
      }
      print "</td></tr>\n";
    }
?>
  </table>
<?php
  } else {
    print "  <p>There are no email aliases</p>\n";
  }

  if ($_REQUEST['action'] == "editalias") {
    $alias = execute("SELECT Name, Type, Data FROM Aliases WHERE ID='" . mysql_escape_string($_REQUEST['aliasid']) . "';");
?>
  <a name=aliasform></a>
  <form action="domain.php" method="POST" id="aliasform">
   <input name="action" type="hidden" value="updatealias">
   <input name="domainid" type="hidden" value="<?php print htmlspecialchars($ddetails['ID']); ?>">
   <input name="aliasid" type="hidden" value="<?php print htmlspecialchars($_REQUEST['aliasid']); ?>">
   <table>
    <tr><td>Address name</td><td><input name="name" value="<?php print htmlspecialchars($alias[0]['Name']); ?>"></td></tr>
    <tr><td>Address type</td><td><select name="type" id="aliastype" onchange="updatealias();"><option value="account"<?php if($alias[0]['Type']=="account") { print " selected";} ?>>Mail account<option value="address"<?php if ($alias[0]['Type']=="address") { print " selected";} ?>>Email address<option value="list"<?php if($alias[0]['Type']=="list") { print " selected";} ?>>Mailing list</select></td></tr>
    <tr id="aliasaccount"><td>Account</td><td><select name="data" id="aliasaccountdata"><?php $accounts = execute("SELECT ID, Name FROM Accounts WHERE UserID=" . mysql_escape_string($_REQUEST['userid']) . " ORDER BY Name;"); for ($accountid = 0; $accountid < count($accounts); $accountid++) { print "<option value=\"" . htmlspecialchars($accounts[$accountid]['ID']) . "\"" . iif($accounts[$accountid]['ID'] == $host[0]['Data'], " selected", "") . ">" . htmlspecialchars($accounts[$accountid]['Name']); } ?></select></td></tr>
    <tr id="aliasaddress"><td>Address</td><td><input name="data" id="aliasaddressdata" value="<?php print htmlspecialchars($alias[0]['Data']); ?>"></td></tr>
    <tr id="aliaslist"><td>List</td><td><select name="data" id="aliaslistdata"><?php $lists = execute("SELECT ID, Name FROM Lists WHERE UserID=" . mysql_escape_string($_REQUEST['userid']) . " ORDER BY Name;"); for ($listid = 0; $listid < count($lists); $listid++) { print "<option value=\"" . htmlspecialchars($lists[$listid]['ID']) . "\"" . iif($lists[$listid]['ID'] == $host[0]['Data'], " selected", "") . ">" . htmlspecialchars($lists[$listid]['Name']); } ?></select></td></tr>
    <tr><td colspan=2 align=center><input type="submit" value="Update email address"></td></tr>
   </table>
<?php
  } else {
?>
  <a name=aliasform></a>
  <form action="domain.php" method="POST" id="aliasform">
   <input name="action" type="hidden" value="addalias">
   <input name="domainid" type="hidden" value="<?php print htmlspecialchars($ddetails['ID']); ?>">
   <table>
    <tr><td>Address name</td><td><input name="name"></td></tr>
    <tr><td>Address type</td><td><select name="type" id="aliastype" onchange="updatealias();"><option value="account">Mail account<option value="address" selected>Email address<option value="list">Mailing list</select></td></tr>
    <tr id="aliasaccount"><td>Account</td><td><select name="data" id="aliasaccountdata"><?php $accounts = execute("SELECT ID, Name FROM Accounts WHERE UserID=" . mysql_escape_string($_REQUEST['userid']) . " ORDER BY Name;"); for ($accountid = 0; $accountid < count($accounts); $accountid++) { print "<option value=\"" . htmlspecialchars($accounts[$accountid]['ID']) . "\">" . htmlspecialchars($accounts[$accountid]['Name']); } ?></select></td></tr>
    <tr id="aliasaddress"><td>Address</td><td><input name="data" id="aliasaddressdata" value=""></td></tr>
    <tr id="aliaslist"><td>List</td><td><select name="data" id="aliaslistdata"><?php $lists = execute("SELECT ID, Name FROM Lists WHERE UserID=" . mysql_escape_string($_REQUEST['userid']) . " ORDER BY Name;"); for ($listid = 0; $listid < count($lists); $listid++) { print "<option value=\"" . htmlspecialchars($lists[$listid]['ID']) . "\">" . htmlspecialchars($lists[$listid]['Name']); } ?></select></td></tr>
    <tr><td colspan=2 align=center><input type="submit" value="Add email address"></td></tr>
   </table>
<?php
  }
?>
   <script language="javascript" type="text/javascript">
    function updatealias() {
      current = document.getElementById("aliastype").value;
      aliasaccount.style.display = (current == "account") ? '' : 'none';
      document.getElementById("aliasaccountdata").disabled = (!(current == "account"));
      aliasaddress.style.display = (current == "address") ? '' : 'none';
      document.getElementById("aliasaddressdata").disabled = (!(current == "address"));
      aliaslist.style.display = (current == "list") ? '' : 'none';
      document.getElementById("aliaslistdata").disabled = (!(current == "list"));
    }
    updatealias();
   </script>
  </form>
<?php
} elseif ($ddetails['Mail'] == "secondary") {
  print "  <p>We are not the primary Mail server for this domain.</p>\n";
} else {
  print "  <p>Email is not enabled for this domain.</p>";
}

print "  <h2>DNS hosts</h2>";

if ($ddetails['DNS'] == "primary") {
  $hosts = execute("SELECT ID, Name, Type, Data FROM Hosts WHERE DomainID='" . mysql_escape_string($ddetails['ID']) . "' ORDER BY Name;");

  if ($hosts) {
?>
  <table>
   <tr><th>Actions</th><th>Address</th><th>Type</th><th>Destination</th></tr>
<?php
    for($row = 0; $row < count($hosts); $row++) {
      print "   <tr><td><div class=action><a href=\"?action=edithost&amp;domainid=" . urlencode($ddetails['ID']) . "&amp;hostid=" . urlencode($hosts[$row]['ID']) . "#hostform\">edit</a> <a href=\"?action=deletehost&amp;domainid=" . urlencode($ddetails['ID']) . "&amp;hostid=" . urlencode($hosts[$row]['ID']) . "\">delete</a></div></td><td>";
      if ($hosts[$row]['Name'] == "") {
        print "<a href=\"http://" . urlencode($ddetails['DomainName']) . "\" target=\"_blank\">Default</a>";
      } else {
        print "<a href=\"http://" . urlencode($hosts[$row]['Name']) . "." . urlencode($ddetails['DomainName']) . "\" target=\"_blank\">" . htmlspecialchars($hosts[$row]['Name']) . "</a>";
      }
      print "</td><td>";
      if ($hosts[$row]['Type'] == "website") {
        print "Hosted site</td><td><a href=\"website.php?websiteid=" . urlencode($hosts[$row]['Data']) . "\">";
        $webname = execute("SELECT Name FROM Websites WHERE ID='" . mysql_escape_string($hosts[$row]['Data']) . "';");
        if ($webname) {
          print htmlspecialchars($webname[0]['Name']);
        } else {
          print htmlspecialchars($hosts[$row]['Data']);
        }
        print "</a>";
      } elseif ($hosts[$row]['Type'] == "a") {
        print "IP address</td><td>" . htmlspecialchars($hosts[$row]['Data']);
      } elseif ($hosts[$row]['Type'] == "cname") {
        print "Pointer</td><td>" . htmlspecialchars($hosts[$row]['Data']);
      } elseif ($hosts[$row]['Type'] == "subdomain") {
        print "Subdomain</td><td>" . iif($hosts[$row]['Data'] != "", htmlspecialchars($hosts[$row]['Data']), "local");
      }
      print "</td></tr>\n";
    }
?>
  </table>
<?php
  } else {
    print "  <p>There are no hosts</p>\n";
  }

  if ($_REQUEST['action'] == "edithost") {
    $host = execute("SELECT Name, Type, Data FROM Hosts WHERE ID='" . mysql_escape_string($_REQUEST['hostid']) . "';");
?>
  <a name=hostform></a>
  <form action="domain.php" method="POST" id="hostform">
   <input name="action" type="hidden" value="updatehost">
   <input name="domainid" type="hidden" value="<?php print htmlspecialchars($ddetails['ID']); ?>">
   <input name="hostid" type="hidden" value="<?php print htmlspecialchars($_REQUEST['hostid']); ?>">
   <table>
    <tr><td>Host name</td><td><input name="name" value="<?php print htmlspecialchars($host[0]['Name']); ?>"></td></tr>
    <tr><td>Host type</td><td><select name="type" id="hosttype" onchange="updatehost();"><option value="website"<?php if($host[0]['Type']=="website") { print " selected";} ?>>Hosted site<option value="a"<?php if($host[0]['Type']=="a") { print " selected";} ?>>IP Address (A)<option value="cname"<?php if($host[0]['Type']=="cname") { print " selected";}?>>Pointer (CNAME)<option value="subdomain"<?php if($host[0]['Type']=="subdomain") { print " selected";}?>>Subdomain</select></td></tr>
    <tr id="hostwebsite"><td>Website</td><td><select name="data" id="hostwebsitedata"><?php $websites = execute("SELECT ID, Name FROM Websites WHERE UserID=" . mysql_escape_string($_REQUEST['userid']) . " ORDER BY Name;"); for ($websiteid = 0; $websiteid < count($websites); $websiteid++) { print "<option value=\"" . htmlspecialchars($websites[$websiteid]['ID']) . "\"" . iif($websites[$websiteid]['ID'] == $host[0]['Data'], " selected", "") . ">" . htmlspecialchars($websites[$websiteid]['Name']); } ?></select></td></tr>
    <tr id="hostaddress"><td>Address</td><td><input name="data" id="hostaddressdata" value="<?php print htmlspecialchars($host[0]['Data']); ?>"></td></tr>
    <tr><td colspan=2 align=center><input type="submit" value="Update host"></td></tr>
   </table>
<?php
  } else {
?>
  <a name=hostform></a>
  <form action="domain.php" method="POST" id="hostform">
   <input name="action" type="hidden" value="addhost">
   <input name="domainid" type="hidden" value="<?php print htmlspecialchars($ddetails['ID'] ); ?>">
   <table>
    <tr><td>Host name</td><td><input name="name" value=""></td></tr>
    <tr><td>Host type</td><td><select name="type" id="hosttype" onchange="updatehost()"><option value="website">Hosted site<option value="a">IP Address (A)<option value="cname" selected>Pointer (CNAME)<option value="subdomain">Subdomain</select></td></tr>
    <tr id="hostwebsite"><td>Website</td><td><select name="data" id="hostwebsitedata"><?php $websites = execute("SELECT ID, Name FROM Websites WHERE UserID=" . mysql_escape_string($_REQUEST['userid']) . " ORDER BY Name;"); for ($websiteid = 0; $websiteid < count($websites); $websiteid++) { print "<option value=\"" . htmlspecialchars($websites[$websiteid]['ID']) . "\">" . htmlspecialchars($websites[$websiteid]['Name']); } ?></select></td></tr>
    <tr id="hostaddress"><td>Address</td><td><input name="data" id="hostaddressdata" value=""></td></tr>
    <tr><td colspan=2 align=center><input type="submit" value="Add host"></td></tr>
   </table>
<?php
  }
?>
   <script language="jscript" type="text/javascript">
    function updatehost() {
      current = document.getElementById("hosttype").value;

      hostwebsite.style.display = (current == "website") ? '' : 'none';
      document.getElementById("hostwebsitedata").disabled = (!(current == "website"));
      hostaddress.style.display = (current != "website") ? '' : 'none';
      document.getElementById("hostaddressdata").disabled = (!(current != "website"));
    }
    updatehost();
   </script>
  </form>
<?php
} elseif ($ddetails['DNS'] == "secondary") {
  print "  <p>We are not the primary DNS server for this domain.</p>\n";
} else {
  print "  <p>DNS is not enabled for this domain.</p>";
}
print_footer()
?>
