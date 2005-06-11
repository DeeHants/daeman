<?php
require("../common.inc");
require("dns.inc");
checkstatus();

print_header("Name lookup");
print "  <h3><a href=\"../index.php\">Home</a> - <a href=\"index.php\">Tools</a> - Name lookup</h3>\n";

if (isset($_REQUEST['action'])) {
  if ($_REQUEST['action'] == "lookup") {

    $hostnames[] = $_REQUEST['hostname'];

    print "  <h2>Name lookup results</h2>";

    for ($hostnameid = 0; $hostnameid < count($hostnames); $hostnameid++) {
      print "  <p id=\"pleasewait" . $hostnames[$hostnameid] . "\">Please wait, looking up " . $hostnames[$hostnameid] . "...</p>\n";
      $data = lookup($hostnames[$hostnameid], $_REQUEST['type']);
      print "  <script language=javascript type=\"text/javascript\">document.getElementById(\"pleasewait" . $hostnames[$hostnameid] . "\").style.display=\"none\";</script>\n";
      print "  <p><pre>" . $data . "</pre></p>\n";
    }
  }
}
?>
  <h2>Name lookup</h2>
  <form action="dns.php">
   <input type=hidden name=action value="lookup">
   <table>
    <tr><td>Hostname</td><td><input name="hostname" value="<?php print htmlspecialchars($_REQUEST["hostname"]); ?>"></td></tr>
    <tr><td>Type</td><td><select name=type><?php print optionlist(array(array("a", "A record"), array("mx", "MX records"), array("ns", "Name server records"), array("any", "All details")), $_REQUEST['type']); ?></td></tr>
<!--
    <tr><td>Top level domain</td><td><select name=suffix><option value="">Custom<option value="couk">.co.uk<option value="com">.com<option value=".net</td></tr>
    <tr><td>Guess alternatives</td><td><input type=checkbox name=guess></td></tr>
-->
    <tr><td colspan=2 align=center><input type=submit value="Lookup"></td></tr>
   </table>
  </form>
<?php
print_footer();
?>
