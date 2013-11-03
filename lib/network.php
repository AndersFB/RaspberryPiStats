<?php

class LibNetwork {
  
  public static function connections() {

    $connections = shell_exec("netstat -nta --inet | wc -l");
    $connections--;

    return array('connections' => substr($connections, 0, -1));
  }

}
