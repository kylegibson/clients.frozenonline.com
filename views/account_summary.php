<?
if(!(isset($page) && isset($_SESSION["system"]) && isset($_SESSION["passwd"]))) {
  header("Location: /");
  exit;
}
$system = $_SESSION["system"];
$passwd = $_SESSION["passwd"];
$card = array(
  "System" => $system,
  "Memory" => $system_info["memory"],
  "CPU Cores" => $system_info["cpu_cores"],
  "VNC Port" => $system_info["vncport_external"],
);
?>
<div class="box summary">
  <div class="inner">
    <h2>Account Summary</h2>
    <? foreach($card as $dt => $dd): ?>
    <dl><dt><?=$dt?></dt><dd><?=$dd?></dd></dl>
    <? endforeach;?>
  </div>
</div>
