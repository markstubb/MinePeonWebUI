<?php
/*
f_status gets values that people want to see in realtime
returns success, status data and errors
*/
header('Content-type: application/json');

// Only send cpu intensive values if requested
if(isset($_REQUEST['all'])){
	$export['uptime'] = explode(' ', exec("cat /proc/uptime"));
	$export['temp'] = substr(exec('/opt/vc/bin/vcgencmd measure_temp'), 5, -2);
}

$export['load'] = sys_getloadavg()[0];
$export['success'] = true;

echo json_encode($export);
?>