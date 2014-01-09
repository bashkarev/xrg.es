<?php

require(dirname(__FILE__) ."/trace.php");
require(dirname(__FILE__) ."/preg.php");

session_start();

try {
	$preg = new Preg($_POST);

    $preg->validate();
    $preg->prepare();
    $preg->flags();
    $preg->offset();
    $preg->limit();
    $preg->delimiter();
    $preg->exec();
    
    $redis = new Redis();
    $redis->connect("localhost");

    $key = $preg->key_cache();
    $ttl = $redis->setex("xrg.es:". $key, 60*60*24*30*12, $preg->value_cache()); // 60*60*24*30*12   1 año
    trace("Cache STORED xrg.es:". $key);
    
} catch (Exception $e) {}

$tpl = array(
    'result' => array(
        'return' => $preg->return, 
        'status' => $preg->status, 
        'reason' => $preg->reason, 
        'content' => debug($preg->content),
        'snippet' => $preg->snippet,
        'use' => $preg->use,
        'tags' => '',
        'permalink' => $key),
    'o' => $preg->o,
    'm' => $preg->m,
    'a' => $preg->a,
    's' => $preg->s,
    'r' => $preg->r
);

header("Content-Type: text/json");
echo json_encode($tpl);