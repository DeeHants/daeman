<?php
require("common.inc");

checkstatus();

print_header("POP3/IMAP accounts: " . htmlspecialchars($details['Name']));
?>
  <h3><a href="index.php">Home</a> - <a href="user.php?userid=<?php print urlencode($details['ID']); ?>">Account</a> - Mail accounts</h3>
  <h2>Accounts</h2>
<?php
if (isset($_REQUEST['action'])) {
  if ($_REQUEST['action'] == "addaccount") {
    $res = execute("SELECT Max(AccountID) AS LastID FROM Accounts WHERE UserID='" . mysql_escape_string($details['ID']) . "';");
    $nextaccountid = $res[0]['LastID'];
    if (!($nextaccountid)) {
      $res = execute("SELECT AccountID FROM Users WHERE ID='" . mysql_escape_string($details['ID']) . "';");
      $nextaccountid = $res[0]['AccountID'];
    }
    $nextaccountid++;

    if (execute("INSERT INTO Accounts (UserID, Name, RealName, AccountID) VALUES ('" . mysql_escape_string($details['ID']) . "', '" . mysql_escape_string($_REQUEST['name']) . "', '" . mysql_escape_string($_REQUEST['realname']) . "', '" . mysql_escape_string($nextaccountid) . "');")) {
      print "   <p class=status>" . htmlspecialchars($_REQUEST['accountname']) . " successfully added.</p>";
    } else {
      print "   <p class=error>There was an error adding " . htmlspecialchars($_REQUEST['accountname']) . ".</p>";
    }
  } elseif ($_REQUEST['action'] == "updateaccount") {
    if (execute("UPDATE Accounts SET RealName='" . mysql_escape_string($_REQUEST['realname']) . "' WHERE ID='" . mysql_escape_string($_REQUEST['accountid']) . "';")) {
      print "  <p class=status>Email account updated successfully.</p>\n";
    } else {
      print "  <p class=error>Error updating email account.</p>\n";
    }
  } elseif ($_REQUEST['action'] == "deleteaccount") {
    if (execute("DELETE FROM Accounts WHERE ID='" . mysql_escape_string($_REQUEST['accountid']) . "';")) {
      print "  <p class=status>Email account deleted successfully.</p>\n";
    } else {
      print "  <p class=error>Error deleting email account.</p>\n";
    }
  }
}

$accounts = execute("SELECT ID, Name, RealName FROM Accounts WHERE UserID='" . mysql_escape_string($details['ID']) . "';");

if ($accounts) {
?>
  <table>
   <tr><th>Actions</th><th>Name</th><th>Real name</th></tr>
<?php
  for($row = 0; $row < count($accounts); $row++) {
    print "  <tr><td><div class=action><a href=\"?action=editaccount&amp;userid=" . urlencode($details['ID']) . "&amp;accountid=" . urlencode($accounts[$row]['ID']) . "\">edit</a> <a href=\"?action=deleteaccount&amp;userid=" . urlencode($details['ID']) . "&amp;accountid=" . urlencode($accounts[$row]['ID']) . "\">delete</a> <a href=\"chpasswd.php?userid=" . urlencode($details['ID']) . "&amp;accountid=" . urlencode($accounts[$row]['ID']) . "\">set pass</a></div></td><td>" . htmlspecialchars($accounts[$row]['Name']) . "</td><td>" . htmlspecialchars($accounts[$row]['RealName']) . "</td></tr>\n";
  }
?>
  </table>
<?php
} else {
  print "  <p>There are no accounts</p>\n";
}

if ($_REQUEST['action'] == "editaccount") {
  $account = execute("SELECT Name, RealName FROM Accounts WHERE ID = " . mysql_escape_string($_REQUEST['accountid']) . " ORDER BY Name;");
?>
  <form action="accounts.php" method="POST">
   <input name="action" type="hidden" value="updateaccount">
   <input name="userid" type="hidden" value="<?php print htmlspecialchars($details['ID']); ?>">
   <input name="accountid" type="hidden" value="<?php print htmlspecialchars($_REQUEST['accountid']); ?>">
   <table>
    <tr><td>Account name</td><td><?php print htmlspecialchars($account[0]['Name']); ?></td></tr>
    <tr><td>Real name</td><td><input name="realname" value="<?php print htmlspecialchars($account[0]['RealName']); ?>"></td></tr>
    <tr><td colspan=2 align=center><input type="submit" value="Update account"></td></tr>
   </table>
  </form>
<?php
} else {
?>
  <form action="accounts.php" method="POST">
   <input name="action" type="hidden" value="addaccount">
   <input name="userid" type="hidden" value="<?php print htmlspecialchars($details['ID']); ?>">
   <table>
    <tr><td>Account name</td><td><input name="name"></td></tr>
    <tr><td>Real name</td><td><input name="realname"></td></tr>
    <tr><td colspan=2 align=center><input type="submit" value="Add new account"></td></tr>
   </table>
  </form>
<?php
}

print_footer();
?>
