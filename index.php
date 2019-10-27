<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'functions.php';
include 'config.php';
header('Content-Type: application/json');
$proxies = new Memcached();
$proxies->addServer("127.0.0.1", 11211);
$current = $proxies->get("current"); // Get the last proxy used
if ( $current > $proxynum ) {
  $proxies->set("current", 1);
  $current=1;
}
if ($proxies->get("proxy".$current."-status") !== "running"){
  if (get_status("proxy".$current) !== "running"){ // TODO try to chache this value to minimize time
    $current = $current+1;
    if ($current >= $proxynum ){
      $current = 1;
    }
    
  } else {
    $proxies->set("proxy".$current."-status" , "running");
  }
}
//echo $current;
$proxy = $proxies->get("proxy".$current);
//echo $proxy;
$v=$_GET["v"];
$res=shell_exec("LC_ALL=en_US.UTF-8 youtube-dl --proxy http://admin:aHmEd256@".$proxy.":3128 -f 18 -4 --dump-json https://www.youtube.com/watch?v=".$v." 2>&1");
$response=json_decode($res);
if(empty($response)){
  $bad = array('status' => 'bad', 'error' => $res);
  print_r(json_encode($bad));
  if (strpos($res, '429') !== false){
    replace_linode("proxy".$current);
  }
}
else {
$arrayName = array('status' => "ok", "thumb" => $response->thumbnail , "title" => $response->title , "url" =>  $response->url);
print_r(json_encode($arrayName));
}
$next = $current+1;
$proxies->set("current", $next);
?>

