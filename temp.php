<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'functions.php';
//print_r(get_id("proxy1"));


$proxies = new Memcached();
$proxies->addServer("127.0.0.1", 11211);
$response = $proxies->delete("proxy1");
if ($response) {
echo $response;
} else {
$proxies->set("proxy1", create_linode("proxy1")) or die("Cannot create linode");
}

?>