<?
$page = "views/login_form.php";
$system = null;
$passwd = null;
if(isset($_SESSION["system"]) && isset($_SESSION["pwd"])) {
  $system = $_SESSION["system"];
  $passwd = $_SESSION["passwd"];
}
if(isset($_POST["system"]) && isset($_POST["passwd"])) {
  $system = strtolower($_POST["system"]);
  $passwd = $_POST["passwd"];
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
      $_SESSION["system"] = $system;
      $_SESSION["passwd"] = $passwd;
      unset($error);
    }
  } catch (Exception $e) { }
}
?>
