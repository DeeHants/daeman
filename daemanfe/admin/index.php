<?php
require("../common.inc");

checkadminstatus();

print_header("System administration");
?>
  <h3><a href="../index.php">Home</a> - System administration</h3>
  <h2>Users</h2>
  <p>Edit your <a href="users.php">Users</a>.</p>
  <h2>Servers</h2>
  <p>Edit your <a href="servers.php">Servers</a>.</p>
  <h2>System summary</h2>
  <p>View a <a href="summary.php">summary</a> of all the users/domains/accounts/websites on the system in a tree structure.</p>
<?php
print_footer()
?>
