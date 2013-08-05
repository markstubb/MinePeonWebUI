<?php
/*
f_settings syncs settings and always returns new state
returns settings and logs last settings change in ['time']
*/
header('Content-type: application/json');

$r['data'] = json_decode(file_get_contents('/opt/minepeon/etc/minepeon.conf'), true);

// Prevent losing all settings
if (strlen(json_encode($r))<200) {
	$r['info'][]=array("type" => "warning", "text" => "Suspiciously short minepeon.conf");
}

// Fetch current settings
if (!empty($_REQUEST['pass'])) {
	$pass=json_decode($_REQUEST['pass']);
	if (strlen($pass) > 0) {
		$file = '/opt/minepeon/etc/uipassword';

		file_put_contents($file,'minepeon:' . crypt($pass));
		$r['info'][]=array("type" => "success", "text" => "Password saved.");
	}
}

// Sync current with new settings
if (!empty($_REQUEST['all'])) {
	foreach (json_decode($_REQUEST['all'], true) as $key => $value) {
		$r['data'][$key]=$value;
	}
	// Write back to file
	file_put_contents('/opt/minepeon/etc/minepeon.conf', json_encode($r['data'], JSON_PRETTY_PRINT));
	$r['info'][]=array("type" => "success", "text" => "Settings saved.");
}
$r['time'] = time();

// Sync timezone
if (!empty($_REQUEST['timezone'])) {
	$timezone = $r['data']['userTimezone'];
	ini_set( 'date.timezone', $timezone );
	putenv('TZ=' . $timezone);
	date_default_timezone_set($timezone);
	$r['info'][]=array("type" => "success", "text" => "Timezone updated");
}


echo json_encode($r);
?>