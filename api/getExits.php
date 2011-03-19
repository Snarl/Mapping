<?php

// Title: getExits.php
// Description: Get a list of exits served as JSON
// Author: Samuel Gaus
// Args: 

	require("config.php");

	require("xml2array.php");

	$exitfiles = array_values(array_filter(scandir($settings['data_dir']), function($v){
		return (array_pop(explode(".",$v))=="exit")?true:false;
	}));

	$exits = array();

	foreach($exitfiles as $exit){
		$arr = simpleXMLtoArray(simplexml_load_file("{$settings['data_dir']}/{$exit}"));
		$exits[$arr['title']] = $arr;
	}
	
	echo(json_encode($exits));

?>
