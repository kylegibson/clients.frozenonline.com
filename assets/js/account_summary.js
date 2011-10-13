$(function() {
  var i_reboot = $(".reboot input[value=Reboot]");
  var confirm_d = $(".reboot .are-you-sure");
  var in_progress = $(".reboot .in-progress");
  i_reboot.click(function() {
    i_reboot.fadeOut();
    confirm_d.fadeIn();
  });
  $(".reboot input[value=No]").click(function() {
    confirm_d.fadeOut();
    i_reboot.fadeIn();
  });
  $(".reboot input[value=Yes]").click(function() {
    confirm_d.fadeOut();
    in_progress.fadeIn();
    $.get('<?=$reboot_url?>', function(data) {
      in_progress.text(data);
    });
  });
});
