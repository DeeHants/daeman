<?php
require("../common.inc");

checkstatus();

print_header("Tools");
?>
  <h3><a href="../index.php">Home</a> - Tools</h3>
  <h2><a href="whois.php">Domain lookup</a></h2>
  <p>This allows you to check for a domain's availability and who it is currently registered to.</p>
<!--
  <h2><a href="dns.php">Name lookup</a></h2>
  <p>This allows you to do DNS lookups using any DNS server to check propogations, and accessability.</p>
-->
<?php
print_footer()
?>
