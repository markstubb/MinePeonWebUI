<?php
/*
f_status gets values that people want to see in realtime
returns success, status data and errors
*/
header('Content-type: application/json');

require('miner.inc.php');

// Miner data
//$export['summary'] = cgminer("summary", "")['SUMMARY'];
$export['devs'] = cgminer("devs", "")['DEVS'];
$export['pools'] = cgminer("pools", "")['POOLS'];


$devices = 0;
$MHSav = 0;
$Accepted = 0;
$Rejected = 0;
$HardwareErrors = 0;
$Utility = 0;

foreach ($devs as $dev) {
  if ($dev['MHS5s'] > 0) {
    $devices++;
    $MHSav = $MHSav + $dev['MHSav'];
    $Accepted = $Accepted + $dev['Accepted'];
    $Rejected = $Rejected + $dev['Rejected'];
    $HardwareErrors = $HardwareErrors + $dev['HardwareErrors'];
    $Utility = $Utility + $dev['Utility'];
  }
}

$export['dtot']=array(
	'devices'=>$devices,
	'MHSav'=>$MHSav,
	'Accepted'=>$Accepted,
	'Rejected'=>$Rejected,
	'HardwareErrors'=>$HardwareErrors,
	'Utility'=>$Utility,
	'TotalShares'=>$Accepted+$Rejected+$HardwareErrors);

$export['success'] = true;

echo json_encode($export);
?>