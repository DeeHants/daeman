<?php
require("common.inc");

$detailsw = websitedetails($websiteid, $websitename);
if (!$detailsw){
  checkstatus();
  print "  <p>This is not a valid website</p>\n";
  exit;
}
checkstatus();
print_header("Hosted website: " . htmlspecialchars($websitename));
?>
  <h3><a href="index.php">Home</a> - <a href="user.php?userid=<?php print urlencode($userid); ?>">Account</a> - <a href="websites.php?userid=<?php print urlencode($userid); ?>">Websites</a> - <?php print htmlspecialchars($websitename); ?></h3>
<?php

if (isset($action)){
  if ($action == "addhost"){
    if (execute("INSERT INTO WebsiteHosts (WebsiteID, Host) VALUES ('" . mysql_escape_string($websiteid) . "', '" . mysql_escape_string($host) . "');")){
      print "  <p class=status>Host added successfully.</p>\n";
    }else{
      print "  <p class=error>Error adding host.</p>\n";
    }
  }elseif ($action == "updatehost"){
    if (execute("UPDATE WebsiteHosts SET Host='" . mysql_escape_string($host) . "' WHERE ID='" . mysql_escape_string($hostid) . "';")){
      print "  <p class=status>Host updated successfully.</p>\n";
    }else{
      print "  <p class=error>Error updating Host.</p>\n";
    }
  }elseif ($action == "deletehost"){
    if (execute("DELETE FROM WebsiteHosts WHERE ID='" . mysql_escape_string($hostid) . "';")){
      print "  <p class=status>Host deleted successfully.</p>\n";
    }else{
      print "  <p class=error>Error deleting host.</p>\n";
    }
  }
}

print "  <h2>Hostnames</h2>\n";
$hosts = execute("SELECT ID, Host FROM WebsiteHosts WHERE WebsiteID='" . mysql_escape_string($websiteid) . "' ORDER BY Host;");
$fhosts = execute("SELECT Hosts.ID, Hosts.Name, DomainName FROM Hosts LEFT JOIN Domains ON Domains.ID=Hosts.DomainID WHERE Type='website' AND Data='" . mysql_escape_string($websiteid) . "' ORDER BY Hosts.Name;");

if ($hosts or $fhosts){
?>
  <table backcolour=red>
   <tr><th>Actions</th><th>Hostname</th></tr>
<?php
  for($row = 0; $row < count($hosts); $row++){
    print "   <tr><td><div class=action><a href=\"?action=edithost&amp;websiteid=" . urlencode($websiteid) . "&amp;hostid=" . urlencode($hosts[$row]['ID']) . "#hostform\">edit</a> <a href=\"?action=deletehost&amp;websiteid=" . urlencode($websiteid) . "&amp;hostid=" . urlencode($hosts[$row]['ID']) . "\">delete</a></div></td><td><a href=\"http://" . urlencode($hosts[$row]['Host']) . "\" target=\"_blank\">" . htmlspecialchars($hosts[$row]['Host']) . "</a></td></tr>\n";
  }
  for($row = 0; $row < count($fhosts); $row++){
    print "   <tr><td><div class=action>preconfigured<!-- <a href=\"?action=deletealias&amp;domainid=" . urlencode($domainid) . "&amp;aliasid=" . urlencode($fhosts[$row]['ID']) . "\">delete</a>--></div></td><td>";
    if ($fhosts[$row]['Name'] == ""){
      print "<a href=\"http://" . urlencode($fhosts[$row]['DomainName']) . "\" target=\"_blank\">" . htmlspecialchars($fhosts[$row]['DomainName']) ."</a>";
    }else{
      print "<a href=\"http://" . urlencode($fhosts[$row]['Name']) . "." . urlencode($fhosts[$row]['DomainName']) . "\" target=\"_blank\">" . htmlspecialchars($fhosts[$row]['Name']) . "." . htmlspecialchars($fhosts[$row]['DomainName']) . "</a>";
    }
    print "</td></tr>\n";
  }
?>
  </table>
<?php
}else{
  print "  <p>There are no hosts configured for this website.</p>\n";
}

if ($action == "edithost"){
  $host = execute("SELECT Host FROM WebsiteHosts WHERE ID='" . mysql_escape_string($hostid) . "';");
?>
  <a name=hostform>
  <form action="website.php" method="POST">
   <input name="action" type="hidden" value="updatehost">
   <input name="websiteid" type="hidden" value="<?php print htmlspecialchars($websiteid); ?>">
   <input name="hostid" type="hidden" value="<?php print htmlspecialchars($hostid); ?>">
   <table>
    <tr><td>Host name</td><td><input name="host" value="<?php print htmlspecialchars($host[0]['Host']); ?>"> <a href="help.php#hosthost">?</a></td></tr>
    <tr><td colspan=2 align=center><input type="submit" value="Update host"></td></tr>
   </table>
  </form>
<?php
}else{
?>
  <a name=hostform>
  <form action="website.php" method="POST">
   <input name="action" type="hidden" value="addhost">
   <input name="websiteid" type="hidden" value="<?php print htmlspecialchars($websiteid); ?>">
   <table>
    <tr><td>Host name</td><td><input name="host"> <a href="help.php#hosthost">?</a></td></tr>
    <tr><td colspan=2 align=center><input type="submit" value="Add host"></td></tr>
   </table>
  </form>
<?php
}

print "  <h2>Tools</h2>\n";
if ($detailsw['Trial']) {
  print "  <p>The development site is available at <a href=\"http://" . urlencode($detailsw['Name']) . ".trial.earlsoft.co.uk/\" target=\"_blank\">" . urlencode($detailsw['Name']) . ".trial.earlsoft.co.uk</a>\n";
}
if ($detailsw['Logging']) {
  print "  <p>We use analog to recreate the logs every 6 hours (6:00 and 12:00). <a href=\"http://logs.earlsoft.co.uk/" . urlencode($detailsw['Name']) . ".htm\">View logs</a>\n";
  print "  <p>You can also access the log fires directly from the HTTP server at <a href=\"ftp://" . urlencode($details['Name']) . "@jerry.earlsoft.co.uk/websites/" . urlencode($detailsw['Name']) . "_log\">ftp://" . urlencode($details['Name']) . "@jerry.earlsoft.co.uk/websites/" . urlencode($detailsw['Name']) . "_log</a>.\n";
}
print "  <p>You can upload your website using FTP. Here is a link to your website directory upload area. <a href=\"ftp://" . urlencode($details['Name']) . "@jerry.earlsoft.co.uk/websites/" . urlencode($detailsw['Name']) . "\">Enter upload area</a>.\n";

print_footer()
?>
