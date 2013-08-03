<?php
/*
f_status gets values that people want to see in realtime
returns success, status data and errors
*/
header('Content-type: application/json');

require('miner.inc.php');

// Miner data
$export['mhsav'] = cgminer("summary", "")['SUMMARY'][0]['MHSav'];
//$export['devs'] = cgminer("devs", "")['id'];
//$export['pools'] = cgminer("pools", "")['POOLS'];

// MinePeon data
//$export['minepeon']['uptime'] = explode(' ', exec("cat /proc/uptime"));
$export['temp'] = substr(exec('/opt/vc/bin/vcgencmd measure_temp'), 5, -2);

$cpu=explode(' ', exec("cat /proc/stat | grep cpu0"));
$export['load']    = sys_getloadavg()[0];
$export['cpuIdle'] = $cpu[4];
$export['cpuTot']  = array_sum($cpu);

$export['mhsav'] = rand(800,1000)*rand(800,1000);

$export['success'] = true;

echo json_encode($export);
?>