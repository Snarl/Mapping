<?php

	require("config.php");

	require("xml2array.php");

	$zonefiles = array_values(array_filter(scandir($settings['data_dir']), function($v){
		return (array_pop(explode(".",$v)))?true:false;
	}));

	$zones = array();

	foreach($zonefiles as $zone){
		$arr = simpleXMLtoArray(simplexml_load_file("{$settings['data_dir']}/{$zone}"));
		$zones[$arr['title']] = $arr;
	}
	
	echo(json_encode($zones));

?>
