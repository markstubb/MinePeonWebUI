<?php
/*
f_settings syncs settings and always returns new state
returns settings and logs last settings change in ['time']
*/
header('Content-type: application/json');

// Set new password
if (!empty($_REQUEST['pass'])) {
	$pass=json_decode($_REQUEST['pass']);
	if (strlen($pass) > 0) {
		$file = '/opt/minepeon/etc/uipassword';

		file_put_contents($file,'minepeon:' . crypt($pass));
		$r['info'][]=array("type" => "success", "text" => "Password saved");
	}
}

// Manage settings
elseif (!empty($_REQUEST['settings'])) {
	$newdata   = json_decode($_REQUEST['settings'], true);
	$r['data'] = json_decode(file_get_contents('/opt/minepeon/etc/minepeon.conf'), true);

	// Sync current with new settings
	if(is_array($newdata)){
		foreach ($newdata as $key => $value) {
			$r['data'][$key]=$value;
		}
		// Write back to file
		file_put_contents('/opt/minepeon/etc/minepeon.conf', json_encode($r['data'], JSON_PRETTY_PRINT));
		$r['info'][]=array("type" => "success", "text" => "Settings saved");
	}
	// Load current settings
	else{
		$r['info'][]=array("type" => "success", "text" => "Settings loaded");
	}
}

// Manage pools
elseif (!empty($_REQUEST['pools'])) {
	$newdata   = json_decode($_REQUEST['pools'], true);
	$r['data'] = json_decode(file_get_contents("/opt/minepeon/etc/miner.conf"), true);

	// Overwrite current with new pools
	if(is_array($newdata)){
		unset($r['data']['pools']);
		$r['data']['pools']=$newdata;
		// Write back to file
		file_put_contents('/opt/minepeon/etc/miner.conf', json_encode($r['data'], JSON_PRETTY_PRINT));
		$r['info'][]=array("type" => "success", "text" => "Pools data saved");

		// Setup socket
		$client = stream_socket_client("tcp://127.0.0.1:4028", $errno, $errorMessage);

		// Socket failed
		if ($client === false) {
			$r['info'][]=array("type" => "danger", "text" => "Could not restart cgminer because: ".$errno." ".$errorMessage);
		}
		// Socket success
		else{
			fwrite($client, json_encode(array("command"=>"restart")));
			fclose($client);
			$r['info'][]=array("type" => "info", "text" => "Cgminer restarting: ");
		}
	}
	// Load current settings
	else{
		$r['info'][]=array("type" => "success", "text" => "Pools data loaded");
	}
}

// Set system timezone to what is stored in settings 
elseif (!empty($_REQUEST['timezone'])) {
	$timezone = json_decode(file_get_contents('/opt/minepeon/etc/minepeon.conf'), true)['userTimezone'];
	ini_set( 'date.timezone', $timezone );
	putenv('TZ=' . $timezone);
	date_default_timezone_set($timezone);
	$r['info'][]=array("type" => "danger", "text" => "Timezone updated");
}

$r['data']['time'] = time();

echo json_encode($r);
?>