<?php
require("../common.inc");

// Redirect back to te index page if they aren't logged in
if (!$loggedin){header("location: ../index.php"); exit();}

// If the logged in user isn't an admin, redirect back to the index
if (!(userisadmin($currentuserid))){header("location: ../index.php"); exit();}

print_header("Summary");
print "  <h3><a href=\"../index.php\">Home</a> - <a href=\"index.php\">System administration</a> - Summary</h3>\n";

$users = execute("SELECT ID, Name, RealName FROM Users ORDER BY Name;");
print "  <ul>\n";
for ($userid = 0; $userid < count($users); $userid++){
  print "   <li>" . htmlspecialchars($users[$userid]['Name']) . " (" . htmlspecialchars($users[$userid]['RealName']) . ")\n";

  print "   <ul>\n";

  $domains = execute("SELECT ID, Name, DomainName FROM Domains WHERE UserID='" . mysql_escape_string($users[$userid]['ID']) . "' ORDER BY Name;");
  if ($domains){
    print "    <li><b>Domains:</b>\n";
    for ($domainid = 0; $domainid < count($domains); $domainid++){
      print "    <li>" . htmlspecialchars($domains[$domainid]['Name']) . " (" . htmlspecialchars($domains[$domainid]['DomainName']) . ")\n";
      print "    <ul>\n";

      $hosts = execute("SELECT ID, Name, Type, Data FROM Hosts WHERE DomainID='" . mysql_escape_string($domains[$domainid]['ID']) . "' ORDER BY Name;");
      if ($hosts){
        print "     <li><b>Hosts:</b>\n";
        for ($hostid = 0; $hostid < count($hosts); $hostid++){
          print "     <li>" . htmlspecialchars($hosts[$hostid]['Name']) . " -> " . htmlspecialchars($hosts[$hostid]['Type']) . ", " . htmlspecialchars($hosts[$hostid]['Data']) . "\n";
        }
      }

      $aliases = execute("SELECT ID, Name, Data FROM Aliases WHERE DomainID='" . mysql_escape_string($domains[$domainid]['ID']) . "' ORDER BY Name;");
      if ($aliases){
        print "     <li><b>Aliases:</b>\n";
        for ($aliasid = 0; $aliasid < count($aliases); $aliasid++){
          print "     <li>" . htmlspecialchars($aliases[$aliasid]['Name']) . " -> " . htmlspecialchars($aliases[$aliasid]['Data']) . "\n";
        }
      }

      print "    </ul>\n";
    }
  }

  $websites = execute("SELECT ID, Name FROM Websites WHERE UserID='" . mysql_escape_string($users[$userid]['ID']) . "' ORDER BY Name;");
  if ($websites){
    print "    <li><b>Websites:</b>\n";
    for ($websiteid = 0; $websiteid < count($websites); $websiteid++){
      print "    <li>" . htmlspecialchars($websites[$websiteid]['Name']) . "\n";
      print "    <ul>\n";

      $hosts = execute("SELECT ID, Host FROM WebsiteHosts WHERE WebsiteID='" . mysql_escape_string($websites[$websiteid]['ID']) . "' ORDER BY Host;");
      if ($hosts){
        print "     <li><b>Hosts:</b>\n";
        for ($hostid = 0; $hostid < count($hosts); $hostid++){
          print "      <li>" . htmlspecialchars($hosts[$hostid]['Host']) . "\n";
        }
      }
      print "    </ul>\n";
    }
  }

  $accounts = execute("SELECT ID, Name, RealName FROM Accounts WHERE UserID='" . mysql_escape_string($users[$userid]['ID']) . "' ORDER BY Name;");
  if ($accounts){
    print "    <li><b>Accounts:</b>\n";
    for ($accountid = 0; $accountid < count($accounts); $accountid++){
      print "    <li>" . htmlspecialchars($accounts[$accountid]['Name']) . " (" . htmlspecialchars($accounts[$accountid]['RealName']) . ")\n";
    }
  }

  print "   </ul>\n";
}
print "  </ul>\n";

print_footer();
?>
