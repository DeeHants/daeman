<?php
require("common.inc");

checkstatus();

print_header("Mailing lists: " . htmlspecialchars($details['Name']));
?>
  <h3><a href="index.php">Home</a> - <a href="user.php?userid=<?php print urlencode($userid); ?>">Account</a> - Mailing lists</h3>
  <h2>Lists</h2>
<?php
if (isset($action)){
  if ($action == "addlist"){
    if (execute("INSERT INTO Lists (UserID, Name, Owner, Public) VALUES ('" . mysql_escape_string($userid) . "', '" . mysql_escape_string($name) . "', '" . mysql_escape_string($owner) . "', '" . mysql_escape_string($public) . "');")){
      print "   <p class=status>" . htmlspecialchars($name) . " successfully added.</p>";
    }else{
      print "   <p class=error>There was an error adding " . htmlspecialchars($name) . ".</p>";
    }
  }elseif ($action == "updatelist"){
    if (execute("UPDATE Lists SET Owner='" . mysql_escape_string($owner) . "', Public='" . mysql_escape_string($public). "' WHERE ID='" . mysql_escape_string($listid) . "';")){
      print "  <p class=status>Mailing list updated successfully.</p>\n";
    }else{
      print "  <p class=error>Error updating mail list.</p>\n";
    }
  }elseif ($action == "deletelist"){
    if (execute("DELETE FROM Lists WHERE ID='" . mysql_escape_string($listid) . "';")){
      print "  <p class=status>Mailing list deleted successfully.</p>\n";
    }else{
      print "  <p class=error>Error deleting mailing list.</p>\n";
    }
  }
}

$lists = execute("SELECT ID, Name, Owner, Public FROM Lists WHERE UserID='" . mysql_escape_string($userid) . "';");

if ($lists){
?>
  <table>
   <tr><th>Actions</th><th>Name</th><th>Owner</th><th>Public</th></tr>
<?php
  for($row = 0; $row < count($lists); $row++){
    print "  <td><div class=action><a href=\"?action=editlist&amp;userid=" . urlencode($userid) . "&amp;listid=" . urlencode($lists[$row]['ID']) . "\">edit</a> <a href=\"?action=deletelist&amp;userid=" . urlencode($userid) . "&amp;listid=" . urlencode($lists[$row]['ID']) . "\">delete</a></div></td><td>" . htmlspecialchars($lists[$row]['Name']) . "</td><td>" . htmlspecialchars($lists[$row]['Owner'])."</td><td>" . iif($lists[$row]['Public'], "Yes", "No") . "</td></tr>\n";
  }
?>
  </table>
<?php
}else{
  print "  <p>There are no mailing lists</p>\n";
}

if ($action == "editlist"){
  $list = execute("SELECT Name, Owner, Public FROM Lists WHERE ID = " . mysql_escape_string($listid) . " ORDER BY Name;");
?>
  <form action="lists.php" method="POST">
   <input name="action" type="hidden" value="updatelist">
   <input name="userid" type="hidden" value="<?php print htmlspecialchars($userid); ?>">
   <input name="listid" type="hidden" value="<?php print htmlspecialchars($listid); ?>">
   <table>
    <tr><td>List name</td><td><?php print htmlspecialchars($list[0]['Name']); ?> <a href="../help.php#listname">?</a></td></tr>
    <tr><td>Owner</td><td><input name="owner" value="<?php print htmlspecialchars($list[0]['Owner']); ?>"> <a href="../help.php#listowner">?</a></td></tr>
    <tr><td>Public</td><td><input type="checkbox" name="public" value=1<?php if ($list[0]['Public']) { print " checked"; } ?>> <a href="../help.php#listpublic">?</a></td></tr>
    <tr><td colspan=2 align=center><input type="submit" value="Update list"></td></tr>
   </table>
  </form>
<?php
}else{
?>
  <form action="lists.php" method="POST">
   <input name="action" type="hidden" value="addlist">
   <input name="userid" type="hidden" value="<?php print htmlspecialchars($userid); ?>">
   <table>
    <tr><td>List name</td><td><input name="name"> <a href="../help.php#listname">?</a></td></tr>
    <tr><td>Owner</td><td><input name="owner"> <a href="../help.php#listowner">?</a></td></tr>
    <tr><td>Public</td><td><input type="checkbox" name="public" value=1> <a href="../help.php#listpublic">?</a></td></tr>
    <tr><td colspan=2 align=center><input type="submit" value="Add new list"></td></tr>
   </table>
  </form>
<?php
}

print_footer();
?>
