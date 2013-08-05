<?php
/*
f_status gets values that people want to see in realtime
returns success, status data and errors
*/
header('Content-type: application/json');

require('miner.inc.php');

$r['load'] = sys_getloadavg()[0];

// Miner data
//$r['summary'] = cgminer("summary", "")['SUMMARY'];
$r['devs'] = cgminer("devs", "")['DEVS'];
$r['pools'] = cgminer("pools", "")['POOLS'];

// Debug miner data
if(isset($_REQUEST['dev'])){
  $r['devs'][]=array("Name"=>"Hoeba","ID"=>0,"Temperature"=>rand(20,35),"MHS5s"=>rand(00,1000000000),"MHSav"=>rand(600000,800000),"LongPoll"=>"N","Getworks"=>200,"Accepted"=>rand(70,200),"Rejected"=>rand(1,10),"HardwareErrors"=>rand(0,50),"Utility"=>1.2,"LastShareTime"=>time()-rand(0,10));
  $r['devs'][]=array("Name"=>"Debug","ID"=>1,"Temperature"=>rand(20,35),"MHS5s"=>rand(0,10000000),"MHSav"=>rand(100000,120000),"LongPoll"=>"N","Getworks"=>1076,"Accepted"=>1324,"Rejected"=>1,"HardwareErrors"=>46,"Utility"=>1.2,"LastShareTime"=>time()-rand(0,40));
  $r['devs'][]=array("Name"=>"Wut","ID"=>2,"Temperature"=>rand(20,35),"MHS5s"=>rand(0,100000),"MHSav"=>rand(6000,8000),"LongPoll"=>"N","Getworks"=>1076,"Accepted"=>1324,"Rejected"=>1,"HardwareErrors"=>46,"Utility"=>1.2,"LastShareTime"=>time()-rand(0,300));
  $r['devs'][]=array("Name"=>"More","ID"=>2,"Temperature"=>rand(20,35),"MHS5s"=>rand(0,1000),"MHSav"=>rand(6000,8000),"LongPoll"=>"N","Getworks"=>1076,"Accepted"=>1324,"Rejected"=>1,"HardwareErrors"=>46,"Utility"=>1.2,"LastShareTime"=>time()-rand(0,300));
}

$devices = 0;
$MHSav = 0;
$MHS5s = 0;
$Accepted = 0;
$Rejected = 0;
$HardwareErrors = 0;
$Utility = 0;

foreach ($r['devs'] as $id => $dev) {
  if ($dev['MHS5s'] > 0) {
    $devices++;
    $MHS5s = $MHS5s + $dev['MHS5s'];
    $MHSav = $MHSav + $dev['MHSav'];
    $Accepted = $Accepted + $dev['Accepted'];
    $Rejected = $Rejected + $dev['Rejected'];
    $HardwareErrors = $HardwareErrors + $dev['HardwareErrors'];
    $Utility = $Utility + $dev['Utility'];
  }
  $r['devs'][$id]['TotalShares']=$dev['Accepted']+$dev['Rejected']+$dev['HardwareErrors'];
}

$r['dtot']=array(
  'devices'=>$devices,
  'MHS5s'=>$MHS5s,
  'MHSav'=>$MHSav,
  'Accepted'=>$Accepted,
  'Rejected'=>$Rejected,
  'HardwareErrors'=>$HardwareErrors,
  'Utility'=>$Utility,
  'TotalShares'=>$Accepted+$Rejected+$HardwareErrors);

// Set q to 0 if not given
if(isset($_REQUEST['all'])){
  $r['uptime'] = explode(' ', exec("cat /proc/uptime"));
  $r['temp'] = substr(exec('/opt/vc/bin/vcgencmd measure_temp'), 5, -2);
}

$r['time'] = time();
$r['success'] = true;

echo json_encode($r);
?>