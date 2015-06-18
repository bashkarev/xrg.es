<?php

header("Content-Type: application/json");

$hashes = array();
$algos = hash_algos();
foreach ($algos as $algo) {
	$hash = hash($algo, $_GET["plain"]);
	$hashes[] = array(
		"name" => $algo,
		"value" => $hash,
		"length" => strlen($hash)
	);
}

echo json_encode($hashes);
