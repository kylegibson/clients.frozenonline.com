var flot_hover_previous_point = null;

function getOffset() {
  var d = new Date;
  return 60 * d.getTimezoneOffset();
}

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

function do_everything() {
  initialize_system_metric_table_and_plot();
  initialize_data_transfer_plot();
}

function initialize_data_transfer_plot() {

  var plot = {
    system: null,
    date: null,
    json: {}
  };

  plot.options = {
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

  plot.options.yaxis.tickFormatter = function(val, axis) {
    if (val < 0) return "";
    if (val > 1000000)
      return (val / 1000000).toFixed(axis.tickDecimals) + " MiB";
    if (val > 1000)
      return (val / 1000).toFixed(axis.tickDecimals) + " KiB";
    return val.toFixed(axis.tickDecimals) + " B";
  };

  var get_selected_date = function() { 
    return $('#network_transfer_date').datepicker( "getDate" );
  };

  var get_selected_date_yyyy_mm = function() {
    return get_selected_date().format("yyyy-mm");
  };

  var all_systems = "* all *";

  var display_plot = function() {

    var system = $('#network_transfer_systems_list').val();
    var period = $('#network_transfer_period').val();

		var type = $("#network_type").val();	

    var date = get_selected_date();
    var selected_month = date.format("yyyy-mm");
    var selected_day = date.format("yyyy-mm-dd");
    var data = plot.json[selected_month+type];
		var systems = data["systems"];

    var rx = [];
    var tx = [];
    var ticks = [];
    var key = "";

		var rev = {};
		for (var i in systems) {
			rev[systems[i]] = i;
		}

    var c = 1;
    if (system == all_systems) {
      if (period == "month") {
        // X-AXIS = selected month total
        // Y-AXIS = all systems
        key = selected_month;
      } else { // period == "day"
        // X-AXIS = selected day total
        // Y-AXIS = all systems
        key = selected_day;
      }
      var d = data[key];
      for(var i in d) {
        var sys = "";
        for(var j in systems[i]) {
          sys += systems[i][j] + "<br>";
        }
				// var sys = systems[i];
        ticks.push([c, sys]);
        var e = d[i];
        rx.push([c, e[0]]);
        tx.push([c, e[1]]);
        c++;
      }
    } else {
      if (period == "month") {
        // X-AXIS = days
        // Y-AXIS = single system
        var year = date.getFullYear();
        var month = date.getMonth();
        var days_in_month = (new Date(year, month+1, 0)).getDate();

        for(var j = 1; j <= days_in_month; j++) {
          date.setDate(j);
          var t = date.format("yyyy-mm-dd");
          ticks.push([c, date.format("mm-dd")]);

          var rxb = 0;
          var txb = 0;
					var i = rev[system]
          if(t in data && i in data[t]) {
            var e = data[t][i];
            rxb = e[0];
            txb = e[1];
          }
          rx.push([c, rxb]);
          tx.push([c, txb]);
          c++;
        }
        key = system + " " + selected_month;
      } else { // period == "day"
        // X-AXIS = hours
        // Y-AXIS = single system
        for(var j = 0; j < 24; j++) {
          date.setHours(j);
          var t = date.format("yyyy-mm-dd-HH");
          ticks.push([c, date.format("dd-HH")]);

          var rxb = 0;
          var txb = 0;
					var i = rev[system];
          if(t in data && i in data[t]) {
            var e = data[t][i];
            rxb = e[0];
            txb = e[1];
          }
          rx.push([c, rxb]);
          tx.push([c, txb]);
          c++;
        }
        key = system + " " + selected_day;
      }
    }

    var plot_data = [{
      data: rx, 
      bars: { order: 1 },
      label: key + " rx",
      color: "#00FF00"
    }, {
      data: tx,
      bars: { order: 2 },
      label: key + " tx",
      color: "#0000FF"
    }];
    plot.options.xaxis.max = c;
    plot.options.xaxis.ticks = ticks;
    $.plot($("#network_transfer_plot"), plot_data, plot.options);
  };

  var load_json = function() {
		var type = $("#network_type").val();	
    var yyyy_mm = get_selected_date_yyyy_mm();
		var plot_key = yyyy_mm + type;
    if (plot_key in plot.json) {
      display_plot();
    } else {
			var path = "/ajax/xfer.php/"+yyyy_mm+"-"+type+".json";
      $.getJSON(path, function(json) {
        if(json == null) {
          $('#network_transfer_date').datepicker("setDate", new Date);
          return;
        }
        plot.json[plot_key] = json;
        var list = $('#network_transfer_systems_list');
        list.find('option').remove();
        list.append($("<option>").text(all_systems));
        for(var i in json["systems"]) {
          list.append($("<option>").text(json["systems"][i]));
        }
        list.attr("selectedIndex", "0");
        list.change();
      });
    }
  }

  $('#network_transfer_date').datepicker({
    maxDate: '0', // Can't go into the future
    minDate: new Date("11/1/2010"), 
    dateFormat: 'yy-mm-dd',
    changeMonth: true,
    changeYear: true,
    onClose: load_json
  });

  $('#network_transfer_systems_list').change(display_plot);
  $('#network_transfer_date').datepicker("setDate", new Date); 

  $.each(['month', 'day'], function(i, v) {
    $('#network_transfer_period').append($("<option>").text(v));
  });

  $('#network_transfer_period').change(load_json);
  $('#network_transfer_period').attr("selectedIndex", "0");

  $('#network_type').change(load_json);
  $('#network_type').attr("selectedIndex", "0");

  // This sets the initial loading into motion
  $('#network_transfer_period').change();

  $("#network_transfer_plot").bind("plothover", plot_hover_tooltip(
  function(tooltip, evt, pos, item) {
    var formatter = plot.options.yaxis.tickFormatter;
    var y = formatter(item.datapoint[1], item.series.yaxis);
    var label = item.series.xaxis.ticks[item.dataIndex].label;
    label = label.replace(/<br>/g, "");
    tooltip.css({
      left: pos.pageX+10,
      top: pos.pageY+15
    });
    tooltip.text(label + ": " + y);
    tooltip.show();
  }));

  $("#network_transfer_plot").bind("plotclick", function(evt, pos, item) {
    if(item) {
      var list_system = $('#network_transfer_systems_list').val();
      var period = $('#network_transfer_period').val();
      var click_label = item.series.xaxis.ticks[item.dataIndex].label;
      click_label = click_label.replace(/<br>/g, "");
      if (list_system == all_systems) {
        $('#network_transfer_systems_list').val(click_label);
        $('#network_transfer_systems_list').change();
      } else if (period == "month") {
        var m = click_label.match(/(\d\d)-(\d\d)/);
        var date = $('#network_transfer_date').datepicker("getDate");
        if (m[2].charAt(0) == "0") date.setDate(parseInt(m[2].charAt(1)));
        else date.setDate(parseInt(m[2]));
        $('#network_transfer_date').datepicker("setDate", date);
        $('#network_transfer_period').val("day");
        $('#network_transfer_period').change();
      }
    }
  });
}

function initialize_system_metric_table_and_plot() {
  var tz_offset_ms = 1000 * getOffset();
  var ten_secs_ms = 10000;

  /**
   * Display the current time
   */
  setInterval(function() {
    $('#current_time').text((new Date).format("isoDateTime"));
  }, 1000);

  /**
   * Populate column headers for the metrics table
   * and the drop down
   */
  var populate_headers = function (table, select) {
    var system_metric_column_headers = ["system", "address", "load1", "load5", "load15", "%user", "%sys", "%wait", "mem kB", "%mem", "swap kB", "%swap"];
    var row = $("<tr>");
    var skip = /system|address/;
    table.append($("<thead>").append(row));
    $.each(system_metric_column_headers, function(i, v) {
      row.append($("<th>").text(v));
      if (!v.match(skip)) {
        select.append($("<option>").text(v));
      }
    });
  };
  populate_headers($('#system_metric_table'), $('#system_metric_column_list'));

  /**
   * Cache used to store JSON data
   */
  var system_metric_cache = {};

  /**
   * The metric data table
   */
  var data_table = $('#system_metric_table').dataTable({
    "bFilter" : false,
    "bInfo"   : false,
    "bPaginate" : false
  });

  var system_metric_plot_opts = {
    legend: {
      show: true,
      position: "nw",
      backgroundOpacity: 0.2
    },
    xaxis: { 
      mode: "time", 
      min: (new Date).getTime() - ten_secs_ms - tz_offset_ms
    },
    y2axis: { min: 0 },
    lines: { show: true },
    points: { show: true },
    shadowSize: 0,
    grid: { 
      hoverable: true, 
      clickable: true 
    }
  };

  /**
   * Function to generate the system metric plot
   * for the selected metric/column
   */
  var fn_system_metric_generate_plot = function() {

    // Get selected data
    var column_index = $("#system_metric_column_list").attr("selectedIndex") + 2;
    var data = {};
    var cache = system_metric_cache;
    for(var time in cache) {
      var data_set = cache[time]
      for (var i in data_set) {
        var row = data_set[i];
        var system = row[0];
        var offset_time = parseInt(time) - tz_offset_ms;
        var entry = [offset_time, parseFloat(row[column_index])];
        if (system in data) {
          data[system].push(entry);
        } else {
          data[system] = [entry];
        }
      }
    }

    // Generate display data
    var display_data = [];
    for(var system in data) {
      display_data.push({
        yaxis: 2,
        label: system,
        data: data[system]
      });
    }

    // Configure opts
    var last_reported_time = parseInt($('#system_metric_data_list').val());
    var opts = system_metric_plot_opts;
    opts.xaxis.max = last_reported_time + ten_secs_ms - tz_offset_ms;

    // Generate the plot
    $.plot($('#system_metric_plot'), display_data, opts);
  };

  /**
   * Function to change the displayed data based
   * on what is selected in the drop down
   */
  var fn_system_metric_display_table_data = function() {
    var selection = $('#system_metric_data_list').val();
    data_table.fnReloadData(system_metric_cache[selection]);
  };

  /**
   * Function to handle incoming JSON for the metric table
   */
  var fn_system_metric_json_recv = function(json) {
    var now = new Date;
    var ntime = now.getTime();

    system_metric_cache[ntime] = json.aaData;
    var opt = $("<option>").text(now.format("isoDateTime"));
    opt.attr("value", ntime);

    var select = $('#system_metric_data_list');
    select.prepend(opt).attr("selectedIndex", "0");

    // This causes the table to be loaded
    select.change();

    fn_system_metric_generate_plot();
  };

  /**
   * Function to perform the JSON query for the metric table
   */
  var fn_system_metric_fetch_json = function() {
    var settings = data_table.dataTableSettings[0];
    settings.fnServerData("/ajax/data.php", null, fn_system_metric_json_recv);
  };
  fn_system_metric_fetch_json();

  $('#system_metric_plot').bind("plothover", plot_hover_tooltip(
    function(tooltip, evt, pos, item) {
      tooltip.css({
        left: pos.pageX+10,
        top: pos.pageY+15
      });
      tooltip.text(item.series.label + ": " + item.datapoint[1]);
      tooltip.show();
    }
  ));

  setInterval(fn_system_metric_fetch_json, 60000);
  $("#system_metric_column_list").change(fn_system_metric_generate_plot);
  $('#system_metric_data_list').change(fn_system_metric_display_table_data);

  $('#system_metric_options input[value*=legend]').click(function() {
    system_metric_plot_opts.legend.show = !system_metric_plot_opts.legend.show;
    fn_system_metric_generate_plot();
  });
  $('#system_metric_options input[value*=points]').click(function() {
    system_metric_plot_opts.points.show = !system_metric_plot_opts.points.show;
    fn_system_metric_generate_plot();
  });
  $('#system_metric_options input[value*=lines]').click(function() {
    system_metric_plot_opts.lines.show = !system_metric_plot_opts.lines.show;
    fn_system_metric_generate_plot();
  });
  $('#system_metric_options input[value*=Query]').click(fn_system_metric_fetch_json);
}

