<?
if(!(isset($page) && isset($_SESSION["system"]) && isset($_SESSION["passwd"]))) {
  header("Location: /");
  exit;
}
$system = $_SESSION["system"];
$passwd = $_SESSION["passwd"];

$d = date_parse_from_format("Y-m-d.H", $sub_info["expire"]);
$expire_sec = mktime($d["hour"], 0, 0, $d["month"], $d["day"], $d["year"]);
date_default_timezone_set("UTC");
$expiration = date("Y-m-d H:i e", $expire_sec);
$expire_delta = $expire_sec - time();

$status = "";
$time_remaining = "";
if ($expire_delta > 0) {
  $status .= "active";
  $days = intval($expire_delta / 86400);
  $hours = intval(($expire_delta % 86400) / 3600);
  if($days > 0) 
    $time_remaining .= "$days days, ";
  if($hours > 1) 
    $time_remaining .= "$hours hours";
  elseif($days < 0 && $hours == 1) 
    $time_remaining .= "less than 2 hours";
  elseif($days < 0) 
    $time_remaining .= "less than 1 hour";
} else {
  $time_remaining = "no time left";
  $status .= "expired - please renew!";
}

$card = array(
  "System" => $system,
  "Status" => $status,
  "Memory" => $system_info["memory"]."MB",
  "CPU Cores" => $system_info["cpu_cores"],
  "Bandwidth" => $system_info["xfer"]."GB",
  "VNC Host" => "vnc.".$system_info["provider"],
  "VNC Port" => $system_info["vncport_external"],
  "Contact email" => $sub_info["email"],
  "Expiration" => $expiration,
  "Time Remaining" => $time_remaining,
);
?>
<script type="text/javascript">
$(function() {
  $(".reboot input").click(function() {
    /* $("reboot input[value=Reboot]").hide(); */
    $(".reboot .are-you-sure").show();
  });
});
</script>
<div class="box summary">
  <div class="inner">
    <h2>Account Summary</h2>
    <div class="reboot right">
      <div class="hidden are-you-sure">
        <span>Are you sure you want to reboot?</span> 
      </div>
      <input type="button" value="Reboot"/>
    </div>
    <? foreach($card as $dt => $dd): ?>
    <dl><dt><?=$dt?></dt><dd><?=$dd?></dd></dl>
    <? endforeach;?>
  </div>
</div>

