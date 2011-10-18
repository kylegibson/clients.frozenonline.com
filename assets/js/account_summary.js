var flot_hover_previous_point = null;

function plot_hover_tooltip(fn) {
  return function (evt, pos, item) {
    if(item) {
      if(flot_hover_previous_point != item.datapoint) {
        flot_hover_previous_point = item.datapoint;
        fn($("#plot_tooltip"), evt, pos, item);
      }
    } else {
      $("#plot_tooltip").hide();
      flot_hover_previous_point = null;
    }
  };
}

$(function() {

  var tick_formatter = function(val, axis) {
    if (val < 0) return "";
    if (val > 1000000000)
      return (val / 1000000000).toFixed(axis.tickDecimals) + " GiB";
    if (val > 1000000)
      return (val / 1000000).toFixed(axis.tickDecimals) + " MiB";
    if (val > 1000)
      return (val / 1000).toFixed(axis.tickDecimals) + " KiB";
    return val.toFixed(axis.tickDecimals) + " B";
  };

  var init_chart = function(json, dst) {
    var keys = {};
    var ticks = [];
    var c = 1;

    for(var i in json.data) {
      keys[i] = [];
    }

    for(var i in json.columns) {
      var date = json.columns[i];
      ticks.push([c, date]);
      for(var key in keys) {
        keys[key].push([c, json.data[key][date]]);
      }
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
        barWidth: 0.2,
        align: "left"
      },
      xaxis: { 
        min: 0.25,
        max: c,
        ticks: ticks
      },
      yaxis: {
        tickFormatter: tick_formatter
      },
      grid: {
        hoverable: true,
        clickable: true
      }
    };

    var pdata = [];
    var i = 1;
    for(var key in keys) {
      pdata.push({
        data: keys[key],
        bars: { order: i },
        label: key
      });
      i++;
    }
    $.plot(dst, pdata, popts);
    dst.bind('plothover', plot_hover_tooltip(
      function(tooltip, evt, pos, item) {
        tooltip.css({
          left: pos.pageX+10,
          top: pos.pageY+15
        });
        tooltip.text(item.series.label + ": " + item.datapoint[1]);
        tooltip.show();
      }
    ));
  };

  $.getJSON('/json/xfer', function(json) {
    init_chart(json.monthly, $('.bandwidth-usage-monthly'));
    init_chart(json.daily, $('.bandwidth-usage-daily'));
    init_chart(json.hourly, $('.bandwidth-usage-72-hours'));
  });

  var i_reboot = $(".reboot input[value=Reboot]");
  var confirm_d = $(".reboot .are-you-sure");
  var in_progress = $(".reboot .in-progress");
  i_reboot.click(function() {
    i_reboot.fadeOut(function() {
      confirm_d.fadeIn();
    });
  });
  $(".reboot input[value=No]").click(function() {
    confirm_d.fadeOut(function() {
      i_reboot.fadeIn();
    });
  });
  $(".reboot input[value=Yes]").click(function() {
    confirm_d.fadeOut(function() {
      in_progress.fadeIn();
      $.get('<?=$reboot_url?>', function(data) {
        in_progress.text(data);
      });
    });
  });
});
