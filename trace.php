<?php

function trace($str) {
	openlog('xrg.es', LOG_PID, LOG_LOCAL0);
	syslog(LOG_INFO, $str);
}

function debug($mixed) {
    ob_start();
    require_once(dirname(__FILE__) .'/dBug.php');
    new dBug($mixed);
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
}

function h($raw) {
	return htmlentities($raw, ENT_QUOTES);
}