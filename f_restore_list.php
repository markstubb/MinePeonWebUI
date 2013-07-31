<?php
/*
f_backup_list lists all folders in a specified folder
returns success, a list of foldernames and errors
*/
header('Content-type: application/json');

// Default backup folder
$backupFolder='/opt/minepeon/etc/backup/';

// Read folder
if ($handle = opendir($backupFolder)) {
	while (false !== ($name = readdir($handle))) {
		// Save all foldernames
		if(is_dir($backupFolder.$name) && strpos($name, '.')===false){
			$export['folders'][]['name']=$name;
		}
	}
	closedir($handle);
}
else{
	$export['error']="Could not read backupFolder.";
}

$export['success']=(!empty($export['folders']));

echo json_encode($export);
?>