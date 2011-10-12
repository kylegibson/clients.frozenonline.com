<?
$page = "views/login_form.php";
$system = null;
$passwd = null;
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
      $page = "views/account_summary.php";
    }
  } catch (Exception $e) { }
}
?>
