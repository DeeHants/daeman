<?php
require("common.inc");

checkstatus();

if (isset($_REQUEST['domainid'])) {
  $ddetails = domaindetails($_REQUEST['domainid'], $_REQUEST['domainname']);
  if (!$ddetails) {
    checkstatus();
    print "This is not a valid domain<br>\n";
    exit;
  }
}

print_header("Hosted domain: " . htmlspecialchars(iif(isset($_REQUEST['domainid']), $ddetails['DomainName'], "New domain")));
?>
  <h3><a href="index.php">Home</a> - <a href="user.php?userid=<?php print urlencode($details['ID']); ?>">Account</a> - <a href="domains.php?userid=<?php print urlencode($details['ID']); ?>">Domains</a> - <?php print htmlspecialchars(iif(isset($_REQUEST['domainid']), $ddetails['DomainName'], "New domain")); ?></h3>
<?php

switch ($_REQUEST['action']) {
case "addalias":
  if (execute("INSERT INTO Aliases (DomainID, Name, Type, Data) VALUES ('" . mysql_escape_string($ddetails['ID']) . "', '" . mysql_escape_string($_REQUEST['name']) . "', '" . mysql_escape_string($_REQUEST['type']) . "', '" . mysql_escape_string($_REQUEST['data']) . "');")) {
    print "  <p class=status>Email alias added successfully.</p>\n";
  } else {
    print "  <p class=error>Error adding email alias.</p>\n";
  }
  break;

case "updatealias":
  if (execute("UPDATE Aliases SET Name='" . mysql_escape_string($_REQUEST['name']) . "', Type='" . mysql_escape_string($_REQUEST['type']) . "', Data='" . mysql_escape_string($_REQUEST['data']) . "' WHERE ID='" . mysql_escape_string($_REQUEST['aliasid']) . "';")) {
    print "  <p class=status>Email alias updated successfully.</p>\n";
  } else {
    print "  <p class=error>Error updating email alias.</p>\n";
  }
  break;

case "deletealias":
  if (execute("DELETE FROM Aliases WHERE ID='" . mysql_escape_string($_REQUEST['aliasid']) . "';")) {
    print "  <p class=status>Email alias deleted successfully.</p>\n";
  } else {
    print "  <p class=error>Error deleting email alias.</p>\n";
  }
  break;

case "addhost":
  if (execute("INSERT INTO Hosts (DomainID, Name, Type, Data, `Group`) VALUES ('" . mysql_escape_string($ddetails['ID']) . "', '" . mysql_escape_string($_REQUEST['name']) . "', '" . mysql_escape_string($_REQUEST['type']) . "', '" . mysql_escape_string($_REQUEST['data']) . "', '" . mysql_escape_string($_REQUEST['group']) . "');")) {
    print "  <p class=status>Host added successfully.</p>\n";
  } else {
    print "  <p class=error>Error adding host.</p>\n";
  }
  break;

case "updatehost":
  if (execute("UPDATE Hosts SET Name='" . mysql_escape_string($_REQUEST['name']) . "', Type='" . mysql_escape_string($_REQUEST['type']) . "', Data='" . mysql_escape_string($_REQUEST['data']) . "', `Group`='" . mysql_escape_string($_REQUEST['group']) . "' WHERE ID='" . mysql_escape_string($_REQUEST['hostid']) . "';")) {
    print "  <p class=status>Host updated successfully.</p>\n";
  } else {
    print "  <p class=error>Error updating Host.</p>\n";
  }
  break;

case "deletehost":
  if (execute("DELETE FROM Hosts WHERE ID='" . mysql_escape_string($_REQUEST['hostid']) . "';")) {
    print "  <p class=status>Host deleted successfully.</p>\n";
  } else {
    print "  <p class=error>Error deleting host.</p>\n";
  }
  break;

case "adddomain":
  if ($_SESSION['userisadmin']) {
    if (execute("INSERT INTO Domains (UserID, Name, DomainName, Registrar, Expiry, Enabled, DNS, DNSPrimary, DNSServerID, Mail, MailPrimary, MailServerID) VALUES ('" . mysql_escape_string($details['ID']) . "', '" . mysql_escape_string($_REQUEST['name']) . "', '" . mysql_escape_string($_REQUEST['domainname']) . "', '" . mysql_escape_string($_REQUEST['registrar']) . "', '" . mysql_escape_string($_REQUEST['expiry']) . "', '" . mysql_escape_string($_REQUEST['enabled']) . "', '" . mysql_escape_string($_REQUEST['dns']) . "', '" . mysql_escape_string($_REQUEST['dnsprimary']) . "', '" . mysql_escape_string($_REQUEST['dnsserverid']) ."', '" . mysql_escape_string($_REQUEST['mail']) . "', '" . mysql_escape_string($_REQUEST['mailprimary']) . "', '" . mysql_escape_string($_REQUEST['mailserverid']) ."');")) {
      print "  <p class=status>Domain added successfully.</p>\n";
    } else {
      print "  <p class=error>Error adding domain.</p>\n";
    }
  }
  break;

case "updatedomain":
  if ($_SESSION['userisadmin']) {
    if (execute("UPDATE Domains SET DomainName='" . mysql_escape_string($_REQUEST['domainname']) . "', Registrar='" . mysql_escape_string($_REQUEST['registrar']) . "', Expiry='" . mysql_escape_string($_REQUEST['expiry']) . "', Enabled='" . mysql_escape_string($_REQUEST['enabled']) . "', DNS='" . mysql_escape_string($_REQUEST['dns']) . "', DNSPrimary='" . mysql_escape_string($_REQUEST['dnsprimary']) . "', DNSServerID='" . mysql_escape_string($_REQUEST['dnsserverid']) . "', Mail='" . mysql_escape_string($_REQUEST['mail']) . "', MailPrimary='" . mysql_escape_string($_REQUEST['mailprimary']) . "', MailServerID='" . mysql_escape_string($_REQUEST['mailserverid']) . "' WHERE ID='" . mysql_escape_string($_REQUEST['domainid']) . "';")) {
      print "  <p class=status>Domain updated successfully.</p>\n";
    } else {
      print "  <p class=error>Error updating domain.".$DBError."</p>\n";
    }
  }
  break;

case "deletedomain":
  if ($_SESSION['userisadmin']) {
    if (execute("DELETE FROM Domains WHERE ID='" . mysql_escape_string($_REQUEST['domainid']) . "';")) {
      if (execute("DELETE FROM Hosts WHERE DomainID='" . mysql_escape_string($_REQUEST['domainid']) . "';") && execute("DELETE FROM Aliases WHERE DomainID='" . mysql_escape_string($_REQUEST['domainid']) . "';")) {
        print "  <p class=status>Domain deleted successfully.</p>\n";
      } else {
        print "  <p class=error>Error deleting domain hosts/aliases.</p>\n";
      }
    } else {
      print "  <p class=error>Error deleting domain.</p>\n";
    }
  }
  break;
}

