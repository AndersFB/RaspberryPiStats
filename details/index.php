<?php 
  require_once "../charts/config.php";
  require_once "../charts/funs.php";
  $network = Stats::network();
  
  require_once "../lib/uptime.php";
  require_once "../lib/memory.php";
  require_once "../lib/cpu.php";
  require_once "../lib/storage.php";
  require_once "../lib/network.php";
  require_once "../lib/rbpi.php";
  require_once "../lib/users.php";
  $uptime = LibUptime::uptime();
  $ram = LibMemory::ram();
  $swap = LibMemory::swap();
  $cpu = LibCPU::cpu();
  $cpu_heat = LibCPU::heat();
  $hdd = LibStorage::hdd();
  $net_connections = LibNetwork::connections();
  $users = LibUsers::connected();

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
    <meta http-equiv="refresh" content="60" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="../css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="../css/custom.css" rel="stylesheet" media="screen">

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
            height: 140,
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
          height: 140,
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
    <script type='text/javascript'>
      google.load('visualization', '1', {packages:['table']});
      google.setOnLoadCallback(drawTable);
      function drawTable() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'When');
        data.addColumn('number', 'DL');
        data.addColumn('number', 'UL');
        data.addRows([
          
          ['This Month',<?php echo "{v:{$network['mon0rx']},f:'{$network['mon0rx']} MB'} , {v:{$network['mon0tx']},f:'{$network['mon0tx']} MB'}"; ?>],
          ['Prev. Month', <?php echo "{v:{$network['mon1rx']},f:'{$network['mon1rx']} MB'} , {v:{$network['mon1tx']},f:'{$network['mon1tx']} MB'}"; ?>],
          ['Total', <?php echo "{v:{$network['alltrx']},f:'{$network['alltrx']} MB'} , {v:{$network['allttx']},f:'{$network['allttx']} MB'}"; ?>]

        ]);
        
        var options = {
        	sort:'disable',showRowNumber: false
        };

        var table = new google.visualization.Table(document.getElementById('traffic_div'));
        table.draw(data, options);
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
			<li><a href="/"><span class="glyphicon glyphicon-home">&nbsp;</span>Home</a></li>
            <li class="active"><a href="/details/"><span class="glyphicon glyphicon-search">&nbsp;</span>Details</a></li>
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
        <div class="col-lg-10">
            <div class="panel panel-default">
                <ul class="list-group">
                  <li class="list-group-item">
                  <table align="center" width="100%"><tr>
					  <td valign="top" width="20%"><span class="glyphicon glyphicon-cog"></span> <strong>System</strong></td>
			         <td width="5%">&nbsp;</td>
                    <td width="75%">
                      <div class="sysinfo-width">Hostname</div> <?php echo LibRbpi::hostname(true); ?> </br>
                      <div class="sysinfo-width">Systemtime</div> <?php echo LibRbpi::systemTime(); ?> </br>
                      <div class="sysinfo-width">Distribution</div> <?php echo LibRbpi::distribution(); ?> </br>
                      <div class="sysinfo-width">Kernel</div> <?php echo LibRbpi::kernel(); ?> </br>
                      <div class="sysinfo-width">Firmware</div> <?php echo LibRbpi::firmware(); ?> </br>
                      <div class="sysinfo-width">Webserver</div> <?php echo LibRbpi::webServer(); ?> </br>
                      <div class="sysinfo-width">Uptime</div> <?php echo $uptime; ?>
                    </td>
					</tr></table>
                  </li>
                  <li class="list-group-item">
                  <table align="center" width="100%"><tr>
					  <td valign="top" width="20%"><span class="glyphicon glyphicon-dashboard"></span> <strong>RAM</strong></td>
			         <td width="5%">&nbsp;</td>
                    <td width="75%">

                        <table width="100%">
                        <tr>
                        	<td valign="baseline"><div class="sysinfo-width">Memory</div> <?php echo $ram['total']; ?>MB</td>
                        	<td width="60%">
                            <div class="progress" id="popover-ram">
                              <div class="progress-bar progress-bar-<?php echo $ram['alert']; ?>"  role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $ram['percentage']; ?>%"><?php echo $ram['percentage']; ?>%</div>
                            </div>
                            <div id="popover-ram-head" class="hide">Top RAM eaters</div>
                            <div id="popover-ram-body" class="hide"><?php echo shell_to_html_table_result($ram['detail']); ?></div>
                            <div style="margin-top:-15px; margin-bottom:10px;">used: <?php echo $ram['used']; ?>MB &sdot; free: <?php echo $ram['free']; ?>MB</div>
                         </td>
                        </tr>
                        <tr>
                        	<td valign="baseline"><div class="sysinfo-width">Buffered</div> <?php echo $ram['buffers']; ?>MB</td>
                        	<td width="60%">
                            <div class="progress">
                              <div class="progress-bar progress-bar-<?php echo $ram['alert_buffers']; ?>"  role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $ram['percentage_buffers']; ?>%"><?php echo $ram['percentage_buffers']; ?>%</div>
                            </div>
                         </td>
                        </tr>
                        <tr>
                        	<td valign="baseline"><div class="sysinfo-width">Cached</div> <?php echo $ram['cached']; ?>MB</td>
                        	<td width="60%">
                            <div class="progress">
                              <div class="progress-bar progress-bar-<?php echo $ram['alert_cached']; ?>"  role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $ram['percentage_cached']; ?>%"><?php echo $ram['percentage_cached']; ?>%</div>
                            </div>
                         </td>
                        </tr>
                        </table>

                    </td>
					</tr></table>
                  </li>
                  <li class="list-group-item">
                  <table align="center" width="100%"><tr>
					  <td valign="top" width="20%"><span class="glyphicon glyphicon-refresh"></span> <strong>Swap</strong></td>
			         <td width="5%">&nbsp;</td>
                    <td width="75%">

                        <table width="100%">
                        <tr>
                        	<td valign="baseline"><div class="sysinfo-width">Swap</div> <?php echo $swap['total']; ?>MB</td>
                        	<td width="60%">
                            <div class="progress">
                              <div class="progress-bar progress-bar-<?php echo $swap['alert']; ?>"  role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $swap['percentage']; ?>%"><?php echo $swap['percentage']; ?>%</div>
                            </div>
                            <div style="margin-top:-15px; margin-bottom:10px;">used: <?php echo $swap['used']; ?>MB &sdot; free: <?php echo $swap['free']; ?>MB</div>
                         </td>
                        </tr>
                        </table>

                    </td>
					</tr></table>
                  </li>
                  <li class="list-group-item">
                  <table align="center" width="100%"><tr>
					  <td valign="top" width="20%"><span class="glyphicon glyphicon-tasks"></span> <strong>CPU</strong></td>
			         <td width="5%">&nbsp;</td>
                    <td width="75%">
                      <div class="sysinfo-width">Frequency</div> <?php echo $cpu['current']; ?> </br>
                      <div class="sysinfo-width">Load</div> <?php echo $cpu['loads']; ?>% </br></br>
                        <table width="100%">
                        <tr>
                        	<td valign="baseline"><div class="sysinfo-width">Temperatur</div> <div class="sysinfo-bar-kb"><?php echo $cpu_heat['degrees']; ?>&deg;C</div></td>
                        	<td width="60%">
                            <div class="progress" id="popover-cpu">
                              <div class="progress-bar progress-bar-<?php echo $cpu_heat['alert']; ?>"  role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $cpu_heat['percentage']; ?>%"><?php echo $cpu_heat['percentage']; ?>%</div>
                              <div id="popover-cpu-head" class="hide">Top CPU eaters</div>
                              <div id="popover-cpu-body" class="hide"><?php echo shell_to_html_table_result($cpu_heat['detail']); ?></div>
                            </div>
                         </td>
                        </tr>
                        </table>
                        
                      <table width="100%" align="center"><tr>
						 	<td width="50%" align="right"><div id='chart_nowmeter'></div></td>
			            	<td width="50%" align="left"><div id='chart_cpunowmeter'></div></td>
						 </tr></table>
                      <div id='chart_temphistory'></div>
                      <div id='chart_cpuhistory'></div>
                    </td>
					</tr></table>
                  </li>
                  <li class="list-group-item">
                  <table align="center" width="100%"><tr>
					  <td valign="top" width="20%"><span class="glyphicon glyphicon-hdd"></span> <strong>Storage</strong></td>
			         <td width="5%">&nbsp;</td>
                    <td width="75%">
                                            
                      <?php for ($i=0; $i<sizeof($hdd); $i++) { ?>
                        <h5><?php echo $hdd[$i]['name']. "(". $hdd[$i]['format'] .")"; ?></h5>
                        <table width="100%">
                        <tr>
                        	<td valign="baseline"><div class="sysinfo-width">Total size</div> <div class="sysinfo-bar-kb"><?php echo $hdd[$i]['total']; ?></div></td>
                        	<td width="60%">
                            <div class="progress">
                              <div class="progress-bar progress-bar-<?php echo $hdd[$i]['alert']; ?>"  role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $hdd[$i]['percentage']; ?>%"><?php echo $hdd[$i]['percentage']; ?>%</div>
                            </div>
                            <div style="margin-top:-15px; margin-bottom:10px;">used: <?php echo $hdd[$i]['used']; ?> &sdot; available: <?php echo $hdd[$i]['free']; ?></div>
                         </td>
                        </tr>
                        </table>
                      <?php } ?>

                    </td>
					</tr></table>
                  </li>
                  <li class="list-group-item">
                  <table align="center" width="100%"><tr>
					  <td valign="top" width="20%"><span class="glyphicon glyphicon-globe"></span> <strong>Network</strong></td>
			         <td width="5%">&nbsp;</td>
                    <td width="75%">
                      <div class="sysinfo-width">Internel IP</div> <?php echo LibRbpi::internalIp(); ?> </br>
                      <div class="sysinfo-width">Externel IP</div> <?php echo LibRbpi::externalIp(); ?> </br>
                      <div class="sysinfo-width">Connections</div> <?php echo $net_connections['connections']; ?> </br></br>
                      <div id='traffic_graph'></div>
    			  		<div id='traffic_div'></div>
                    </td>
					</tr></table>
                  </li>
                  <li class="list-group-item">
                  <table align="center" width="100%"><tr>
					  <td valign="top" width="20%"><span class="glyphicon glyphicon-user"></span> <strong>Users</strong></td>
			         <td width="5%">&nbsp;</td>
                    <td width="75%">
                    
                    <table width="100%">
                    <?php
						  if (sizeof($users) > 0) {
							for ($i=0; $i<sizeof($users); $i++)
							{
								$break = "";
								if($i < sizeof($users)-1) { $break = "</br></br>"; }
							
							  	echo '<tr><td width="120" valign="top"><strong>'. $users[$i]['user'] .'</strong></td><td> logged in since '. $users[$i]['date']. ' at '. $users[$i]['hour']. ' from '. $users[$i]['ip'] .' '. $users[$i]['dns']. $break. '</td></tr>';
						  	}
						  }
						  else echo '<tr><td colspan="2">No user logged in</td></tr>';
						?>
                      </table>
                      
                    </td>
					</tr></table>
                  </li>
                </ul>
            </div>
        </div>
    </div>
    
    </div> <!-- /container -->

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="//code.jquery.com/jquery.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="../js/bootstrap.min.js"></script>
    <!-- popup info -->
    <script src="../js/details.js"></script>
  </body>
</html>