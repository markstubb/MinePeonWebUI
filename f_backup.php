<?php
/*
- default: List all folders in a specified folder
- download: Zip a folder and serve as download
*/
header('Content-type: application/json');

// Default backup folder
$backupFolder='/opt/minepeon/etc/backup';



// Zip a folder and serve as download
if (!empty($_REQUEST['download'])) {
	$zipFolder='/opt/minepeon/etc/';
	$zipData='backup/'.$_REQUEST['download'];
	$zipName='minepeon-'.$_REQUEST['download'].'.zip';

	if(!file_exists($zipFolder.$zipName)){
		exec('cd '.$zipFolder.$zipData.' ; zip -r '.$zipFolder.$zipName.' ./');
	}
	header('Content-Type: application/zip');
	header('Content-Disposition: attachment; filename=' . basename($zipFolder.$zipName) . ';' );
	header('Content-Transfer-Encoding: binary');
	header('Content-Length: ' . filesize($zipFolder.$zipName));
	readfile($zipFolder.$zipName);
	unlink($zipFolder.$zipName);
	exit;
}

// Get a list of all backups and their content
else{
	// Scan backup folder and remove . & ..
	$items = @scandir($backupFolder);
	if(!empty($items)){
		array_shift($items);
		array_shift($items);
	}
	rsort($items);

	// Scan subfolders, in the future this should also return the files
	foreach ($items as $key => $value) {
		$r['data'][$key]['dir']=$value;
		$r['data'][$key]['items']=@scandir($backupFolder.'/'.$value);
		if(!empty($r['data'][$key]['items'])){
			array_shift($r['data'][$key]['items']);
			array_shift($r['data'][$key]['items']);
		}
	}
}

echo json_encode($r);
?>