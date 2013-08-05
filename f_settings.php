<?php
/*
f_settings gets all settings and saves updated settings AND UPDATES TIME
returns settings, success and action
*/
header('Content-type: application/json');

$r = json_decode(file_get_contents("/opt/minepeon/etc/minepeon.conf"), true);

// Prevent losing all settings
if (strlen(json_encode($r))<200) {
	$r['success']=false;
	echo json_encode($r);
	exit;
}

// Echo and exit if not saving
if (empty($_REQUEST['save'])) {
	$r['time'] = time();
	$r['action']='get';
	$r['success']=true;
	echo json_encode($r);
	exit;
}

// Overwrite old with new settings
foreach (json_decode($_REQUEST['save'], true) as $key => $value) {
	$r[$key]=$value;
}

// It's too small to put this in a seperate file
$timezone = $r['userTimezone'];
ini_set( 'date.timezone', $timezone );
putenv("TZ=" . $timezone);
date_default_timezone_set($timezone);

// Write back to file
$written = file_put_contents("/opt/minepeon/etc/minepeon.conf", json_encode($r, JSON_PRETTY_PRINT));

$r['time'] = time();
$r['success']=true;
$r['action']='put';

echo json_encode($r);
?>