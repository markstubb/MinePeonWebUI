<?php
/*
f_status gets values that people want to see in realtime
returns success, status data and errors
*/
header('Content-type: application/json');

require('miner.inc.php');

// Miner data
$export['summary'] = cgminer("summary", "")['SUMMARY'];
$export['devs'] = cgminer("devs", "")['id'];

// MinePeon data
$export['minepeon']['uptime'] = explode(' ', exec("cat /proc/uptime"));
$export['minepeon']['cpu'] = sys_getloadavg();
$export['minepeon']['temp'] = substr(substr(exec('/opt/vc/bin/vcgencmd measure_temp'), 5), 0, -2);
$export['success'] = true;

echo json_encode($export);
?>