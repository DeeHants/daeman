<?php
function lookup($domain, $server = "") {
  $timeout = 30;
  $result = "";

  if ($server == "") {
    $parts = explode(".", $domain);
    $whoisserver = $parts[count($parts) - 1] . ".whois-servers.net";
//    $host = gethostbyname($whoisserver);
    $host = $whoisserver;
  } else {
    $host = $server;
  }
  flush();

  $whoisSocket = fsockopen($host, 43, $errno, $errstr, $timeout);
  if ($whoisSocket) {
    fputs($whoisSocket, $domain . "\015\012");
    while (!feof($whoisSocket)) {
      $result .= fgets($whoisSocket,128); // . "\n";
    }
    fclose($whoisSocket);
  }
  return $result;
}

function parse_whois($data) {
  $info = array();
  $lines = explode("\n", $data);
  for ($lineid = 0; $lineid < count($lines); $lineid++) {
    $line = trim($lines[$lineid]);
//    print "|" . $line . "|<br>\n";
    if ($next != "") {
      $info[$next] = $line;
      switch ($next) {
      case "registrant.address1": $next = "registrant.address2"; break;
      case "registrant.address2": $next = "registrant.address3"; break;
      case "registrant.address3": $next = "registrant.address4"; break;
      case "registrant.address4": $next = "registrant.address5"; break;
      case "registrant.address5": $next = "registrant.address6"; break;
      case "agent.name":
	if (strpos($line, "[Tag = ") > 0) {
          $info["agent.tag"] = substr($line, strpos($line, "[Tag = ") + 7, -1);
          $info["agent.name"] = substr($line, 0, strpos($line, "[Tag = ") - 1);
        }
        $next = ""; break;
      default: $next = "";
      }
      
    } else {
      if (substr($line, 0, 12) == "No match for") { $info["registered"] = 0; }
      if ($line == "Domain Name:") { $info["registered"] = 1; $next = "domain"; }
      if (substr($line, 0, 12) == "Domain Name:") { $info["registered"] = 1; $info["domain"] = trim(substr($line, 12)); }
      if ($line == "Registrant:") { $next = "registrant.name"; }
      if (substr($line, 0, 11) == "Trading As:") { $info["registrant.company"] = trim(substr($line, 11)); }
      if ($line == "Registrant's Address:") { $next = "registrant.address1"; }
      if ($line == "Registrant's Agent:") { $next = "agent.name"; }
      if (substr($line, 0, 10) == "Registrar:") { $info["agent.name"] = trim(substr($line, 10)); }
      if (substr($line, 0, 13) == "Whois Server:") { $info["agent.whois"] = trim(substr($line, 13)); }
      if (substr($line, 0, 4) == "URL:") { $info["agent.url"] = trim(substr($line, 4)); }
      if (substr($line, 0, 13) == "Referral URL:") { $info["agent.url"] = trim(substr($line, 13)); }
      if (substr($line, 0, 14) == "Registered on:") { $info["date.registered"] = strtotime(trim(substr($line, 14))); }
      if (substr($line, 0, 14) == "Creation Date:") { $info["date.registered"] = strtotime(trim(substr($line, 14))); }
      if (substr($line, 0, 13) == "Renewal Date:") { $info["date.renewal"] = strtotime(trim(substr($line, 13))); }
      if (substr($line, 0, 16) == "Expiration Date:") { $info["date.renewal"] = strtotime(trim(substr($line, 16))); }
      if (substr($line, 0, 13) == "Last updated:") { $info["date.updated"] = strtotime(trim(substr($line, 13))); }
      if (substr($line, 0, 13) == "Updated Date:") { $info["date.updated"] = strtotime(trim(substr($line, 13))); }
      if ($line == "Registration Status:") { $next = "status"; }
      if (substr($line, 0, 7) == "Status:") { $info["status"] = trim(substr($line, 7)); }
      if ($line == "--") { $next = "regtistrar"; }
    }
  }
  return $info;
}
?>
