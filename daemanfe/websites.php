<?php
require("common.inc");

checkstatus();

print_header("Hosted websites: " . htmlspecialchars($details['RealName']));
?>
  <h3><a href="index.php">Home</a> - <a href="user.php?userid=<?php print urlencode($userid); ?>">Account</a> - Websites</h3>
  <h2>Websites</h2>
<?php
if (isset($action)){
  if (userisadmin($currentuserid)) {
    if ($action == "addwebsite"){
      if (execute("INSERT INTO Websites (UserID, Name, Trial, Logging) VALUES ('" . mysql_escape_string($userid) . "', '" . mysql_escape_string($name) . "','" . mysql_escape_string($trial) . "','" . mysql_escape_string($logging) . "');")){
        print "  <p class=status>Website added successfully.</p>\n";
      }else{
        print "  <p class=error>Error adding website.</p>\n";
      }
    }elseif ($action == "updatewebsite"){
      if (execute("UPDATE Websites SET Name='" . mysql_escape_string($name) . "', Trial='" . mysql_escape_string($trial) . "', Logging='" . mysql_escape_string($logging) . "' WHERE ID='" . mysql_escape_string($websiteid) . "';")){
        print "  <p class=status>Website updated successfully.</p>\n";
      }else{
        print "  <p class=error>Error updating website.</p>\n";
      }
    }elseif ($action == "deletewebsite"){
      if (execute("DELETE FROM Websites WHERE ID='" . mysql_escape_string($websiteid) . "';")){
        if (execute("DELETE FROM WebsiteHosts WHERE WebsiteID='" . mysql_escape_string($websiteid) . "';")){
          print "  <p class=status>Website deleted successfully.</p>\n";
        }else{
          print "  <p class=error>Error deleting website hosts.</p>\n";
        }
      }else{
        print "  <p class=error>Error deleting website.</p>\n";
      }
    }
  }
}

$websites = execute("SELECT ID, Name, Trial, Logging FROM Websites WHERE UserID='" . mysql_escape_string($userid) . "';");

if ($websites){
?>
  <table backcolour=red>
   <tr><?php if (userisadmin($currentuserid)) { print "<th>Actions</th>"; } ?><th>Website name</th><th>Trial site</th><th>Logging</th></tr>
<?php
  for($row = 0; $row < count($websites); $row++){
    if (userisadmin($currentuserid)) {
      print "   <tr><td><div class=action><a href=\"?action=editwebsite&amp;userid=" . urlencode($userid) . "&amp;websiteid=" . urlencode($websites[$row]['ID']) . "#websiteform\">edit</a> <a href=\"?action=deletewebsite&amp;userid=" . urlencode($userid) . "&amp;websiteid=" . urlencode($websites[$row]['ID']) . "\">delete</a></div></td><td><a href=\"website.php?websiteid=" . urlencode($websites[$row]['ID']) . "\">" . htmlspecialchars($websites[$row]['Name']) . "</a></td><td>" . iif($websites[$row]['Trial'], "Yes", "No") . "</td><td>" . iif($websites[$row]['Logging'], "Yes", "No") . "</td></tr>\n";
    } else {
      print "   <tr><td><a href=\"website.php?websiteid=" . urlencode($websites[$row]['ID']) . "\">" . htmlspecialchars($websites[$row]['Name']) . "</a></td><td>" . iif($websites[$row]['Trial'], "Yes", "No") . "</td><td>" . iif($websites[$row]['Logging'], "Yes", "No") . "</td></tr>\n";
    }
  }
?>
  </table>
<?php
}else{
  print "  <p>There are no websites currently set up. Please contact Earl Software to add a new website.</p>\n";
}

if (userisadmin($currentuserid)) {
  if ($action == "editwebsite"){
    $website = execute("SELECT Name, Trial, Logging FROM Websites WHERE ID='" . mysql_escape_string($websiteid) . "';");
?>
  <a name=websiteform>
  <form action="websites.php" method="POST">
   <input name="action" type="hidden" value="updatewebsite">
   <input name="userid" type="hidden" value="<?php print htmlspecialchars($userid); ?>">
   <input name="websiteid" type="hidden" value="<?php print htmlspecialchars($websiteid); ?>">
   <table>
    <tr><td>Website name</td><td><input name="name" value="<?php print htmlspecialchars($website[0]['Name']); ?>"> <a href="help.php#websitename">?</a></td></tr>
    <tr><td>Trial site?</td><td><input type="checkbox" name="trial" value=1<?php if ($website[0]['Trial']) { print " checked"; } ?>> <a href="../help.php#websitetrial">?</a></td></tr>
    <tr><td>Enable Logging</td><td><input type="checkbox" name="logging" value=1<?php if ($website[0]['Logging']) { print " checked"; } ?>> <a href="../help.php#websitelogging">?</a></td></tr>
    <tr><td colspan=2 align=center><input type="submit" value="Update website"></td></tr>
   </table>
  </form>
<?php
  }else{
?>
  <a name=websiteform>
  <form action="websites.php" method="POST">
   <input name="action" type="hidden" value="addwebsite">
   <input name="userid" type="hidden" value="<?php print htmlspecialchars($userid); ?>">
   <table>
    <tr><td>Name</td><td><input name="name"> <a href="help.php#websitename">?</a></td></tr>
    <tr><td>Trial site?</td><td><input type="checkbox" name="trial" value=1 checked> <a href="../help.php#websitetrial">?</a></td></tr>
    <tr><td>Enable Logging</td><td><input type="checkbox" name="logging" value=1 checked> <a href="../help.php#websitelogging">?</a></td></tr>
    <tr><td colspan=2 align=center><input type="submit" value="Add website"></td></tr>
   </table>
  </form>
<?php
  }
}

print_footer();
?>
