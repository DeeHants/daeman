<?php
require("common.inc");
checkstatus();
print_header("Account administration: " . htmlspecialchars($details['RealName']));
?>
  <h3><a href="index.php">Home</a> - Account</h3>
  <h2>Domains</h2>
  <p>Edit your <a href="domains.php?userid=<?php print urlencode($details['ID']) ?>">hosted domains</a>.</p>
  <h2>Mail accounts</h2>
  <p>Edit your <a href="accounts.php?userid=<?php print urlencode($details['ID']) ?>">POP3/IMAP mail accounts</a>.</p>
  <p>Edit your <a href="lists.php?userid=<?php print urlencode($details['ID']) ?>">mailing lists</a>.</p>
  <h2>Websites</h2>
  <p>Edit your <a href="websites.php?userid=<?php print urlencode($details['ID']) ?>">website details</a>.</p>
<?php
print_footer();
?>
