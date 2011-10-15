$(function() {

  $.getJSON('/json/xfer', function(json) {
    var rx_bytes = [];
    var tx_bytes = [];
    var ticks = [];

    var c = 1;
    for(var date in json.columns) {
      ticks.push([c, date]);
      rx_bytes.push([c, json.data.rx_bytes[date]]);
      tx_bytes.push([c, json.data.tx_bytes[date]]);
      c++;
    }

    var popts = {
      legend: { 
        backgroundOpacity: 0
      },
      zoom: { interactive: true },
      pan: { interactive: true },
      lines: { show: false },
      bars: {
        show: true, 
        lineWidth: 0,
        barWidth: 0.35,
        align: "left"
      },
      xaxis: { 
        min: 0.25,
        max: null,
        ticks: null
      },
      yaxis: {
        tickFormatter: null
      },
      grid: {
        hoverable: true,
        clickable: true
      }
    };
    popts.yaxis.tickFormatter = function(val, axis) {
      if (val < 0) return "";
      if (val > 1000000)
        return (val / 1000000).toFixed(axis.tickDecimals) + " MiB";
      if (val > 1000)
        return (val / 1000).toFixed(axis.tickDecimals) + " KiB";
      return val.toFixed(axis.tickDecimals) + " B";
    };

    popts.xaxis.max = c;
    popts.xaxis.ticks = ticks;

    var pdata = [{
      data: rx_bytes,
      bars: { order : 1 },
      label: "rx_bytes",
      color: "#0F0"
    }, {
      data: tx_bytes,
      bars: { order : 2 },
      label: "tx_bytes",
      color: "#00F"
    }];
    $.plot($('.bandwidth-usage-monthly'), pdata, popts);
  });

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
