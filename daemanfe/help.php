<?php
require("common.inc");
print_header("Help");
?>
  <p>This page contains help on all the options and settings available through the management interface.</p>
  <h2>Home</h2>
  <p>In the Home section, you will see a number of options which will be explained below:</p>
  <h3>Administer account</h3>
  <p>This will allow you to change any aspects of your hosted services (email aliases, domain names, websites, etc...)</p>
  <h3>Change password</h3>
  <p>You can change the password used for this admin system, and the default mailbox for all your domains (if they aren't forwarded elsewhere). This also affects your Database password if necassary.</p>
  <h3>Log out</h3>
  <p>This will log you out of the management system and close the session.</p>
  <h3>Administration</h3>
  <p>If your user is marked as an admin, this will give you access to the user administration options as well as allow you to administer existing users accounts.</p>
  <h2>Account Administration</h2>
<?php
print_footer();
?>