if ($_SESSION['userisadmin']) {
?>
  <h2>Domain</h2>
<?php
  $fill = array();
  if (isset($_REQUEST['domainid'])) {
    $domain = execute("SELECT Name, DomainName, Registrar, Expiry, Enabled, DNS, DNSPrimary, DNSServerID, Mail, MailPrimary, MailServerID FROM Domains WHERE ID='" . mysql_escape_string($_REQUEST['domainid']) . "';");
    $fill['id'] = $_REQUEST['domainid'];
    $fill['name'] = $domain[0]['Name'];
    $fill['domainname'] = $domain[0]['DomainName'];
    $fill['registrar'] = $domain[0]['Registrar'];
    $fill['expiry'] = $domain[0]['Expiry'];
    $fill['enabled'] = $domain[0]['Enabled'];
    $fill['dns'] = $domain[0]['DNS'];
    $fill['dnsprimary'] = $domain[0]['DNSPrimary'];
    $fill['dnsserverid'] = $domain[0]['DNSServerID'];
    $fill['mail'] = $domain[0]['Mail'];
    $fill['mailprimary'] = $domain[0]['MailPrimary'];
    $fill['mailserverid'] = $domain[0]['MailServerID'];
  }
?>
  <a name=domainform></a>
  <form action="domain.php" method="POST" id="domainform">
   <input name="action" type="hidden" value="<?php print iif(isset($fill['id']), "update", "add"); ?>domain">
   <input name="userid" type="hidden" value="<?php print htmlspecialchars($details['ID']); ?>">
<?php if (isset($fill['id'])) { ?>
   <input name="domainid" type="hidden" value="<?php print htmlspecialchars($fill['id']); ?>">
<?php } ?>
   <table>
<?php if (isset($fill['id'])) { ?>
    <tr><td>Name</td><td><?php print htmlspecialchars($fill['name']); ?></td></tr>
<?php } else { ?>
    <tr><td>Name</td><td><input name="name" value="<?php print htmlspecialchars($fill['name']); ?>" onclick="updateform()"></td></tr>
<?php } ?>
    <tr><td>Domain name</td><td><input name="domainname" value="<?php print htmlspecialchars($fill['domainname']); ?>" onclick="updateform()"></td></tr>
    <tr><td>Registrar</td><td><input name="registrar" value="<?php print htmlspecialchars($fill['registrar']); ?>" onclick="updateform()"></td></tr>
    <tr><td>Expiry</td><td><input name="expiry" value="<?php print htmlspecialchars($fill['expiry']); ?>" onclick="updateform()"></td></tr>
    <tr><td>Enabled</td><td><input type="checkbox" name="enabled" value=1<?php if ($fill['enabled']) { print " checked"; } ?> onclick="updateform()"></td></tr>
    <tr><td>DNS</td><td><select name="dns" onclick="updateform()"><?php print optionlist(array(array("primary", "Primary"), array("secondary", "Secondary"), array("none", "None")), $fill['dns']); ?></select></td></tr>
    <tr><td>Primary DNS server</td><td><input name="dnsprimary" value="<?php print htmlspecialchars($fill['dnsprimary']); ?>" onclick="updateform()"></td></tr>
    <tr><td>DNS Server</td><td><select name="dnsserverid" onclick="updateform()"><?php print optionlist(execute("SELECT ID, Name FROM Servers WHERE DNS=1 ORDER BY Name;"), $fill['dnsserverid']); ?></select></td></tr>
    <tr><td>Mail</td><td><select name="mail" onclick="updateform()"><?php print optionlist(array(array("primary", "Primary"), array("secondary", "Secondary"), array("none", "None")), $fill['mail']); ?></select></td></tr>
    <tr><td>Primary mail server</td><td><input name="mailprimary" value="<?php print htmlspecialchars($fill['mailprimary']); ?>" onclick="updateform()"></td></tr>
    <tr><td>Mail Server</td><td><select name="mailserverid" onclick="updateform()"><?php print optionlist(execute("SELECT ID, Name FROM Servers WHERE Mail=1 ORDER BY Name;"), $fill['mailserverid']); ?></select></td></tr>
    <tr><td colspan=2 align=center><input type="submit" value="<?php print iif(isset($fill['id']), "Update", "Add"); ?> domain"></td></tr>
   </table>
  </form>
  <script language=javascript type="text/javascript">
function updateform() {
  domainform.dns.disabled = !domainform.enabled.checked;
  domainform.dnsprimary.visible = !(!domainform.enabled.checked || domainform.dns.value == "primary");
  domainform.dnsserverid.disabled = !domainform.enabled.checked || domainform.dns.value == "none";
  domainform.mail.disabled = !domainform.enabled.checked;
  domainform.mailprimary.disabled = !domainform.enabled.checked || domainform.mail.value == "primary";
  domainform.mailserverid.disabled = !domainform.enabled.checked || domainform.mail.value == "none";
}
updateform();
  </script>
<?php
}

