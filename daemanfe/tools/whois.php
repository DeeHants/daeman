<?php
require("../common.inc");
require("whois.inc");
checkstatus();

print_header("Domain lookup");
print "  <h3><a href=\"../index.php\">Home</a> - <a href=\"index.php\">Tools</a> - Domain lookup</h3>\n";

if (isset($_REQUEST['action'])) {
  if ($_REQUEST['action'] == "lookup") {

    if (strpos($_REQUEST['domain'], ".") != 0) {
      $domains[] = $_REQUEST['domain'];
    } else {
      $domains[] = $_REQUEST['domain'] . ".com";
      $domains[] = $_REQUEST['domain'] . ".net";
      $domains[] = $_REQUEST['domain'] . ".org";
      $domains[] = $_REQUEST['domain'] . ".co.uk";
      $domains[] = $_REQUEST['domain'] . ".org.uk";
    }


    print "  <h2>Domain lookup results</h2>";

    for ($domainid = 0; $domainid < count($domains); $domainid++) {
      print "  <p id=\"pleasewait" . $domains[$domainid] . "\">Please wait, looking up " . $domains[$domainid] . "...</p>\n";
      flush();
      $data = lookup($domains[$domainid], $_REQUEST['server']);
      print "  <script language=javascript type=\"text/javascript\">document.getElementById(\"pleasewait" . $domains[$domainid] . "\").style.display=\"none\";</script>\n";

      if ($data != "") {
        $info = parse_whois($data);
        if (!isset($info['domain'])) { $info['domain'] = $domains[$domainid]; }
        if (!isset($_REQUEST['server'])){
          if ($info['registered'] == 1) {
            print "  <p>" . $domains[$domainid] . " is currently registered";
            if ($info["registrant.name"] != "") {
              print " to " . $info["registrant.name"];
              if ($info["registrant.company"] != "" && $info["registrant.company"] != $info["registrant.name"]) {
                print " of " . $info["registrant.company"];
              }
            }
            print " by ";
            if ($info["agent.url"] != "") { print "<a href=\"" . $info["agent.url"] . "\" target=\"_blank\">"; }
            print $info["agent.name"];
            if ($info["agent.url"] != "") { print "</a>"; }
            if ($info["agent.tag"] != "") { print " (" . $info["agent.tag"] . ")"; }
/*
    [status] => ACTIVE
*/        
            print ".<br>It was registered on " . date("j/n/Y", $info["date.registered"]) . " and expires on " . date("j/n/Y", $info["date.renewal"]) . ".";
            if ($info["agent.whois"] != "") {
              print "<br>The registrars <a href=\"whois.php?action=lookup&domain=" . urlencode($info["domain"]) . "&server=" . urlencode($info["agent.whois"]) . "\">whois server</a> has more information.";
            }
            print "</p>\n";
          } else {
            print "  <p>" . $info["domain"] . " is currently available.</p>\n";
          }
        }
	if (isset($_REQUEST['showall']) || isset($_REQUEST['server'])) { 
          print "  <pre>" . $info['data'] . "</pre>";
        }
      } else {
        print "  <p>There was an error looking up " . $domains[$domainid] . ".</p>\n";
      }
    }
  }
}
?>
  <h2>Domain lookup</h2>
  <form action="whois.php">
   <input type=hidden name=action value="lookup">
   <table>
    <tr><td>Domain</td><td><input name=domain value="<?php print htmlspecialchars($_REQUEST["domain"]); ?>"></td></tr>
<!--
    <tr><td>Top level domain</td><td><select name=suffix><option value="">Custom<option value="couk">.co.uk<option value="com">.com<option value=".net</td></tr>
    <tr><td>Guess alternatives</td><td><input type=checkbox name=guess></td></tr>
-->
    <tr><td>Show full whois info</td><td><input type=checkbox name=showall<?php if ($_REQUEST['showall']) { print " checked"; } ?>></td></tr>
    <tr><td colspan=2 align=center><input type=submit value="Lookup"></td></tr>
   </table>
  </form>
<?php
print_footer();
?>
