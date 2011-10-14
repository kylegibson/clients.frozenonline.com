<?
function get_date_format($period) {
  $formats = array(
    "monthly"  => "Y-m",
    "daily"    => "Y-m-d",
    "hourly"   => "Y-m-d.H"
    );
  if(array_key_exists($period, $formats))
    return $formats[$period];
  return "";
}

function get_date_step($period) {
  $steps = array(
    "monthly"  => "+1 month", 
    "daily"    => "+1 day", 
    "hourly"   => "+1 hour",
    );
  if(array_key_exists($period, $steps))
    return $steps[$period];
  return "";
}

function get_date_span($start, $stop, $period) {
  $format = get_date_format($period);
  $step = get_date_step($period);
  $dates = array();
  while($stop >= $start) {
    $dates[] = date($format, $start);
    $start = strtotime($step, $start);
  }
  return $dates;
}

function get_current($period) {
  return date(get_date_format($period));
}

function get_xfer_metrics($system, $dates, $date_step) {
  $metrics = array("tx_bytes", "rx_bytes", "vnc-tx_bytes", "vnc-rx_bytes");
  // $current = get_current($date_step);
  $conn = cassandra_connect(KS_METRICS);
  $agg = new ColumnFamily($conn, CF_AGG);
  $keys = array();
  $totals = array();
  foreach($metrics as $metric) {
    $keys[] = "$system-$metric-$date_step";
    $totals[$metric] = array();
  }
  $result = $agg->multiget($keys, $dates);
  foreach($metrics as $metric) {
    $xfer = $result["$system-$metric-$date_step"];
    foreach($dates as $date) {
      $v = array_key_exists($date, $xfer) ? $xfer[$date] : "";
      $totals[$metric][$date] = $v;
    }
  }
  return $totals;
}
?>
