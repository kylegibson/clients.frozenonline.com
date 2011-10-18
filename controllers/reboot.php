<?
function reboot_init() {
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
  return false;
}
?>
