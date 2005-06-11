<?php
require("common.inc");
checkstatus();

$hasmail = (count(execute("SELECT ID FROM Servers WHERE Mail=1;")) >= 1);
$haslist = (count(execute("SELECT ID FROM Servers WHERE List=1;")) >= 1);
$hashttp = (count(execute("SELECT ID FROM Servers WHERE HTTP=1;")) >= 1);
print_header("Account administration: " . htmlspecialchars($details['RealName']));
?>
  <h3><a href="index.php">Home</a> - Account</h3>
  <h2>Domains</h2>
  <p>Edit your <a href="domains.php?userid=<?php print urlencode($details['ID']) ?>">domains</a>, and manage all the hosts/email addresses.</p>
<?php
if ($hasmail || $haslist) {
?>
  <h2>Mail accounts</h2>
<?php
  if ($hasmail) {
?>
  <p>Edit your <a href="accounts.php?userid=<?php print urlencode($details['ID']) ?>">POP3/IMAP mail accounts</a>.</p>
<?php
  }
  if ($haslist) {
?>
  <p>Edit your <a href="lists.php?userid=<?php print urlencode($details['ID']) ?>">mailing lists</a>.</p>
<?php
  }
}
if ($hashttp) {
?>
  <h2>Websites</h2>
  <p>Edit the list of <a href="websites.php?userid=<?php print urlencode($details['ID']) ?>">hosted websites</a> and their hosts.</p>
<?php
}
if ($hasmail || $hashttp) {
?>
  <h2>Quicksetup</h2>
  <p>Alternatively, you can use the <a href="quicksetup.php?userid=<?php print urlencode($details['ID']) ?>">quick setup</a> that will add a domain, website and set up the hosts and default email address in one step.</p>
<?php
}
print_footer();
?>
