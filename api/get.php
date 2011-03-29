<?php

// Title: get.php
// Description: Get a list of requested datatype served as JSON
// Author: Samuel Gaus
// Args: 
// 		type = String(zone|exit)

	require("config.php");

	require("xml2array.php");

	$type = $_GET['type'];
	
	if( (empty($type)) || ($type!="exit" && $type!="zone") ){
		die("You must specify zones or exits to be returned");
	}

	$json = array();

	foreach(glob("{$settings['data_dir']}/*.{$type}") as $file){
		$arr = simpleXMLtoArray(simplexml_load_file($file));
		$json[$arr['title']] = $arr;
	}
	
	$response = json_encode($json);
	
	header("Content-Length: ".strlen($response));
	
	echo($response);

?>
