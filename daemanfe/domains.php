<?php
require("common.inc");

checkstatus();

print_header("Hosted domains: " . htmlspecialchars($details['Name']));
?>
  <h3><a href="index.php">Home</a> - <a href="user.php?userid=<?php print urlencode($userid); ?>">Account</a> - Domains</h3>

  <h2>Domains</h2>
<?php
if (isset($action)){
  if (userisadmin($currentuserid)) {
    if ($action == "adddomain"){
      if (execute("INSERT INTO Domains (UserID, Name, DomainName, Registrar, Expiry, Enabled, DNS, DNSPrimary, DNSServerID, Mail, MailPrimary, MailServerID) VALUES ('" . mysql_escape_string($userid) . "', '" . mysql_escape_string($name) . "', '" . mysql_escape_string($domainname) . "', '" . mysql_escape_string($registrar) . "', '" . mysql_escape_string($expiry) . "', '" . mysql_escape_string($enabled) . "', '" . mysql_escape_string($dns) . "', '" . mysql_escape_string($dnsprimary) . "', '" . mysql_escape_string($dnsserverid) ."', '" . mysql_escape_string($mail) . "', '" . mysql_escape_string($mailprimary) . "', '" . mysql_escape_string($mailserverid) ."');")){
        print "  <p class=status>Domain added successfully.</p>\n";
      }else{
        print "  <p class=error>Error adding domain.</p>\n";
      }
    }elseif ($action == "updatedomain"){
      if (execute("UPDATE Domains SET DomainName='" . mysql_escape_string($domainname) . "', Registrar='" . mysql_escape_string($registrar) . "', Expiry='" . mysql_escape_string($expiry) . "', Enabled='" . mysql_escape_string($enabled) . "', DNS='" . mysql_escape_string($dns) . "', DNSPrimary='" . mysql_escape_string($dnsprimary) . "', DNSServerID='" . mysql_escape_string($dnsserverid) . "', Mail='" . mysql_escape_string($mail) . "', MailPrimary='" . mysql_escape_string($mailprimary) . "', MailServerID='" . mysql_escape_string($mailserverid) . "' WHERE ID='" . mysql_escape_string($domainid) . "';")){
        print "  <p class=status>Domain updated successfully.</p>\n";
      } else {
        print "  <p class=error>Error updating domain.".$DBError."</p>\n";
      }
    }elseif ($action == "deletedomain"){
      if (execute("DELETE FROM Domains WHERE ID='" . mysql_escape_string($domainid) . "';")){
        if (execute("DELETE FROM Hosts WHERE DomainID='" . mysql_escape_string($domainid) . "';") && execute("DELETE FROM Aliases WHERE DomainID='" . mysql_escape_string($domainid) . "';")){
          print "  <p class=status>Domain deleted successfully.</p>\n";
        }else{
          print "  <p class=error>Error deleting domain hosts/aliases.</p>\n";
        }
      } else {
        print "  <p class=error>Error deleting domain.</p>\n";
      }
    }
  }
}

