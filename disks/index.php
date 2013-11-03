<?php
  require_once "../lib/disks.php";
  require_once "../lib/memory.php";
  require_once "../lib/cpu.php";
  require_once "../lib/storage.php";
  require_once "../lib/rbpi.php";
  $disks = LibDisks::disks();
  $ram = LibMemory::ram();
  $cpu = LibCPU::cpu();
  $cpu_heat = LibCPU::heat();
  $hdd = LibStorage::hdd();
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Raspberry Pi Status</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="../css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="../css/custom.css" rel="stylesheet" media="screen">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="../../assets/js/html5shiv.js"></script>
      <script src="../../assets/js/respond.min.js"></script>
    <![endif]-->    
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
            <li><a href="/details/"><span class="glyphicon glyphicon-search">&nbsp;</span>Details</a></li>
           <li><a href="/services/"><span class="glyphicon glyphicon-cog">&nbsp;</span>Services</a></li>
           <li class="active"><a href="/disks/"><span class="glyphicon glyphicon-hdd">&nbsp;</span>Disks</a></li>
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
					  <td valign="top" width="20%"><span class="glyphicon glyphicon-hdd"></span> <strong>Disks</strong></td>
			         <td width="5%">&nbsp;</td>
                    <td width="75%">
                    
                        <table width="100%">
						  <?php	 
                            for ($i = 0; $i < sizeof($disks); $i++) {
                              $break = "";
							  	 if($i < sizeof($disks)-1) { $break = "</br></br>"; }
								 
							  	 if ($disks[$i]["type"] != "disk")
                              {
								  	 
									 $label = '<span class="label label-success">Mounted</span>';
									 if($disks[$i]['mountpoint']=="") { $label = '<span class="label label-danger">Not mounted</span>'; }
									 
                                 echo '<tr><td width="120" valign="top">'. $label .'</td>';
                                 echo '<td>'. $disks[$i]['name'] . "</br>Size: " .  $disks[$i]['size'] . "</br>Mountpoint: " . $disks[$i]['mountpoint']  . $break .'</td></tr>';
                              }
                              else echo '<tr><td width="120" valign="top"></td><td>'. $disks[$i]['name'] . $break .'</br></td></tr>';                           
                            }
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
  </body>
</html>