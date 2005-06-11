<?php
require("common.inc");

checkstatus();

print_header("Hosted domains: " . htmlspecialchars($details['RealName']));
?>
  <h3><a href="index.php">Home</a> - <a href="user.php?userid=<?php print urlencode($details['ID']); ?>">Account</a> - Domains</h3>

  <h2>Domains</h2>
<?php
$domains = execute("SELECT ID, Name, DomainName, Enabled, DNS, DNSPrimary, Mail, MailPrimary FROM Domains WHERE UserID='" . mysql_escape_string($details['ID']) . "' ORDER BY DomainName;");
if ($domains) {
?>
  <table>
   <tr><th>Actions</th><th>Name</th><th>Domain name</th><th>Enabled</th><th>DNS</th><th>Email</th></tr>
<?php
  for($row = 0; $row < count($domains); $row++) {
    print "   <tr><td><div class=action><a href=\"domain.php?domainid=" . urlencode($domains[$row]['ID']) . "#domainform\">edit</a>" . iif(($_SESSION['userisadmin']), " <a href=\"?action=deletedomain&amp;userid=" . urlencode($details['ID']) . "&amp;domainid=" . urlencode($domains[$row]['ID']) . "\">delete</a>", "") . "</div></td><td>" . htmlspecialchars($domains[$row]['Name']) . "</td><td><a href=\"domain.php?domainid=" . urlencode($domains[$row]['ID']) . "\">" . htmlspecialchars($domains[$row]['DomainName']) . "</a></td><td>" . iif($domains[$row]['Enabled'], "Yes", "No") . "</td><td>" . iif($domains[$row]['DNS'] == "primary", "Primary", iif($domains[$row]['DNS'] == "secondary", "Secondary to " . htmlspecialchars($domains[$row]['DNSPrimary']), "None")) . "</td><td>" . iif($domains[$row]['Mail'] == "primary", "Primary", iif($domains[$row]['Mail'] == "secondary", "Secondary" . iif($domains[$row]['MailPrimary'], " to " . htmlspecialchars($domains[$row]['MailPrimary']), ""), "None")) . "</td></tr>\n";
  }
?>
  </table>
<?php
} else {
  print "  <p>There are no domains currently set up. Please contact Earl Software to add a new domain.</p>\n";
}
if ($_SESSION['userisadmin']) {
  print "  <p>Add a <a href=\"domain.php?userid=" . urlencode($details['ID']) . "#domainform\">new domain</a>\n";
}

print_footer();
?>
