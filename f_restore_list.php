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
			$r['folders'][]['name']=$name;
		}
	}
	closedir($handle);
}
else{
	$r['error']="Could not read backupFolder.";
}

$r['success']=(!empty($r['folders']));

echo json_encode($r);
?>