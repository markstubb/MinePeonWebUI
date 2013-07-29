<?php
error_reporting(E_ALL);
header('Content-type: application/json');

// Check for POST or GET data
if (empty($_REQUEST['command'])) {
	echo json_encode(array('success' => false, 'debug' => "No command given"));
	exit;
}

$command = array ("command" => $_REQUEST['command']);
$debug[] = "Command: ".$_REQUEST['command'];

// Check for parameters
if (empty($_REQUEST['parameter'])) {
	$debug[] = "No parameter given";
}
else{
	$command['parameter']=$_REQUEST['parameter'];
	$debug[] = "Parameter: ".$_REQUEST['parameter'];
}

// Prepare socket
$jsonCmd = json_encode($command);
$host = "127.0.0.1";
$port = 4028;

// Setup socket
$client = stream_socket_client("tcp://$host:$port", $errno, $errorMessage);

if ($client === false) {
	throw new UnexpectedValueException("Failed to connect: $errorMessage");
}
fwrite($client, $jsonCmd);
$response = stream_get_contents($client);
fclose($client);
$response = json_decode($response, true);

// Return data
$response['success']=true;
$response['debugpeon']=$debug;
echo json_encode($response);
?>