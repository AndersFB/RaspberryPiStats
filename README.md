Raspberry Pi Stats
================

Web page showing stats of your Raspberry Pi.

This project is maked up by parts from <a href="http://yuraa.github.io/Raspberry-Pi-Heartbeat/" target="_blank">Raspberry Pi Heartbeat</a> and <a href="https://github.com/imjacobclark/Raspcontrol" target="_blank">Raspcontrol</a>.

The projects layout is based on the <a href="http://getbootstrap.com" target="_blank">Bootstrap</a> framework.

Read charts/README.md for information about configuration of the database used by some the graph.

![Raspberry Pi Stats](http://i.imgur.com/kEgOLob.png)

If you don't have WebMin or/and PHPMyAdmin the following lines in index.php, details/index.php, disks/index.php and service/index.php is unnecessary:

```
<li class="dropdown">
  <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-cog"></span> <b class="caret"></b></a>
  <ul class="dropdown-menu">
    <li><a href="/phpmyadmin/"><span class="glyphicon glyphicon-tasks">&nbsp;</span>PHPMyAdmin</a></li>
    <li><a href="https://<?php echo $_SERVER['SERVER_NAME']; ?>:10000/"><span class="glyphicon glyphicon-dashboard">&nbsp;</span>WebMin</a></li>
  </ul>
</li>
```
