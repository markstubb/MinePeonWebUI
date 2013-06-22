<?php

$file = "/opt/minepeon/etc/minepeon.conf";

// read the miner config file

// $settings = json_decode(file_get_contents($file), true);

// decode the json
//$data = json_decode($minerConf, true);

/*
$settings = array(
                "timezone" => "timezone",
                "other" => "other",
)
*/	
// Recode into JSON and save
file_put_contents($file, json_encode($settings));

