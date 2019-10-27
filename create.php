<?php
include 'functions.php';
include 'config.php';
for ($x = 1; $x <= $proxynum; $x++) {
    echo create_linode("proxy".$x);
    echo "<br>";
}
?> 
