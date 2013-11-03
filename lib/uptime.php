<?php

class LibUptime {
  
  public static function uptime() {

    $uptime = shell_exec("cat /proc/uptime");
    $uptime = explode(" ", $uptime); 

    return self::readbleTime($uptime[0]);

  }

  protected static function readbleTime($seconds) {
    $y = floor($seconds / 60/60/24/365);
    $d = floor($seconds / 60/60/24) % 365;
    $h = floor(($seconds / 3600) % 24);
    $m = floor(($seconds / 60) % 60);
    $s = $seconds % 60;

    $string = '';

    if ($y > 0) {
      $yw = $y > 1 ? ' years ' : ' year ';
      $string .= $y . $yw;
    }

    if ($d > 0) {
      $dw = $d > 1 ? ' days ' : ' day ';
      $string .= $d . $dw;
    }

    if ($h > 0) {
      $hw = $h > 1 ? ' hours ' : ' hour ';
      $string .= $h . $hw;
    }

    if ($m > 0) {
      $mw = $m > 1 ? ' minutes ' : ' minute ';
      $string .= $m . $mw;
    }

    if ($s > 0) {
     $sw = $s > 1 ? ' seconds ' : ' second ';
     $string .= $s . $sw;
    }

    return preg_replace('/\s+/', ' ', $string);
  }

  public static function short() {
	 $uptime_array = explode(" ", exec("cat /proc/uptime"));
	 $seconds = round($uptime_array[0], 0);	
	 $minutes = $seconds / 60;
	 $hours = $minutes / 60;
	 $days = floor($hours / 24);
	 $hours = sprintf('%02d', floor($hours - ($days * 24)));
	 $minutes = sprintf('%02d', floor($minutes - ($days * 24 * 60) - ($hours * 60)));
		
	 if ($days == 0):
	 $uptime = $hours . ":" .  $minutes . " (hh:mm)";
	 elseif($days == 1):
	 $uptime = $days . " day, " .  $hours . ":" .  $minutes . " (hh:mm)";
	 else:
	 $uptime = $days . " days, " .  $hours . ":" .  $minutes . " (hh:mm)";
	 endif;
	
	 return $uptime;
  }

}

?>