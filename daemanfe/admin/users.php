<?php
require("../common.inc");

checkadminstatus();

print_header("User Administration");
print "  <h3><a href=\"../index.php\">Home</a> - <a href=\"index.php\">System administration</a> - Users</h3>\n";


if (isset($action)){
  if ($action == "adduser"){
    if ($mode == "service"){
      $res = execute("SELECT Max(AccountID) AS LastID FROM Users WHERE AccountID>=400 AND AccountID<500;");
      $nextaccountid = $res[0]['LastID'];
      $nextaccountid++;
    }elseif ($mode == "user" || $mode == "admin"){
      $res = execute("SELECT Max(AccountID) AS LastID FROM Users WHERE AccountID>=500 AND AccountID<1000;");
      $nextaccountid = $res[0]['LastID'];
      $nextaccountid++;
    }elseif ($mode == "customer"){
      $res = execute("SELECT Max(AccountID) AS LastID FROM Users WHERE AccountID>=1000;");
      $nextaccountid = $res[0]['LastID'];
      $nextaccountid = (($nextaccountid / 100) + 1) * 100;
    }

    if (execute("INSERT INTO Users (Name, AccountID, Password, DBPassword, RealName, Enabled, Hosting, Admin, DB) VALUES ('" . mysql_escape_string($name) . "', '" . mysql_escape_string($nextaccountid) . "', '!!', '', '" . mysql_escape_string($realname) . "', '" . mysql_escape_string($enabled) . "', " . iif($mode == "customer", "1", "0") . ", " . iif($mode == "admin", "1", "0") . ", 0);")){
      print "  <p class=status>User added successfully.</p>\n";
    }else{
      print "  <p class=error>Error adding user. $DBError</p>\n";
    }
  }elseif ($action == "updateuser"){
/*
<!--
Name	AccountID	Password,DBPassword	RealName	Expires	Enabled	 Hosting	Admin	DB
form	form					form			form	form	form	form
-->
*/

//    if (execute("UPDATE Users SET Name='" . mysql_escape_string($name) . "', AccountID='" . mysql_escape_string($accountid) . "', RealName='" . mysql_escape_string($realname) . "', Enabled='" . mysql_escape_string($enabled) . "', Hosting='" . mysql_escape_string($hosting) . "' WHERE ID='" . mysql_escape_string($userid) . "';")){
    if (execute("UPDATE Users SET Name='" . mysql_escape_string($name) . "', AccountID='" . mysql_escape_string($accountid) . "', RealName='" . mysql_escape_string($realname) . "', Enabled='" . mysql_escape_string($enabled) . "', Hosting='" . mysql_escape_string($hosting) . "', Admin='" . mysql_escape_string($admin) . "', DB='" . mysql_escape_string($db) . "' WHERE ID='" . mysql_escape_string($userid) . "';")){
      print "  <p class=status>User updated successfully.</p>\n";
    }else{
      print "  <p class=error>Error updating user.</p>\n";
    }
  }elseif ($action == "deleteuser"){
    if (execute("DELETE FROM Users WHERE ID='" . mysql_escape_string($userid) . "';")){
      print "  <p class=status>User deleted successfully.</p>\n";
    }else{
      print "  <p class=error>Error deleting user.</p>\n";
    }
  }
}

$users = execute("SELECT Users.ID, Users.Name, Users.AccountID, Enabled, Hosting, Admin FROM Users ORDER BY Name;");
if ($users){
?>
  <table backcolour=red>
   <tr><th>Actions</th><th>Name</th><th>User ID</th><th>Enabled</th><th>Hosting</th><th>Administrator</th></tr>
<?php
  for($row = 0; $row < count($users); $row++){
    print "   <tr><td><div class=action><a href=\"users.php?action=edituser&amp;userid=" . urlencode($users[$row]['ID']) . "#userform\">edit</a> <a href=\"users.php?action=deleteuser&amp;userid=" . urlencode($users[$row]['ID']) . "\">delete</a> <a href=\"../chpasswd.php?userid=" . urlencode($users[$row]['ID']) . "\">set pass</a></div></td><td>" . iif($users[$row]['Hosting'], "<a href=\"../user.php?userid=" . urlencode($users[$row]['ID']) . "\">", "") . htmlspecialchars($users[$row]['Name']) . iif($users[$row]['Hosting'], "</a>", "") . "</td><td>" . htmlspecialchars($users[$row]['AccountID']) . "</td><td>" . iif($users[$row]['Enabled'], "Yes", "No") . "</td><td>" . iif($users[$row]['Hosting'], "Yes", "No") . "</td><td>" . iif($users[$row]['Admin'], "Yes", "No") . "</td></tr>\n";
  }
?>
  </table>
<?php
}else{
  print "  <p>There are no users</p>\n";
}

if ($action == "edituser"){
  $user = execute("SELECT Name, AccountID, RealName, Enabled, Hosting, DB, 
Admin FROM Users WHERE ID='" . mysql_escape_string($userid) . "';");
?>
  <a name=userform>
  <form action="users.php" method="POST">
   <input name="action" type="hidden" value="updateuser">
   <input name="userid" type="hidden" value="<?php print htmlspecialchars($userid); ?>">
   <table>
    <tr><td>User name</td><td><input name="name" value="<?php print htmlspecialchars($user[0]['Name']); ?>"> <a href="../help.php#username">?</a></td></tr>
    <tr><td>User ID</td><td><input name="accountid" value="<?php print htmlspecialchars($user[0]['AccountID']); ?>"> <a href="../help.php#useraccountid">?</a></td></tr>
    <tr><td>Real name</td><td><input name="realname" value="<?php print htmlspecialchars($user[0]['RealName']); ?>"> <a href="../help.php#userrealname">?</a></td></tr>
    <tr><td>Enabled</td><td><input type="checkbox" name="enabled" value=1<?php if ($user[0]['Enabled']) { print " checked"; } ?>> <a href="../help.php#userenabled">?</a></td></tr>
    <tr><td>Hosting</td><td><input type="checkbox" name="hosting" value=1<?php if ($user[0]['Hosting']) { print " checked"; } ?>> <a href="../help.php#userhosting">?</a></td></tr>
    <tr><td>Database access</td><td><input type="checkbox" name="db" value=1<?php if ($user[0]['DB']) { print " checked"; } ?>> <a href="../help.php#userdb">?</a></td></tr>
    <tr><td>Administrator</td><td><input type="checkbox" name="admin" value=1<?php if ($user[0]['Admin']) { print " checked"; } ?>> <a href="../help.php#useradmin">?</a></td></tr>
    <tr><td colspan=2 align=center><input type="submit" value="Update user"></td></tr>
   </table>
  </form>
<?php
}else{
?>
  <a name=userform>
  <form action="users.php" method="POST">
   <input name="action" type="hidden" value="adduser">
   <table>
    <tr><td>User name</td><td><input name="name"> <a href="../help.php#username">?</a></td></tr>
    <tr><td>User mode</td><td><select name="mode"><option value="service">Service<option value="user">User<option value="customer" selected>Customer<option value="admin">Administrator</select> <a href="../help.php#usermode">?</a></td></tr>
    <tr><td>Real name</td><td><input name="realname"> <a href="../help.php#userrealname">?</a></td></tr>
    <tr><td>Enabled</td><td><input type="checkbox" name="enabled" value=1 checked> <a href="../help.php#userenabled">?</a></td></tr>
    <tr><td colspan=2 align=center><input type="submit" value="Add user"></td></tr>
   </table>
  </form>
<?php
}

print_footer()
?>
