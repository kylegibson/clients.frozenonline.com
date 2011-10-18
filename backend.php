<?
define('ROOT', $_SERVER["DOCUMENT_ROOT"]);
require_once(ROOT."/config.php");

$request = basename($_SERVER["REQUEST_URI"]);

if($request == "logout") {
  require_helper("logout");
  logout();
  exit;
}

$page = $pages["login"];
$title = $titles["login"];

$system = $passwd = null;

if(isset($_SESSION[$k_system]) && isset($_SESSION[$k_passwd])) {
  $system = $_SESSION[$k_system];
  $passwd = $_SESSION[$k_passwd];
}
if(isset($_POST[$k_system]) && isset($_POST[$k_passwd])) {
  $system = strtolower(trim($_POST[$k_system]));
  $passwd = trim($_POST[$k_passwd]);
}

if($system != null && $passwd != null) {
  require_once("/home/frozen/phpcassa.php");
  $error = "Login failed. User does not exist or incorrect password.";
  try {
    $conn = cassandra_connect(KS_FO);
    $sys = new ColumnFamily($conn, CF_SYSTEMS);
    $system_info = $r = $sys->get($system);
    if($r["status"] == "assigned" && $r["subscription"] != ""
        && hash("sha256", ".:$system.$passwd:.") == $r["slogin_www"]) {
      $_SESSION[$k_system] = $system;
      $_SESSION[$k_passwd] = $passwd;
      unset($error);
      $logged_in = true;
      $sub = new ColumnFamily($conn, CF_SUBSCRIPTIONS);
      $sub_info = $sub->get($system_info["subscription"]);
    }
  } catch (Exception $e) { }
}
if($logged_in) {
  if($request == "reboot") { // Ajax callback
    if($system_info["flag_allow_web_reset"] == 0) {
      echo "That system cannot be reset from the web";
    } else {
      try {
        $sys->insert($system, array("control" => "reset"));
        echo "Reboot is in progress";
      } catch (Exception $e) { 
        echo "An error occured";
      }
    }
    exit;
  }
  if($request == "xfer") {
    ob_start('ob_gzhandler');
    require_helper("bandwidth");
    $start = strtotime($sub_info["start"]);
    $monthly_start = $start_of_year = strtotime(date("Y-01-01"));
    $daily_start = $_30_days_ago = strtotime("30 days ago"):
    $hourly_start = $_72_days_ago = strtotime("72 hours ago");
    if($start > $monthly_start) {
      $monthly_start = $start;
    }
    if($start > $_30_days_ago) {
      $daily_start = $start;
    }
    if($start > $_72_days_ago) {
      $hourly_start = $start;
    }
    $monthly_dates = get_date_span($monthly_start, time(), "monthly");
    $monthly = get_xfer_metrics($system, $monthly_dates, "monthly");
    $daily_dates = get_date_span($daily_start, time(), "daily");
    $daily = get_xfer_metrics($system, $daily_dates, "daily");
    $hourly_dates = get_date_span($hourly_start, time(), "hourly");
    $hourly = get_xfer_metrics($system, $hourly_dates, "hourly");
      $out = array(
      "monthly" = array("column" => $monthly_dates, "data" => $monthly),
      "daily" = array("column" => $hourly_dates, "data" => $daily),
      "hourly" = array("column" => $daily_dates, "data" => $hourly),
    );
    echo json_encode($out);
    exit;
  }
  $menu[] = "Summary:/f/summary";
  $menu[] = "Logout:/f/logout";
  $page = isset($pages[$request]) ? $pages[$request] : $pages["summary"];
  $title = isset($titles[$request]) ? $titles[$request] : $titles["summary"];
}
?>
