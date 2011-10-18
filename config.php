<?
function require_helper($name) {
  require_once(ROOT."/helpers/".$name.".php");
}
function require_controller($name) {
  require_once(ROOT."/controllers/".$name.".php");
  $func = "{$name}_init";
  if(function_exists($func)) {
    if(!$func()) {
      exit;
    }
  }
}

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

?>
