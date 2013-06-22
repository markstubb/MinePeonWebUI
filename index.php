<?php

include('ssl.inc.php');
include('timezone.inc.php');
require('miner.inc.php');

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

$stats = cgminer("devs");
$status = $stats['STATUS'];
$devs = $stats['DEVS'];



include('head.php');
include('menu.php');
?>

	<div>
		<center>Update Time: <?php echo date('D, d M Y H:i:s T') ?><center>
	</div>
	<div class="container">
		<h1>Graphs</h1>
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
			</tbody> 
		</table>

	</div>
<?php

include('foot.php');

function statsTable($devs) {

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
				<td>" . date('h:i:s', $dev['LastShareTime']) . "</td>
			</tr>";
			
		
		}
	}

	return $tableRow;

}
