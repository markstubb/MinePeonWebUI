<?php

require('ssl.inc.php');
require('timezone.inc.php');
require('miner.inc.php');


if (isset($_POST['newpassword'])) {

	if ($_POST['newpassword'] <> '') {
		$file = '/opt/minepeon/etc/uipassword';
		$content = 'minepeon:' . crypt($_POST['newpassword']);

		file_put_contents($file, $content);
	}
}

if (isset($_POST['userTimeZone'])) {

 
	$file = '/opt/minepeon/etc/timezone';
	$content = $_POST['userTimeZone'];

	file_put_contents($file, $content);

}

if (isset($_POST['userDonation'])) {

 
	$file = '/opt/minepeon/etc/donation';
	$content = $_POST['userDonation'];

	file_put_contents($file, $content);
	
	$userDonation = $_POST['userDonation']; 

}

require('settings.inc.php');

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
	
	if ($timezone == $tz) {
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
					<input type="submit" value="Set"></td>
					</tr>
					<tr>
					<td>TimeZone: </td>
					<td><?php echo $tzselect ?>
					<input type="submit" value="Set"></td>
					</tr>
					<tr>
					<td>Donation (Minutes per 24 hours): </td>
					<td><input type="text" value="<?php echo $donation ?>" name="userDonation">
					<input type="submit" value="Set"></td>



					</tr>
					<table>
					</form>
					
        </div>
		<?php if($donation == 0) { ?>
		<div class="container">
			<fieldset>
				<legend>Plea</legend>
				<p>Please reconsider your decision to give back absolutely nothing to the project that is currently running your miners. A lot of time and effort has gone into making MinePeon what it is today and a small token of 15 minutes of your hash power would be greatly appreciated and will continue to fund the ongoing development and support of MinePeon.  </p>
				<p>It is such a small amount and well below the normal variance in bitcoin mining you will not even notice the difference. If you work it out for every 1 GH/s you have it is 0.00027 bitcoin a day, ask yourself, is that really too much to support MinePeon?  Are you really that cheap?</p>
				<p>Some of the features that I would like to include are;-</p>
				<ul>
					<li>TFT Display</li>
					<li>LCD Display</li>
					<li>Android app</li> 
					<li>iOS app</li>
					<li>Live Update</li>
					<li>SMS/Email Alerts</li>
					<li>Backup/Restore</li>
					<li>Cloud Control</li>
					<li>VPN Tunneling (DDOS Protection & Anonymity)</li>
				</ul>
				<p>Most of those new features cost money to setup and run, I would prefer not to have to make features avalible as 'paied for' addons but it all depends on you.</p>
				<p>Neil Fincham</p>
				<p>The MineForeman</p>
				<p>P.S. Every time you set donations to zero a kitten dies. <marquee direction="left" scrollamount="3" behavior="scroll" style="width: 60px; height: 15px; color: #ff0000; font-size: 11px; text-decoration: blink;">Kitten Killer!</marquee></p>
			</fieldset>
		</div>
		<?php } ?>
<?php

include('foot.php');