<?php
require("../common.inc");
print_header("Error");
?>
  <p>You do not appear to be logged in. You need to be logged in to access this page. If you have logged in, make sure you have cookies enabled, and try again.</p>
  <p><a href="../<?php if (isset($url)) { print "?url=" . rawurlencode($url); } ?>">Back to home</a></p>
<?php
print_footer();
?>
