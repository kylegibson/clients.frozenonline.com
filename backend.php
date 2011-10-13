<?
$request = basename($_SERVER["REQUEST_URI"]);
$reboot_url = "/f/reboot";

if($request == "logout") {
  $_SESSION = array();
  if (ini_get("session.use_cookies")) {
      $params = session_get_cookie_params();
      setcookie(session_name(), '', time() - 42000,
          $params["path"], $params["domain"],
          $params["secure"], $params["httponly"]
      );
  }
  session_destroy();
  header("Location: /");
  exit;
}

$pages = array(
"login" => "views/login_form.php",
"summary" => "views/account_summary.php",
);

$titles = array(
"login" => "Login",
"summary" => "Account Summary",
);

$page = $pages["login"];
$title = $titles["login"];
$header = "views/header.php";

$system = $passwd = null;
$k_system = "system";
$k_passwd = "passwd";
if(isset($_SESSION[$k_system]) && isset($_SESSION[$k_passwd])) {
  $system = $_SESSION[$k_system];
  $passwd = $_SESSION[$k_passwd];
}
if(isset($_POST[$k_system]) && isset($_POST[$k_passwd])) {
  $system = strtolower(trim($_POST[$k_system]));
  $passwd = trim($_POST[$k_passwd]);
}
$logged_in = false;
$menu = array("Home:/");
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
    echo "Reboot is in progress";
    exit;
  }
  $menu[] = "Summary:/f/summary";
  $menu[] = "Logout:/f/logout";
  $page = isset($pages[$request]) ? $pages[$request] : $pages["summary"];
  $title = isset($titles[$request]) ? $titles[$request] : $titles["summary"];
}
?>
