<?php
require("../common.inc");
require("whois.inc");
checkstatus();

print_header("Domain lookup");
print "  <h3><a href=\"../index.php\">Home</a> - <a href=\"index.php\">Tools</a> - Domain lookup</h3>\n";

if (isset($_REQUEST['action'])){
  if ($_REQUEST['action'] == "lookup") {

    $domains[] = $_REQUEST['domain'];
//    $domains[] = "earlsoft.com";
//    $domains[] = "earlsoftsucks.co.uk";
//    $domains[] = "earlsoftsucks.com";

    for ($domainid = 0; $domainid < count($domains); $domainid++) {
      print "  <p id=\"pleasewait" . $domains[$domainid] . "\">Please wait, looking up " . $domains[$domainid] . "...</p>\n";
      $data = lookup($domains[$domainid]);
      print "  <script>document.getElementById(\"pleasewait" . $domains[$domainid] . "\").style.display=\"none\";</script>\n";

      if ($data != "") {
        $info = parse_whois($data);
        if ($info['registered'] == 1) {
          print "  <p>" . $domains[$domainid] . " is currently registered";
          if ($info["registrant.name"] != "") {
            print " to " . $info["registrant.name"];
            if ($info["registrant.company"] != "" && $info["registrant.company"] != $info["registrant.name"]) {
              print " of " . $info["registrant.company"];
            }
          }
          print " by ";
          if ($info["agent.url"] != "") { print "<a href=\"" . $info["agent.url"] . "\">"; }
          print $info["agent.name"];
          if ($info["agent.url"] != "") { print "</a>"; }
          if ($info["agent.tag"] != "") { print " (" . $info["agent.tag"] . ")"; }

/*
    [status] => ACTIVE
*/        

          print ".<br>It was registered on " . date("j/n/Y", $info["date.registered"]) . " and expires on " . date("j/n/Y", $info["date.renewal"]) . ".</p>\n";
        } else {
          print "  <p>" . $info["registrant.name"] . " is currently available.</p>\n";
        }
//        print "  <pre>"; print_r($info); print "</pre>";
      } else {
        print "  <p>There was an error looking up " . $info["registrant.name"] . ".</p>\n";
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
    <tr><td colspan=2 align=center><input type=submit value="Lookup"></td></tr>
   </table>
  </form>
<?php
print_footer();
?>
