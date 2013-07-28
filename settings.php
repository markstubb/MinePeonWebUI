<?php

require_once('settings.inc.php');
require_once('miner.inc.php');


if (isset($_POST['newpassword'])) {

	if ($_POST['newpassword'] <> '') {
		$file = '/opt/minepeon/etc/uipassword';
		$content = 'minepeon:' . crypt($_POST['newpassword']);

		file_put_contents($file, $content);
	}
}

if (isset($_POST['userTimeZone'])) {

	$settings['timezone'] = $_POST['userTimeZone'];
	writeSettings($settings);
	
}

if (isset($_POST['userDonation'])) {

	$settings['donation'] = $_POST['userDonation'];
	writeSettings($settings);
	
}

if (isset($_POST['deviceName'])) {

	$settings['deviceName'] = $_POST['deviceName'];
	writeSettings($settings);
	
}

if (isset($_POST['userEmail'])) {

	$settings['email'] = $_POST['userEmail'];
	writeSettings($settings);
	
}

if (isset($_POST['userSMTP'])) {

	$settings['smtp'] = $_POST['userSMTP'];
	writeSettings($settings);
	
}

if (isset($_POST['userDevices'])) {

	$settings['devices'] = $_POST['userDevices'];
	writeSettings($settings);
	
}

function formatOffset($offset) {
        $hours = $offset / 3600;
        $remainder = $offset % 3600;
        $sign = $hours > 0 ? '+' : '-';
        $hour = (int) abs($hours);
        $minutes = (int) abs($remainder / 60);

        if ($hour == 0 AND $minutes == 0) {
            $sign = ' ';
        }
        return $sign . str_pad($hour, 2, '0', STR_PAD_LEFT) .':'. str_pad($minutes,2, '0');

}

$utc = new DateTimeZone('UTC');
$dt = new DateTime('now', $utc);

$tzselect = '<select name="userTimeZone">';

foreach(DateTimeZone::listIdentifiers() as $tz) {
    $current_tz = new DateTimeZone($tz);
    $offset =  $current_tz->getOffset($dt);
    $transition =  $current_tz->getTransitions($dt->getTimestamp(), $dt->getTimestamp());
    $abbr = $transition[0]['abbr'];

	$selected = "";
	
	if ($settings['timezone'] == $tz) {
		$selected = "selected";
	}
	
    $tzselect = $tzselect . '<option ' .$selected. ' value="' .$tz. '">' .$tz. ' [' .$abbr. ' '. formatOffset($offset). ']</option>';
}
$tzselect = $tzselect . '</select>';


include('head.php');
include('menu.php');
?>
	<div>
		<?php if($donation == 0) { ?><embed height="0" width="0" src="inc/kitten.mp3"><?php } ?>
	</div>
        <div class="container">
				<h1>Settings</h1>
				<form name="input" action="/settings.php" method="post">
					<table border="0">
					<tr>
					<td>New Password: </td>
					<td><input type="text" value="" name="newpassword">
					</tr>
					<tr>
					<td>TimeZone: </td>
					<td><?php echo $tzselect ?>
					</tr>
					<tr>
					<td>Donation (Minutes per 24 hours): </td>
					<td><input type="text" value="<?php echo $settings['donation'] ?>" name="userDonation">
					</tr>
					<tr>
					<td>Device Name for Alerts: </td>
					<td><input type="text" value="<?php echo $settings['deviceName'] ?>" name="deviceName">
					</tr>
					<tr>
					<td>Email for Alerts: </td>
					<td><input type="text" value="<?php echo $settings['email'] ?>" name="userEmail">
					</tr>
					<tr>
					<td>Your SMTP Server: </td>
					<td><input type="text" value="<?php echo $settings['smtp'] ?>" name="userSMTP">
					</tr>
					<tr>
					<td>Expected Devices: </td>
					<td><input type="text" value="<?php echo $settings['devices'] ?>" name="userDevices">
					</td>
					</tr>					
					<table>
					<input type="submit" value="Save Settings">
					</form>
					
        </div>
		<?php if($donation == 0) { echo $plea; } ?>
<?php

include('foot.php');