<?php
require("common.inc");
print_header("Help");

switch ($_REQUEST['section']) {
case "":
case "contents":
?>
  <h3><a href="index.php">Home</a> - Help</h3>
  <p>This is the management system used to control your domains, websites, mail and mailing lists. The sections below cover various features of this management system.</p>
  <ol>
   <li><a href="help.php?section=home">Home</a>
   <li><a href="help.php?section=account">Account administration</a>
   <ol>
    <li><a href="help.php?section=domains">Domains</a>
    <ol>
     <li><a href="help.php?section=aliases">Mail aliases</a>
     <li><a href="help.php?section=hosts">DNS hosts</a>
    </ol>
    <li><a href="help.php?section=accounts">Mail accounts</a>
    <li><a href="help.php?section=lists">Mailing lists</a>
    <li><a href="help.php?section=websites">Websites</a>
   </ol>
   <li><a href="help.php?section=tools">Tools</a>
    <ol>
     <li><a href="help.php?section=whois">Domain lookup</a>
<!--     <li><a href="help.php?section=dns">Name lookup</a>-->
    </ol>
   <li><a href="help.php?section=administration">System administration</a>
   <ol>
    <li><a href="help.php?section=users">Users</a>
    <li><a href="help.php?section=servers">Servers</a>
    <li><a href="help.php?section=summary">Summary</a>
   </ol>
  </ol>
<?php
  break;
case "home":
?>
  <h3><a href="index.php">Home</a> - <a href="help.php">Help</a> - Home</h3>
  <p>In the Home section, you will see a number of options which will be explained below:</p>
  <h3><a href="http://management.earlsoft.co.uk/help.php?section=administration">Administer account</a></h3>
  <p>This will allow you to change any aspects of your hosted services (email aliases, domain names, websites, etc...)</p>
  <h3>Change password</h3>
  <p>You can change the password used for the management system, and the default mailbox for all your domains (if they aren't forwarded elsewhere). This also affects your Database password if its configured.</p>
  <h3><a href="http://management.earlsoft.co.uk/help.php?section=tools">Tools</a></h3>
  <p>The tools section allow you to do various diagnostic tests like DNS and Domain lookups.<p>
  <h3><a href="http://management.earlsoft.co.uk/help.php?section=administration">System administration</a></h3>
  <p>If your user is marked as an admin, this will give you access to the user administration options as well as allow you to administer existing users accounts.</p>
  <h3>Help</h3>
  <p>This will open up the contants of this help system where you can find out all about this management system.</p>
  <h3>Log out</h3>
  <p>This will log you out of the management system and close the session.</p>
  <p>Once the session is closed, no one can use this computer to access or change your settings until you log back in again.</p>
<?php
  break;
case "account":
?>
  <h3><a href="index.php">Home</a> - <a href="help.php">Help</a> - Account administration</h3>
<?php
  break;
default:
?>
  <h3><a href="index.php">Home</a> - <a href="help.php">Help</a> - Invalid section</h3>
  <p>The specified section does not exist.</p>
<?php
}
print_footer();
?>
