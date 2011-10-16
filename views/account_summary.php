<?
if(!(isset($page) && isset($_SESSION["system"]) && isset($_SESSION["passwd"]))) {
  header("Location: /");
  exit;
}
$system = $_SESSION["system"];
$passwd = $_SESSION["passwd"];

if(substr($system_info["subscription"], 0, 2) == "S-") {
  $expiration = "Paypal Subscription";
  $time_remaining = "N/A";
  $expire_delta = 1;
} else {
  $expire_sec = parse_expire($sub_info["expire"]);
  $expiration = format_expire($expire_sec);
  $expire_delta = $expire_sec - time();
  $time_remaining = expire_time_remaining($expire_delta);
}

$status = $expire_delta > 0 ? "active" : "expired - please renew!";

$card = array(
  "System" => $system,
  "Status" => $status,
  "Memory" => $system_info["memory"]."MB",
  "CPU Cores" => $system_info["cpu_cores"],
  "Bandwidth" => $system_info["xfer"]."GB",
  "VNC Host" => "vnc.".$system_info["provider"],
  "VNC Port" => $system_info["vncport_external"],
  "Contact email" => $sub_info["email"],
  "Service Start" => $sub_info["start"],
  "Expiration" => $expiration,
  "Time Remaining" => $time_remaining,
);
$reset_pending = isset($system_info["control"]) && $system_info["control"] == "reset";
?>
<script src="/assets/js/jquery.flot-20110621.js" type="text/javascript"> </script>
<script src="/assets/js/jquery.flot.orderBars.js" type="text/javascript"> </script>
<script src="/assets/js/account_summary.js" type="text/javascript"> </script>
<div id="plot_tooltip"></div>
<div class="box summary">
  <div class="inner">
    <h2>Account Summary</h2>
    <div class="reboot right">
<?if($reset_pending):?>
      <div class="in-progress">Reboot is in progress</div>
<?else:?>
      <input type="button" value="Reboot"/>
<?endif?>
      <div class="hidden in-progress">Submitting reboot request</div>
      <div class="hidden are-you-sure">
        <div>Are you sure you want to reboot?</div> 
        <div class='right'>
            <input type="button" value="Yes"/>
            <input type="button" value="No"/>
        </div>
      </div>
    </div>
    <? foreach($card as $dt => $dd): ?>
    <dl><dt><?=$dt?></dt><dd><?=$dd?></dd></dl>
    <? endforeach;?>
    <h2>Bandwidth Usage</h2>
    <div class="bandwidth-usage-72-hours"></div>
    <div class="bandwidth-usage-daily"></div>
    <div class="bandwidth-usage-monthly"></div>
  </div>
</div>

