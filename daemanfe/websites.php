<?php
require("common.inc");

checkstatus();

print_header("Hosted websites: " . htmlspecialchars($details['RealName']));
?>
  <h3><a href="index.php">Home</a> - <a href="user.php?userid=<?php print urlencode($details['ID']); ?>">Account</a> - Websites</h3>
  <h2>Websites</h2>
<?php
if (isset($_REQUEST['action'])) {
  if ($_SESSION['userisadmin']) {
    if ($_REQUEST['action'] == "addwebsite") {
      if (execute("INSERT INTO Websites (UserID, Name, Logging, Redirect, ServerID) VALUES ('" . mysql_escape_string($details['ID']) . "', '" . mysql_escape_string($_REQUEST['name']) . "','" . mysql_escape_string($_REQUEST['logging']) . "', '" . mysql_escape_string($_REQUEST['redirect']). "', '" . mysql_escape_string($_REQUEST['serverid']) . "');")) {
        print "  <p class=status>Website added successfully.</p>\n";
      } else {
        print "  <p class=error>Error adding website.</p>\n";
      }
    } elseif ($_REQUEST['action'] == "updatewebsite") {
      if (execute("UPDATE Websites SET Logging='" . mysql_escape_string($_REQUEST['logging']) . "', Redirect='" .mysql_escape_string($_REQUEST['redirect']) . "', Parameters='" . mysql_escape_string($_REQUEST['parameters']) . "', ServerID='" . mysql_escape_string($_REQUEST['serverid']) . "' WHERE ID='" . mysql_escape_string($_REQUEST['websiteid']) . "';")) {
        print "  <p class=status>Website updated successfully.</p>\n";
      } else {
        print "  <p class=error>Error updating website.</p>\n";
      }
    } elseif ($_REQUEST['action'] == "deletewebsite") {
      if (execute("DELETE FROM Websites WHERE ID='" . mysql_escape_string($_REQUEST['websiteid']) . "';")) {
        if (execute("DELETE FROM WebsiteHosts WHERE WebsiteID='" . mysql_escape_string($_REQUEST['websiteid']) . "';")) {
          print "  <p class=status>Website deleted successfully.</p>\n";
        } else {
          print "  <p class=error>Error deleting website hosts.</p>\n";
        }
      } else {
        print "  <p class=error>Error deleting website.</p>\n";
      }
    }
  }
}

$websites = execute("SELECT ID, Name, Logging, Redirect FROM Websites WHERE UserID='" . mysql_escape_string($details['ID']) . "' ORDER BY Name;");

if ($websites) {
?>
  <table>
   <tr><?php if ($_SESSION['userisadmin']) { print "<th>Actions</th>"; } ?><th>Website name</th><th>Logging</th><th>Notes</th></tr>
<?php
  for($row = 0; $row < count($websites); $row++) {
    if ($_SESSION['userisadmin']) {
      print "   <tr><td><div class=action><a href=\"?action=editwebsite&amp;userid=" . urlencode($details['ID']) . "&amp;websiteid=" . urlencode($websites[$row]['ID']) . "#websiteform\">edit</a> <a href=\"?action=deletewebsite&amp;userid=" . urlencode($details['ID']) . "&amp;websiteid=" . urlencode($websites[$row]['ID']) . "\">delete</a></div></td><td><a href=\"website.php?websiteid=" . urlencode($websites[$row]['ID']) . "\">" . htmlspecialchars($websites[$row]['Name']) . "</a></td><td>" . iif($websites[$row]['Logging'], "Yes", "No") . "</td><td>" . iif($websites[$row]['Redirect'] == "", "", "Redirects to " . $websites[$row]['Redirect']) . "</td></tr>\n";
    } else {
      print "   <tr><td><a href=\"website.php?websiteid=" . urlencode($websites[$row]['ID']) . "\">" . htmlspecialchars($websites[$row]['Name']) . "</a></td><td>" . iif($websites[$row]['Logging'], "Yes", "No") . "</td><td>" . iif($websites[$row]['Redirect'] == "", "", "Redirected to " . $websites[$row]['Redirect']) . "</td></tr>\n";
    }
  }
?>
  </table>
<?php
} else {
  print "  <p>There are no websites currently set up. Please contact Earl Software to add a new website.</p>\n";
}

if ($_SESSION['userisadmin']) {
  $fill = array();
  if ($_REQUEST['action'] == "editwebsite") {
    $website = execute("SELECT Name, Logging, Redirect, Parameters, ServerID FROM Websites WHERE ID='" . mysql_escape_string($_REQUEST['websiteid']) . "';");
    $fill['id'] = $_REQUEST['websiteid'];
    $fill['name'] = $website[0]['Name'];
    $fill['logging'] = $website[0]['Logging'];
    $fill['redirect'] = $website[0]['Redirect'];
    $fill['parameters'] = $website[0]['Parameters'];
    $fill['serverid'] = $website[0]['ServerID'];
  }
?>
  <a name=websiteform></a>
  <form action="websites.php" method="POST">
   <input name="action" type="hidden" value="<?php print iif(isset($fill['id']), "update", "add"); ?>website">
   <input name="userid" type="hidden" value="<?php print htmlspecialchars($details['ID']); ?>">
<?php if (isset($fill['id'])) { ?>
   <input name="websiteid" type="hidden" value="<?php print htmlspecialchars($fill['id']); ?>">
<?php } ?>
   <table>
<?php if (isset($fill['id'])) { ?>
    <tr><td>Website name</td><td><?php print htmlspecialchars($fill['name']); ?></td></tr>
<?php } else { ?>
    <tr><td>Website name</td><td><input name="name" value="<?php print htmlspecialchars($fill['name']); ?>"></td></tr>
<?php } ?>
    <tr><td>Enable logging</td><td><input type="checkbox" name="logging" value=1<?php if ($fill['logging']) { print " checked"; } ?>></td></tr>
    <tr><td>Redirect to</td><td><input name="redirect" value="<?php print htmlspecialchars($fill['redirect']); ?>"></td></tr>
<?php if (isset($fill['id'])) { ?>
    <tr><td>VHost directives</td><td><textarea name="parameters" rows=3 cols=40><?php print htmlspecialchars($fill['parameters']); ?></textarea></td></tr>
<?php } ?>
    <tr><td>Server</td><td><select name="serverid"><?php print optionlist(execute("SELECT ID, Name FROM Servers WHERE HTTP=1 ORDER BY Name;"), $fill['serverid']); ?></select></td></tr>
    <tr><td colspan=2 align=center><input type="submit" value="<?php print iif(isset($fill['id']), "Update", "Add"); ?> website"></td></tr>
   </table>
  </form>
<?php
}

print_footer();
?>
