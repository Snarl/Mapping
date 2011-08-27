<?php

/*--------------------------
Configuration - Sukey Mapping API
---------------------------*/

// This is the absolute server path to base directory. See README for more information.
$settings['base_dir'] = "/path/to/mapping";
// This is the absolute server path to the data directory
$settings['data_dir'] = $settings['base_dir'] . "/data";

// DO NOT CHANGE ANYTHING BELOW THIS LINE

function callAPI($call){
	$url = 'http';
	if ($_SERVER["HTTPS"] == "on") { $url .= "s";}
	$url .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$url .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$url .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	$url = substr($url,0,strrpos($url,"/"));
	//now add the api call
	$url .= "/" . $call;
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$output = curl_exec($ch);       
	curl_close($ch);
	return $output;
}

?>
