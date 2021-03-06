<?php 
  require_once "charts/config.php";
  require_once "charts/funs.php";
  $network = Stats::network();
  
  require_once "lib/uptime.php";
  require_once "lib/memory.php";
  require_once "lib/cpu.php";
  require_once "lib/storage.php";
  require_once "lib/rbpi.php";
  $uptime = LibUptime::uptime();
  $ram = LibMemory::ram();
  $cpu = LibCPU::cpu();
  $cpu_heat = LibCPU::heat();
  $hdd = LibStorage::hdd();
  
function shell_to_html_table_result($shellExecOutput) {
	$shellExecOutput = preg_split('/[\r\n]+/', $shellExecOutput);

	// remove double (or more) spaces for all items
	foreach ($shellExecOutput as &$item) {
		$item = preg_replace('/[[:blank:]]+/', ' ', $item);
		$item = trim($item);
	}

	// remove empty lines
	$shellExecOutput = array_filter($shellExecOutput);

	// the first line contains titles
	$columnCount = preg_match_all('/\s+/', $shellExecOutput[0]);
	$shellExecOutput[0] = '<tr><th>' . preg_replace('/\s+/', '</th><th>', $shellExecOutput[0], $columnCount) . '</th></tr>';
	$tableHead = $shellExecOutput[0];
	unset($shellExecOutput[0]);

	// others lines contains table lines
	foreach ($shellExecOutput as &$item) {
		$item = '<tr><td>' . preg_replace('/\s+/', '</td><td>', $item, $columnCount) . '</td></tr>';
	}

	// return the build table
	return '<table class=\'table table-striped\'>'
				. '<thead>' . $tableHead . '</thead>'
				. '<tbody>' . implode($shellExecOutput) . '</tbody>'
			. '</table>';
}
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Raspberry Pi Status</title>
    <meta http-equiv="refresh" content="120" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="css/custom.css" rel="stylesheet" media="screen">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="../../assets/js/html5shiv.js"></script>
      <script src="../../assets/js/respond.min.js"></script>
    <![endif]-->
 
    <script type='text/javascript' src='https://www.google.com/jsapi'></script>
    <script type='text/javascript'>
      google.load('visualization', '1', {packages:['gauge']});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['Label', 'Value'],
            ['T', <?php echo Stats::temperature();?> ], //initial value from PHP
          ]);

		 var options = {
            height: 120,
            redFrom: 68, redTo: 85,
            yellowFrom:51, yellowTo: 68,
            greenFrom:0,greenTo:60,
            minorTicks: 5, min:0,max:85 
          };
		  
        var chart = new google.visualization.Gauge(document.getElementById('chart_nowmeter'));
        chart.draw(data, options);
		
		//Regular updates of the gauge
          setInterval(function () {
            var xhReq = new XMLHttpRequest();
            xhReq.onreadystatechange = function() {
              if(this.readyState!=4 || this.status != 200)
                return; //not ready / bad answer

              var temp = xhReq.responseText.trim();
              var data = google.visualization.arrayToDataTable([
                ['Label', 'Value'],
                ['T', parseFloat(temp) ], //new value attained by AJAX
              ]);
            
              chart.draw(data, options);
            }
            xhReq.open("GET", <?php echo Stats::temperatureURL();?>, true);
            xhReq.send(null);
          },1000);
      }
    </script>
    <script type='text/javascript'>
      google.load('visualization', '1', {packages:['gauge']});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Label', 'Value'],
          ['CPU', <?php echo Stats::cpuLoad();?>], //initial value from PHP  
        ]);

        var options = {
          height: 120,
          redFrom: 70, redTo: 100,
          yellowFrom:50, yellowTo: 70,
          greenFrom:0,greenTo:20,
          minorTicks: 5, min:0,max:100
        };

        var chart = new google.visualization.Gauge(document.getElementById('chart_cpunowmeter'));
        chart.draw(data, options);
		
		//Regular updates of the gauge        
        setInterval(function () {
          var xhReq = new XMLHttpRequest();
          xhReq.onreadystatechange = function() {
            if(this.readyState!=4 || this.status != 200)
              return; //not ready / bad answer

            var cpu = this.responseText.trim();
            var data = google.visualization.arrayToDataTable([
              ['Label', 'Value'],
              ['CPU', parseFloat(cpu) ], //new value attained by AJAX
            ]);
              
            chart.draw(data, options);
          }
          xhReq.open("GET", <?php echo Stats::cpuURL();?>, true);
          xhReq.send(null);
        },1000);
      }
    </script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Day', 'Download (mb)', 'Upload (mb)'],
          ['Today',  <?php echo "{$network['day0rx']},{$network['day0tx']}";?>],
          ['-1 day',   <?php echo "{$network['day1rx']},{$network['day1tx']}";?>],
          ['-2 days',  <?php echo "{$network['day2rx']},{$network['day2tx']}";?>]
        ]);

        var options = {
          title: 'Daily Traffic',height:158,legend:{position: 'in', alignment:'center'}
          
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('traffic_graph'));
        chart.draw(data, options);
      }
    </script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['id', 'temperature'], <?php echo Stats::temperatures() ; ?>  
        ]);

        var options = {
          title: 'Raspberry PI Temperature',legend:{position: 'in', alignment:'center'}, hAxis:{viewWindow:{max: 36}}
        };

        var chart = new google.visualization.LineChart(document.getElementById('chart_temphistory'));
        chart.draw(data, options);
      }
    </script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['id', 'CPU Load %'], <?php echo Stats::cpuLoads() ; ?>  
        ]);

        var options = {
          title: 'Raspberry PI CPU Load',legend:{position: 'in', alignment:'center'}, hAxis:{viewWindow:{max: 36}}
        };

        var chart = new google.visualization.LineChart(document.getElementById('chart_cpuhistory'));
        chart.draw(data, options);
      }
    </script>
    
  </head>
  <body>
    <!-- Fixed navbar -->
    <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
      <!-- Brand and toggle get grouped for better mobile display -->
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="#"><?php echo LibRbpi::hostname(true); ?></a>
      </div>
    
      <!-- Collect the nav links, forms, and other content for toggling -->
      <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav">
          <li><p class="navbar-text"><span class="label label-<?php echo $cpu_heat['alert']; ?>">T: <?php echo $cpu_heat['degrees']; ?>&deg;C</span></p></li>
          <li><p class="navbar-text"><span class="label label-<?php echo $cpu['alert']; ?>">Load: <?php echo $cpu['loads']; ?>%</span></p></li>
          <li><p class="navbar-text"><span class="label label-<?php echo $ram['alert']; ?>">Memory: <?php echo $ram['percentage']; ?>%</span></p></li>
          <li><p class="navbar-text"><span class="label label-<?php echo $hdd[0]['alert']; ?>">Disk: <?php echo $hdd[0]['percentage']; ?>%</span></p></li>
        </ul>
        
        <ul class="nav navbar-nav navbar-right">
			<li class="active"><a href="/"><span class="glyphicon glyphicon-home">&nbsp;</span>Home</a></li>
            <li><a href="/details/"><span class="glyphicon glyphicon-search">&nbsp;</span>Details</a></li>
           <li><a href="/services/"><span class="glyphicon glyphicon-cog">&nbsp;</span>Services</a></li>
           <li><a href="/disks/"><span class="glyphicon glyphicon-hdd">&nbsp;</span>Disks</a></li>
            <li class="dropdown">
			        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-cog"></span> <b class="caret"></b></a>
			        <ul class="dropdown-menu">
			          <li><a href="/phpmyadmin/"><span class="glyphicon glyphicon-tasks">&nbsp;</span>PHPMyAdmin</a></li>
			          <li><a href="https://<?php echo $_SERVER['SERVER_NAME']; ?>:10000/"><span class="glyphicon glyphicon-dashboard">&nbsp;</span>WebMin</a></li>
			        </ul>
			      </li>
        </ul>
      </div><!-- /.navbar-collapse -->
    </nav>

    <div class="container" style="margin-top:70px;">

    <!-- Main component for a primary marketing message or call to action -->
    <div class="row">
        <div class="col-lg-7">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">Network</h3>
              </div>
              <div class="panel-body">
                <div id='traffic_graph'></div>
              </div>
            </div>
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">History</h3>
              </div>
              <div class="panel-body">
                <div id='chart_temphistory'></div>
                <div id='chart_cpuhistory'></div>
              </div>
            </div>
		 </div>
        <div class="col-lg-3">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">System info</h3>
              </div>
                  <ul class="list-group">
                    <li class="list-group-item">
                        <div class="sysinfo-width">Hostname</div> <?php echo LibRbpi::hostname(true); ?> </br>
						<div class="sysinfo-width">Internel IP</div> <?php echo LibRbpi::internalIp(); ?> </br>
                        <div class="sysinfo-width">Distribution</div> <?php echo LibRbpi::distributionShort(); ?> </br>
                        <div class="sysinfo-width">Uptime</div> <?php echo LibUptime::short(); ?> </br>
			            <table width="100%" align="center"><tr>
						 <td width="50%" align="right"><div id='chart_nowmeter'></div></td>
			            <td width="50%" align="left"><div id='chart_cpunowmeter'></div></td>
						 </tr></table>
                    </li>
                    <li class="list-group-item">
                        <div class="sysinfo-width">Memory</div> <?php echo $ram['total']; ?>MB </br></br>
                        <table width="100%">
                        <tr>
                        	<td>
                            <div class="progress" id="popover-ram">
                              <div class="progress-bar progress-bar-<?php echo $ram['alert']; ?>"  role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $ram['percentage']; ?>%"><?php echo $ram['percentage']; ?>%</div>
                            </div>
                            <div id="popover-ram-head" class="hide">Top RAM eaters</div>
                            <div id="popover-ram-body" class="hide"><?php echo shell_to_html_table_result($ram['detail']); ?></div>
                            <div style="margin-top:-15px; margin-bottom:10px;">
                              <div class="sysinfo-bar">used</div> <?php echo $ram['used']; ?>MB </br>
                              <div class="sysinfo-bar">free</div> <?php echo $ram['free']; ?>MB
                            </div>
                         </td>
                        </tr>
                        </table>
                    </li>
                    <li class="list-group-item">
                        <?php for ($i=0; $i<sizeof($hdd); $i++) { ?>
                        <h5><?php echo $hdd[$i]['name']." (".$hdd[$i]['format'].")"; ?></h5>
                        <table width="100%">
                        <tr>
                        	<td>
                            <div class="progress">
                              <div class="progress-bar progress-bar-<?php echo $hdd[$i]['alert']; ?>"  role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $hdd[$i]['percentage']; ?>%"><?php echo $hdd[$i]['percentage']; ?>%</div>
                            </div>
                            <div style="margin-top:-15px; margin-bottom:10px;">
                            <div class="sysinfo-bar">total size</div> <div class="sysinfo-bar-kb"><?php echo $hdd[$i]['total']; ?></div>
                            </div>
                         </td>
                        </tr>
                        </table>
                        <?php } ?>
                    </li>
                  </ul>
            </div>
		 </div>
    </div>
    </div> <!-- /container -->

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="//code.jquery.com/jquery.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <!-- popup info -->
    <script src="js/details.js"></script>
  </body>
</html>