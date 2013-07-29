<?php
error_reporting(E_ALL);
header('Content-type: application/json');

// Check for POST or GET data
if (empty($_REQUEST['saving']) or !$_REQUEST['saving']) {
	echo json_encode(array('success' => false, 'debug' => "Not saving"));
	exit;
}

//initialize a limit to the number of pools that are added to the miner config file. is there an official limit?
$poolLimit = 20;

// as long as the POST URL, USER and PASS data are present and the count is under the poolLimit, process the POST data
$j = 0;
while (!empty($_REQUEST['URL'.$j.'']) and !empty($_REQUEST['USER'.$j.'']) and $j<$poolLimit) {

	// Set pool at j
	$dataPools[$j] = array(
		"label" => $_REQUEST['LABEL'.$j],
		"url" => $_REQUEST['URL'.$j],
		"user" => $_REQUEST['USER'.$j],
		"pass" => $_REQUEST['PASS'.$j],
		);

	// debug output
	// echo $_REQUEST['URL'.$j.''] . $_REQUEST['USER'.$j.''] . $_REQUEST['PASS'.$j.''];

	// increment count
	$j++;
}

$written = 0;

// Recode into JSON and save
// Never save if no pools given
if (!empty($dataPools)) {
	// Read current config
	$data = json_decode(file_get_contents("/opt/minepeon/etc/miner.conf", true), true);
	// Unset currect
	unset($data['pools']);
	// Set new pool data
	$data['pools']=$dataPools;
	// Write back to file
	$written = file_put_contents("/opt/minepeon/etc/miner.conf", json_encode($data, JSON_PRETTY_PRINT));
}

echo json_encode(array('success' => true, 'written' => $written, 'debug' => $dataPools));
?>