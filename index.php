<?php

include('ssl.inc.php');
include('timezone.inc.php');
require('miner.inc.php');
include('settings.inc.php');


create_graph("mhsav-hour.png", "-1h", "Hourly");
create_graph("mhsav-day.png", "-1d", "Daily");
create_graph("mhsav-week.png", "-1w", "Weekly");
create_graph("mhsav-month.png", "-1m", "Monthly");
create_graph("mhsav-year.png", "-1y", "Yearly");

function create_graph($output, $start, $title) {
  $RRDPATH = '/opt/minepeon/var/rrd/';
  $options = array(
    "--slope-mode",
    "--start", $start,
    "--title=$title",
    "--vertical-label=Hash per second",
    "--lower=0",
    "DEF:hashrate=" . $RRDPATH . "hashrate.rrd:hashrate:AVERAGE",
    "CDEF:realspeed=hashrate,1000,*",
    "LINE2:realspeed#FF0000"
  );

  $ret = rrd_graph("/opt/minepeon/http/rrd/" . $output, $options);
  if (! $ret) {
    echo "<b>Graph error: </b>".rrd_error()."\n";
  }
}

$stats = cgminer("devs", "");
$status = $stats['STATUS'];
$devs = $stats['DEVS'];



include('head.php');
include('menu.php');
?>

	<div>
		<?php if($donation == 0) { ?><embed height="0" width="0" src="inc/kitten.mp3"><?php } ?>
	</div>
	<div class="container">
		<h1>Graphs</h1>
			<center>Update Time: <?php echo date('D, d M Y H:i:s T') ?><center>
			<div class=graph><img src="rrd/mhsav-hour.png" alt="mhsav.png" /></div>
			<div class=graph><img src="rrd/mhsav-day.png" alt="mhsav.png" /><img src="rrd/mhsav-week.png" alt="mhsav.png" /></div>
		<div class=graph><img src="rrd/mhsav-month.png" alt="mhsav.png" /><img src="rrd/mhsav-year.png" alt="mhsav.png" /></div>
	</div>


	<div class="container">
		<table id="stats" class="tablesorter table table-striped table-bordered table-hover table-condensed stats">
			<thead> 
			<tr>
				<th>Name</th>
				<th>ID</th>
				<th>Temp</th>
				<th>MH/s</th>
				<th>Accept</th>
				<th>Reject</th>
				<th>Error</th>
				<th>Utility</th>
				<th>Last Share Time</th>
			</tr>
			</thead>
			<tbody>
			<?php echo statsTable($devs);  ?>
		</table>

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

function statsTable($devs) {

	$devices = 0;
	$MHSav = 0;
	$Accepted = 0;
	$Rejected = 0;
	$HardwareErrors = 0;
	$Utility = 0;

	$tableRow = "";

	foreach ($devs as $dev) {
		
		if ($dev['MHS5s'] > 0) {
		$tableRow = $tableRow . "

			<tr>
				<td>" . $dev['Name'] . "</td>
				<td>" . $dev['ID'] . "</td>
				<td>" . $dev['Temperature'] . "</td>
				<td>" . $dev['MHSav'] . "</td>
				<td>" . $dev['Accepted'] . "</td>
				<td>" . $dev['Rejected'] . "</td>
				<td>" . $dev['HardwareErrors'] . "</td>
				<td>" . $dev['Utility'] . "</td>
				<td>" . date('H:i:s', $dev['LastShareTime']) . "</td>
			</tr>";
			
		$devices++;
		$MHSav = $MHSav + $dev['MHSav'];
		$Accepted = $Accepted + $dev['Accepted'];
		$Rejected = $Rejected + $dev['Rejected'];
		$HardwareErrors = $HardwareErrors + $dev['HardwareErrors'];
		$Utility = $Utility + $dev['Utility'];
		
		}
	}
	
	$tableRow = $tableRow . "
		</tbody>
		<tfoot>
			<tr>
				<th>Totals</th>
				<th>" . $devices . "</th>
				<th></td>
				<th>" . $MHSav . "</th>
				<th>" . $Accepted . "</th>
				<th>" . $Rejected . "</th>
				<th>" . $HardwareErrors . "</th>
				<th>" . $Utility . "</th>
				<th></th>
			</tr>
		</tfoot>
";
		
	

	return $tableRow;

}