if (isset($_REQUEST['domainid'])) {
?>
  <h2>Email aliases</h2>
<?php
  if ($ddetails['Mail'] == "primary") {
    $aliases = execute("SELECT ID, Name, Type, Data FROM Aliases WHERE DomainID='" . mysql_escape_string($ddetails['ID']) . "' ORDER BY Name;");

?>
  <table>
   <tr><th>Actions</th><th>Address</th><th>Type</th><th>Destination</th></tr>
<?php
    $hasdefault = false;
    for($row = 0; $row < count($aliases); $row++) {
      print "   <tr><td><div class=action><a href=\"?action=editalias&amp;domainid=" . urlencode($ddetails['ID']) . "&amp;aliasid=" . urlencode($aliases[$row]['ID']) . "#aliasform\">edit</a> <a href=\"?action=deletealias&amp;domainid=" . urlencode($ddetails['ID']) . "&amp;aliasid=" . urlencode($aliases[$row]['ID']) . "\">delete</a></div></td><td>";
      if ($aliases[$row]['Name'] == "") {
        print "Default";
        $hasdefault = true;
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

      } elseif ($aliases[$row]['Type'] == "reject") {
        print "Rejected</td><td>";

      }
      print "</td></tr>\n";
    }

    if (!($hasdefault)) {
      print "   <tr><td><div class=action>preconfigured</div></td><td>Default</td><td>Mail account</td><td>Master mailbox</td></tr>\n";
    }
?>
  </table>
<?php

    $fill = array();
    if ($_REQUEST['action'] == "editalias") {
      $alias = execute("SELECT Name, Type, Data FROM Aliases WHERE ID='" . mysql_escape_string($_REQUEST['aliasid']) . "';");
      $fill['id'] = $_REQUEST['aliasid'];
      $fill['name'] = $alias[0]['Name'];
      $fill['type'] = $alias[0]['Type'];
      $fill['data'] = $alias[0]['Data'];
    }
?>
  <a name=aliasform></a>
  <form action="domain.php" method="POST" id="aliasform">
   <input name="action" type="hidden" value="<?php print iif(isset($fill['id']), "update", "add"); ?>alias">
   <input name="domainid" type="hidden" value="<?php print htmlspecialchars($ddetails['ID']); ?>">
<?php if (isset($fill['id'])) { ?>
   <input name="aliasid" type="hidden" value="<?php print htmlspecialchars($fill['id']); ?>">
<?php } ?>
   <table>
    <tr><td>Address name</td><td><input name="name" value="<?php print htmlspecialchars($fill['name']); ?>"></td></tr>
    <tr><td>Address type</td><td><select name="type" id="aliastype" onchange="updatealias();"><?php print optionlist(array(array("account", "Mail account"), array("address", "Email address"), array("list", "Mailing list"), array("sms", "SMS"), array("reject", "Reject")), $fill['type']); ?></select></td></tr>
    <tr id="aliasaccount"><td>Account</td><td><select name="data" id="aliasaccountdata"><?php print optionlist(execute("SELECT ID, Name FROM Accounts WHERE UserID=" . mysql_escape_string($_REQUEST['userid']) . " ORDER BY Name;"), $fill['data']); ?></select></td></tr>
    <tr id="aliasaddress"><td>Address</td><td><input name="data" id="aliasaddressdata" value="<?php print htmlspecialchars($fill['data']); ?>"></td></tr>
    <tr id="aliaslist"><td>List</td><td><select name="data" id="aliaslistdata"><?php print optionlist(execute("SELECT ID, Name FROM Lists WHERE UserID=" . mysql_escape_string($_REQUEST['userid']) . " ORDER BY Name;"), $fill['data']); ?></select></td></tr>
    <tr><td colspan=2 align=center><input type="submit" value="<?php print iif(isset($fill['id']), "Update", "Add"); ?> email alias"></td></tr>
   </table>
   <script language="javascript" type="text/javascript">
    function updatealias() {
      current = document.getElementById("aliastype").value;
      document.getElementById("aliasaccount").style.display = (current == "account") ? '' : 'none';
      document.getElementById("aliasaccountdata").disabled = (!(current == "account"));
      document.getElementById("aliasaddress").style.display = (current == "address") ? '' : 'none';
      document.getElementById("aliasaddressdata").disabled = (!(current == "address"));
      document.getElementById("aliaslist").style.display = (current == "list") ? '' : 'none';
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
    $hosts = execute("SELECT ID, Name, Type, Data, `Group` FROM Hosts WHERE DomainID='" . mysql_escape_string($ddetails['ID']) . "' ORDER BY `Group`, Name;");

    if ($hosts) {
?>
  <table>
   <tr><th>Actions</th><th>Address</th><th>Type</th><th>Destination</th></tr>
<?php
      $lastgroup = "";
      for($row = 0; $row < count($hosts); $row++) {
        if ($hosts[$row]['Group'] != $lastgroup) {
          print "   <tr><td></td><td colspan=3>" . htmlspecialchars($hosts[$row]['Group']) . "</td></tr>\n";
          $lastgroup = $hosts[$row]['Group'];
        }
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

    $fill = array();
    if ($_REQUEST['action'] == "edithost") {
      $host = execute("SELECT Name, Type, Data, `Group` FROM Hosts WHERE ID='" . mysql_escape_string($_REQUEST['hostid']) . "';");
      $fill['id'] = $_REQUEST['hostid'];
      $fill['name'] = $host[0]['Name'];
      $fill['type'] = $host[0]['Type'];
      $fill['data'] = $host[0]['Data'];
      $fill['group'] = $host[0]['Group'];
    }
?>
  <a name=hostform></a>
  <form action="domain.php" method="POST" id="hostform">
   <input name="action" type="hidden" value="<?php print iif(isset($fill['id']), "updatehost", "addhost"); ?>">
   <input name="domainid" type="hidden" value="<?php print htmlspecialchars($ddetails['ID']); ?>">
<?php if (isset($fill['id'])) { ?>
   <input name="hostid" type="hidden" value="<?php print htmlspecialchars($fill['id']); ?>">
<?php } ?>
   <table>
    <tr><td>Host name</td><td><input name="name" value="<?php print htmlspecialchars($fill['name']); ?>"></td></tr>
    <tr><td>Host type</td><td><select name="type" id="hosttype" onchange="updatehost();"><?php print optionlist(array(array("website", "Hosted site"), array("a", "IP Address (A)"), array("cname", "Pointer (CNAME)"), array("subdomain", "Subdomain")), $fill['type']); ?></select></td></tr>
    <tr id="hostwebsite"><td>Website</td><td><select name="data" id="hostwebsitedata"><?php print optionlist(execute("SELECT ID, Name FROM Websites WHERE UserID=" . mysql_escape_string($_REQUEST['userid']) . " ORDER BY Name;"), $fill['data']); ?></select></td></tr>
    <tr id="hostaddress"><td>Address</td><td><input name="data" id="hostaddressdata" value="<?php print htmlspecialchars($fill['data']); ?>"></td></tr>
    <tr id="hostgroup"><td>Group</td><td><input name="group" id="hostgroupdata" value="<?php print htmlspecialchars($fill['group']); ?>"></td></tr>
    <tr><td colspan=2 align=center><input type="submit" value="<?php print iif(isset($fill['id']), "Update", "Add"); ?> host"></td></tr>
   </table>
   <script language="jscript" type="text/javascript">
    function updatehost() {
      current = document.getElementById("hosttype").value;

      document.getElementById("hostwebsite").style.display = (current == "website") ? '' : 'none';
      document.getElementById("hostwebsitedata").disabled = (!(current == "website"));
      document.getElementById("hostaddress").style.display = (current != "website") ? '' : 'none';
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
}
print_footer()
?>
