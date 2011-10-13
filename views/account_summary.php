<?
if(!(isset($page) && isset($_SESSION["system"]) && isset($_SESSION["passwd"]))) {
  header("Location: /");
  exit;
}
$system = $_SESSION["system"];
$passwd = $_SESSION["passwd"];
?>
<div class="box summary">
  <h2>Account Summary</h2>
  <p>System: <?=$system?></p>
</div>
