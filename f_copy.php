<?php
header('Content-type: application/json');

// Check for POST or GET data
if (empty($_REQUEST['src']) || empty($_REQUEST['dst'])) {
	echo json_encode(array('success' => false, 'debug' => "No src/dst given"));
	exit;
}

$src = $_REQUEST['src'];
$dst = $_REQUEST['dst'];

function recurse_copy($src,$dst,$level=3) {
	$success=true;
	if($level==0) return $success;
	global $r;
	$dir = opendir($src); 
	@mkdir($dst); 
	while(false !== ( $file = readdir($dir))) { 
		if (( $file != '.' ) && ( $file != '..' )) { 
			if ( is_dir($src . '/' . $file) ) { 
				$c=recurse_copy($src . '/' . $file,$dst . '/' . $file,$level-1); 
			} 
			else {
				$c=copy($src . '/' . $file,$dst . '/' . $file);
				$r["files"][]=array("file"=>pathinfo($dst,PATHINFO_FILENAME),"success"=>$c);
			}
			$success=$success&&$c;
		} 
	} 
	closedir($dir);
	return $success;
}

// Check destination folder
$dstDir=dirname($dst);
$r['success']=false;
$r['src']=pathinfo($dst,PATHINFO_FILENAME);
$r["dstDir"]=$dstDir;

// Make dir if there isnt
if (!is_writable($dstDir)) {
	$r["mkDir"]="Making destination dir!";
	@mkdir($dstDir,0777,true); 
}

// Predicting errors for debugging
if (!is_readable($src)) {
	$r['error'][]="Source file is not readable";
}
elseif (is_file($src)&&is_dir($dst)) {
	$r['error'][]="File and folder mixup";
}
elseif (is_dir($src)&&is_file($dst)) {
	$r['error'][]="File and folder mixup";
}
elseif (is_file($dst)) {
	$r['error'][]="Destination file already exists";
}

// Copy it already!
if (is_file($src)) {
	$r['type']="file";
	if (!copy($src, $dst)) {
		$r["files"][0]=array("file"=>pathinfo($dst,PATHINFO_FILENAME),"success"=>false);
	}
	else{
		$r['success']=true;
		$r["files"][0]=array("file"=>pathinfo($dst,PATHINFO_FILENAME),"success"=>true);
	}
}
elseif (is_dir($src)) {
	$r['type']="dir";
	if (recurse_copy($src, $dst)) {
		$r['success']=true;
	}
}

// Return success and more data
echo json_encode($r);
?>