$domains = execute("SELECT ID, Name, DomainName, Expiry, Enabled, DNS, DNSPrimary, Mail, MailPrimary FROM Domains WHERE UserID='" . mysql_escape_string($userid) . "' ORDER BY DomainName;"); if ($domains) {
?>
  <table backcolour=red>
   <tr><?php if (userisadmin($currentuserid)) { print "<th>Actions</th>"; } ?><th>Name</th><th>Domain name</th><th>Expiry</th><th>Enabled</th><th>DNS</th><th>Email</th></tr>
<?php
  for($row = 0; $row < count($domains); $row++){
    if (userisadmin($currentuserid)) {
      print "   <tr><td><div class=action><a href=\"?action=editdomain&amp;userid=" . urlencode($userid) . "&amp;domainid=" . urlencode($domains[$row]['ID']) . "#domainform\">edit</a> <a href=\"?action=deletedomain&amp;userid=" . urlencode($userid) . "&amp;domainid=" . urlencode($domains[$row]['ID']) . "\">delete</a></div></td>";
    } else {
      print "   <tr>";
    }
    print "<td>" . htmlspecialchars($domains[$row]['Name']) . "</td><td><a href=\"domain.php?domainid=" . urlencode($domains[$row]['ID']) . "\">" . htmlspecialchars($domains[$row]['DomainName']) . "</a></td><td>" . htmlspecialchars($domains[$row]['Expiry']) . "</td><td>" . iif($domains[$row]['Enabled'], "Yes", "No") . "</td><td>" . iif($domains[$row]['DNS'] == "primary", "Primary", iif($domains[$row]['DNS'] == "secondary", "Secondary to " . htmlspecialchars($domains[$row]['DNSPrimary']), "None")) . "</td><td>" . iif($domains[$row]['Mail'] == "primary", "Primary", iif($domains[$row]['Mail'] == "secondary", "Secondary" . iif($domains[$row]['MailPrimary'], " to " . htmlspecialchars($domains[$row]['MailPrimary']), ""), "None")) . "</td></tr>\n";
  }
?>
  </table>
<?php
} else {
  print "  <p>There are no domains currently set up. Please contact Earl Software to add a new domain.</p>\n";
}

