<?php
function execute($sql) {
  global $DBName, $DBHost, $DBUser, $DBPass, $DBError;

  # Connect to the server
  $link = mysql_pconnect ($DBHost, $DBUser, $DBPass);

  # Select database
  mysql_select_db ($DBName, $link);

  # Execute query
  $rst = mysql_query ($sql, $link);

  $DBError = mysql_error($link);

  # If it's an insert query, return the insert key
  if (ereg ("^[I,i]", $sql)) {
    return mysql_insert_id ($link);
  }

  # If it's an update query, return true or false
  if (ereg ("^[U,u]", $sql)) {
    if ($rst) {
      return true;
    }else {
      return false;
    }
  }

  # If it's a delete query, return true or false
  if (ereg ("^[D,d]", $sql)) {
    if ($rst) {
      return true;
    }else {
      return false;
    }
  }

  # Put the results into an array
  $results = array ();
  for ($i = 0; $i < @mysql_num_rows($rst); $i++) {
    $results[$i] = @mysql_fetch_array($rst);
  }

  # Free up the results
  @mysql_free_result($rst);

#  # Disconnect from the database
#  @mysql_close($link);

  return $results;
}

function mysql_to_date($datestr) {
  list($date, $time) = split(' ', trim($datestr));
  list($year, $month, $day) = split('-', $date);
  list($hours, $mins, $seconds) = split(':', $time);
  $unixtime = mktime($hours, $mins, $seconds, $month, $day, $year);
  return $unixtime;
}
?>
