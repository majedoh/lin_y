<?php
function create_linode($label){ // creates new instance with given label, returns ipv4 of the new instance
    include 'config.php';
    $ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://api.linode.com/v4/linode/instances');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "{\n  \"swap_size\": 512,\n  \"image\": \"linode/ubuntu16.04lts\",\n  \"root_pass\": \"#HashT@g\",\n  \"stackscript_id\": 68166,\n  \"stackscript_data\": {\n    \"squid_user\": \"admin\",\n    \"squid_password\": \"aHmEd256\"\n  },\n  \"authorized_keys\": [\n    \"ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQDnZu2f0DLKjrKwZkf+xB/zTi/jvKbba0HDftvM3h+2+AGjzis8YTZNO45XFaSAtUQU/bq8gWw0HCns3a/w++zhdLgrCKkgyFzCjkOtn2JtXJRAo9aB+O1d/+5yak1S1woLI6pJscE+yg/lUNdCKiet+VVz92NZAZjS83lqpUS6JZp6HGiI4g8s+7pkccnYQsl/+X76LJTuFLkQbO99K7lNC6o2Sa5cOA2IDZhJgkvTxT0gIO+jOJ4acYdF5XCjRXxCUDW4FWTDzAyOqanwTeeL2H5WiK2i0ETvuVmwVurXZj95MbF0SQC/m0sMeh0Cgk/NsgP/yPDu3cvA+ilZz9hl root@instance-1\"\n  ],\n  \"booted\": true,\n  \"label\": \"$label\",\n  \"type\": \"g6-nanode-1\",\n  \"region\": \"us-east\",\n  \"group\": \"Linode-Group\",\n  \"watchdog_enabled\": false,\n  \"tags\": [\"proxy\"]\n}");
curl_setopt($ch, CURLOPT_POST, 1);

$headers = array();
$headers[] = 'Content-Type: application/json';
$headers[] = 'Authorization: Bearer '.$TOKEN;
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
$result = json_decode($result);
curl_close($ch);
$proxies = new Memcached();
$proxies->addServer("127.0.0.1", 11211);
$response = $proxies->delete($label);
$proxies->set($label, $result->ipv4[0]) or die("Cannot create linode");
$proxies->set($label."-status", "stopped") or die("Cannot create linode");
$proxies->set("current", 1); // set current proxy to 1 to be not empty
return $result->ipv4[0];

}

function get_id($label){ // get id of the instance from the given label
        include 'config.php';
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, 'https://api.linode.com/v4/linode/instances');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        
        
        $headers = array();
        $headers[] = 'Authorization: Bearer '.$TOKEN;
        $headers[] = 'X-Filter: { "label":"'.$label.'" }';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $result = curl_exec($ch);
        $result = json_decode($result);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        return($result->data[0]->id);
    }
    function get_status($label){
        include 'config.php';
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, 'https://api.linode.com/v4/linode/instances');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        
        
        $headers = array();
        $headers[] = 'Authorization: Bearer '.$TOKEN;
        $headers[] = 'X-Filter: { "label":"'.$label.'" }';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $result = curl_exec($ch);
        $result = json_decode($result);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        return($result->data[0]->status);
    }
    function replace_linode($label){
        include 'config.php';
        $id = get_id($label);
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.linode.com/v4/linode/instances/'.$id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');


        $headers = array();
        $headers[] = 'Authorization: Bearer '.$TOKEN;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        $proxies = new Memcached();
        $proxies->addServer("127.0.0.1", 11211);
        $response = $proxies->delete($label);
        $new = create_linode($label); // $new is the ip of the new linode
        return $new;
        
    }
?>