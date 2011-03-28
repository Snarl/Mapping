<?php

$settings['base']     = "/maps26/";
$settings['base_dir'] = "/home/sukey/public_html/maps26";
$settings['data_dir'] = $settings['base_dir'] . "/data";

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
