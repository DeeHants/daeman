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
  <p>Edit your <a href="domains.php?userid=<?php print urlencode($details['ID']) ?>">hosted domains</a>.</p>
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
  <p>Edit your <a href="websites.php?userid=<?php print urlencode($details['ID']) ?>">website details</a>.</p>
<?php
}
print_footer();
?>
