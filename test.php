<?php
include 'config.php';
include 'functions.php';
if ($_REQUEST['t'] !== $ACCESS_T){
die("Authentication error");
}
error_reporting(E_ALL);
ini_set('display_errors', 1);
//phpinfo();
$proxies = new Memcached();
$proxies->addServer("127.0.0.1", 11211);
print_r ($proxies->get("proxy"));
echo "OK";
?>