if (userisadmin($currentuserid)) {
  if ($action == "editdomain"){
    $domain = execute("SELECT DomainName, Registrar, Expiry, Enabled, DNS, DNSPrimary, DNSServerID, Mail, MailPrimary, MailServerID FROM Domains WHERE ID='" . mysql_escape_string($domainid) . "';");
?>
  <a name=domainform>
  <form action="domains.php" method="POST">
   <input name="action" type="hidden" value="updatedomain">
   <input name="userid" type="hidden" value="<?php print htmlspecialchars($userid); ?>">
   <input name="domainid" type="hidden" value="<?php print htmlspecialchars($domainid); ?>">
   <table>
    <tr><td>Domain name</td><td><input name="domainname" value="<?php print htmlspecialchars($domain[0]['DomainName']); ?>"> <a href="help.php#domaindomainname">?</a></td></tr>
    <tr><td>Registrar</td><td><input name="registrar" value="<?php print htmlspecialchars($domain[0]['Registrar']); ?>"> <a href="help.php#domainregistrar">?</a></td></tr>
    <tr><td>Expiry</td><td><input name="expiry" value="<?php print htmlspecialchars($domain[0]['Expiry']); ?>"> <a href="help.php#domainexpiry">?</a></td></tr>
    <tr><td>Enabled</td><td><input type="checkbox" name="enabled" value=1<?php if ($domain[0]['Enabled']) { print " checked"; } ?>> <a href="../help.php#domainenabled">?</a></td></tr>
    <tr><td>DNS</td><td><select name="dns"><option value="primary"<?php if($domain[0]['DNS']=="primary"){ print " selected";} ?>>Primary<option value="secondary"<?php if($domain[0]['DNS']=="secondary"){ print " selected";} ?>>Secondary<option value="none"<?php if ($domain[0]['DNS']=="none"){ print " selected";} ?>>None</select> <a href="help.php#domaindns"></td></tr>
    <tr><td>Primary DNS server</td><td><input name="dnsprimary" value="<?php print htmlspecialchars($domain[0]['DNSPrimary']); ?>"> <a href="help.php#domaindnsprimary">?</a></td></tr>
    <tr><td>DNS Server</td><td><select name="dnsserverid"><?php $servers = execute("SELECT ID, Name FROM Servers WHERE DNS=1;"); for ($serverid = 0; $serverid < count($servers); $serverid++) { print "<option value=\"" . htmlspecialchars($servers[$serverid]['ID']) . "\"" . iif($domain[0]['DNSServerID'] == $servers[$serverid]['ID'], " selected", "") . ">" . htmlspecialchars($servers[$serverid]['Name']); } ?></select> <a href="help.php#domaindnsserverid">?</a></td></tr>
    <tr><td>Mail</td><td><select name="mail"><option value="primary"<?php if($domain[0]['Mail']=="primary"){ print " selected";} ?>>Primary<option value="secondary"<?php if($domain[0]['Mail']=="secondary"){ print " selected";} ?>>Secondary<option value="none"<?php if ($domain[0]['Mail']=="none"){ print " selected";} ?>>None</select> <a href="help.php#domainmail"></td></tr>
    <tr><td>Primary mail server</td><td><input name="mailprimary" value="<?php print htmlspecialchars($domain[0]['MailPrimary']); ?>"> <a href="help.php#domainmailprimary">?</a></td></tr>
    <tr><td>Mail Server</td><td><select name="mailserverid"><?php $servers = execute("SELECT ID, Name FROM Servers WHERE Mail=1;"); for ($serverid = 0; $serverid < count($servers); $serverid++) { print "<option value=\"" . htmlspecialchars($servers[$serverid]['ID']) . "\"" . iif($domain[0]['MailServerID'] == $servers[$serverid]['ID'], " selected", "") . ">" . htmlspecialchars($servers[$serverid]['Name']); } ?></select> <a href="help.php#domainmailserverid">?</a></td></tr>
    <tr><td colspan=2 align=center><input type="submit" value="Update domain"></td></tr>
   </table>
  </form>
<?php
  }else{
?>
  <a name=domainform>
  <form action="domains.php" method="POST">
   <input name="action" type="hidden" value="adddomain">
   <input name="userid" type="hidden" value="<?php print htmlspecialchars($userid); ?>">
   <table>
    <tr><td>Name</td><td><input name="name"> <a href="help.php#domainname">?</a></td></tr>
    <tr><td>Domain name</td><td><input name="domainname"> <a href="help.php#domaindomainname">?</a></td></tr>
    <tr><td>Registrar</td><td><input name="registrar"> <a href="help.php#domainregistrar">?</a></td></tr>
    <tr><td>Expiry</td><td><input name="expiry"> <a href="help.php#domainexpiry">?</a></td></tr>
    <tr><td>Enabled</td><td><input type="checkbox" name="enabled" value=1 checked> <a href="../help.php#domainenabled">?</a></td></tr>
    <tr><td>DNS</td><td><select name="dns"><option value="primary" selected>Primary<option value="secondary">Secondary<option value="none">None</select> <a href="help.php#domaindns"></td></tr>
    <tr><td>Primary DNS server</td><td><input name="dnsprimary"> <a href="help.php#domaindnsprimary">?</a></td></tr>
    <tr><td>DNS Server</td><td><select name="dnsserverid"><?php $servers = execute("SELECT ID, Name FROM Servers WHERE DNS=1;"); for ($serverid = 0; $serverid < count($servers); $serverid++) { print "<option value=\"" . htmlspecialchars($servers[$serverid]['ID']) . "\">" . htmlspecialchars($servers[$serverid]['Name']); } ?></select> <a href="help.php#domaindnsserverid">?</a></td></tr>
    <tr><td>Mail</td><td><select name="mail"><option value="primary" selected>Primary<option value="secondary">Secondary<option value="none">None</select> <a href="help.php#domainmail"></td></tr>
    <tr><td>Primary mail server</td><td><input name="mailprimary"> <a href="help.php#domainmailprimary">?</a></td></tr>
    <tr><td>Mail Server</td><td><select name="mailserverid"><?php $servers = execute("SELECT ID, Name FROM Servers WHERE Mail=1;"); for ($serverid = 0; $serverid < count($servers); $serverid++) { print "<option value=\"" . htmlspecialchars($servers[$serverid]['ID']) . "\">" . htmlspecialchars($servers[$serverid]['Name']); } ?></select> <a href="help.php#domainmailserverid">?</a></td></tr>
    <tr><td colspan=2 align=center><input type="submit" value="Add domain"></td></tr>
   </table>
  </form>
<?php
  }
}

print_footer();
?>
