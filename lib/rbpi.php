<?php

class LibRbpi {
  
  public static function distribution() {
    $distroTypeRaw = exec("cat /etc/*-release | grep PRETTY_NAME=", $out);
    $distroTypeRawEnd = str_ireplace('PRETTY_NAME="', '', $distroTypeRaw);
    $distroTypeRawEnd = str_ireplace('"', '', $distroTypeRawEnd);

    return $distroTypeRawEnd;
  }
  
  public static function distributionShort() {
    $distroTypeRaw = exec("cat /etc/*-release | grep PRETTY_NAME=", $out);
    $distroTypeRawEnd = str_ireplace('PRETTY_NAME="', '', $distroTypeRaw);
    $distroTypeRawEnd = str_ireplace('"', '', $distroTypeRawEnd);

    $distro = explode(" ", $distroTypeRawEnd);

    return $distro[0];
  }

  public static function kernel() {
    return exec("uname -mrs");
  }

  public static function firmware() {
    return exec("uname -v");
  }

  public static function hostname($full = false) {
    return $full ? exec("hostname -f") : gethostname();
  }

  public static function systemTime() {
    return exec("date +'%d %b %Y, %T %Z'");;
  }
  
  public static function internalIp() {
    return $_SERVER['SERVER_ADDR'];
  }

  public static function externalIp() {
      $ip = self::loadUrl('http://whatismyip.akamai.com');
      if(filter_var($ip, FILTER_VALIDATE_IP) === false)
          $ip = self::loadUrl('http://ipecho.net/plain');
      if(filter_var($ip, FILTER_VALIDATE_IP) === false)
          return 'Unavailable';
      return $ip;
  }

  public static function webServer() {
    return$_SERVER['SERVER_SOFTWARE'];
  }
  
  protected static function loadUrl($url){
      if(function_exists('curl_init')){
          $curl = curl_init();
          curl_setopt($curl, CURLOPT_URL, $url);
          curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
          $content = curl_exec($curl);
          curl_close($curl);
          return trim($content);
      }elseif(function_exists('file_get_contents')){
          return trim(file_get_contents($url));
      }else{
          return false;
      }
  }

}

?>
