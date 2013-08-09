<?php
/*
f_backup_list lists all folders in a specified folder
returns success, a list of foldernames and errors
*/
header('Content-type: application/json');

// Default backup folder
$backupFolder='/opt/minepeon/etc/backup/';

// Scan backup folder and remove . & ..
$items = @scandir($backupFolder);
array_shift($items);
array_shift($items);

// Scan subfolders
foreach ($items as $key => $value) {
	$r['data'][$key]['dir']=$value;
	$r['data'][$key]['items']=@scandir($backupFolder."/".$value);
	array_shift($r['data'][$key]['items']);
	array_shift($r['data'][$key]['items']);
}

echo json_encode($r);
?>