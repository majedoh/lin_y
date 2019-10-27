<?php
include 'functions.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
//echo replace_linode("proxy1");
//echo get_status("proxy1");
$proxies = new Memcached();
$proxies->addServer("127.0.0.1", 11211);
echo  $proxies->get("proxy1-status");
echo  $proxies->get("proxy2");
echo  $proxies->get("proxy3");

?>