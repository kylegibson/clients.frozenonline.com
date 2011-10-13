<?
function parse_expire($expire) {
  $d = date_parse_from_format("Y-m-d.H", $expire);
  return mktime($d["hour"], 0, 0, $d["month"], $d["day"], $d["year"]);
}

function format_expire($expire_secs) {
  $t = date_default_timezone_get();
  date_default_timezone_set("UTC");
  $e = date("Y-m-d H:i e", $expire_sec);
  date_default_timezone_set($t);
  return $e;
}

function expire_time_remaining($expire_delta) {
  $time_remaining = array();
  if ($expire_delta > 0) {
    $days = intval($expire_delta / 86400);
    $hours = intval(($expire_delta % 86400) / 3600);
    if($days > 0) 
      $time_remaining[] = "$days days, ";
    if($hours > 1) 
      $time_remaining[] = "$hours hours";
    elseif($days < 0 && $hours == 1) 
      $time_remaining[] = "less than 2 hours";
    elseif($days < 0) 
      $time_remaining[] = "less than 1 hour";
  } else {
    $time_remaining[] = "no time left";
  }
  return implode(", ", $time_remaining);
}
?>
