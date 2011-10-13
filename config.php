<?
$logged_in = false;
$header = "views/header.php";
$reboot_url = "/f/reboot";
$k_system = "system";
$k_passwd = "passwd";
$menu = array("Home:/");
$pages = array(
"login" => "views/login_form.php",
"summary" => "views/account_summary.php",
);

$titles = array(
"login" => "Login",
"summary" => "Account Summary",
);

function require_helper($helper) {
  require_once(ROOT."/".$helper.".php");
}
?>
