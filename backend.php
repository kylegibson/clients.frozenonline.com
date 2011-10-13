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
  $menu[] = "Summary:/f/summary";
  $menu[] = "Logout:/f/logout";
  $page = isset($pages[$request]) ? $pages[$request] : $pages["summary"];
  $title = isset($titles[$request]) ? $titles[$request] : $titles["summary"];
  require_once(ROOT."/helpers/parse_expire.php");
}
?>
