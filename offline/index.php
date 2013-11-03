<!DOCTYPE html>
<html>
  <head>
    <title>Raspberry Pi Status</title>
    <meta http-equiv="refresh" content="60" />
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
          ['T', 46]
        ]);

        var options = {
            height: 120,
            redFrom: 75, redTo: 90,
            yellowFrom:60, yellowTo: 75,
            greenFrom:0,greenTo:60,
            minorTicks: 5, min:0,max:90,
          };

        var chart = new google.visualization.Gauge(document.getElementById('chart_nowmeter'));
        chart.draw(data, options);
      }
    </script>
    <script type='text/javascript'>
      google.load('visualization', '1', {packages:['gauge']});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Label', 'Value'],
          ['CPU', 3]
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
      }
    </script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Day', 'Download (mb)', 'Upload (mb)'],
          ['Today',  55, 33],
          ['-1 day',   40, 12],
          ['-2 days',  73, 35]
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
          ['id', 'temperature'], ['15:00', 47.6], ['15:05', 48.9], ['15:10', 50.1]]);

        var options = {
          title: 'Raspberry PI Temperature',legend:{position: 'in', alignment:'center'}
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
          ['id', 'CPU Load %'], ['15:00', 20], ['15:05', 12], ['15:10', 8]]);

        var options = {
          title: 'Raspberry PI CPU Load',legend:{position: 'in', alignment:'center'}
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
        <a class="navbar-brand" href="#">$hostname</a>
      </div>
    
      <!-- Collect the nav links, forms, and other content for toggling -->
      <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav">
          <li><p class="navbar-text"><span class="label label-success">T: 46 C</span></p></li>
          <li><p class="navbar-text"><span class="label label-success">CPU: 2 %</span></p></li>
          <li><p class="navbar-text"><span class="label label-warning">Memory: 85 %</span></p></li>
          <li><p class="navbar-text"><span class="label label-success">Disk: 69 %</span></p></li>
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
			          <li><a href="https://<?php echo $_SERVER['SERVER_NAME']; ?>/10000/"><span class="glyphicon glyphicon-dashboard">&nbsp;</span>WebMin</a></li>
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
                        <div class="sysinfo-width">Hostname</div> $hostname </br>
                        <div class="sysinfo-width">Distribution</div> $distribution </br>
                        <div class="sysinfo-width">Uptime</div> $uptime </br>
			            <table width="100%" align="center"><tr>
						<td width="50%" align="right"><div id='chart_nowmeter'></div></td>
			            <td width="50%" align="left"><div id='chart_cpunowmeter'></div></td>
						</tr></table>
                    </li>
                    <li class="list-group-item">
                        <div class="sysinfo-width">Memory</div> $mem kB </br></br>
                        <table width="100%">
                        <tr>
                        	<td>
                            <div class="progress">
                              <div class="progress-bar progress-bar-success"  role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 45%">45%</div>
                            </div>
                            <div style="margin-top:-15px; margin-bottom:10px;">
                              <div class="sysinfo-bar">used</div> $used kB </br>
                              <div class="sysinfo-bar">free</div> $free kB
                            </div>
                         </td>
                        </tr>
                        </table>
                    </li>
                    <li class="list-group-item">
                        <h5>$disk</h5>
                        <table width="100%">
                        <tr>
                        	<td>
                            <div class="progress">
                              <div class="progress-bar progress-bar-success"  role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 45%">45%</div>
                            </div>
                            <div style="margin-top:-15px; margin-bottom:10px;">
                            <div class="sysinfo-bar">total size</div> $total MB
                            </div>
                         </td>
                        </tr>
                        </table>
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
  </body>
</html>