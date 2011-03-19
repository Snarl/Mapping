<?php

// Title: saveZoneStatus.php
// Description: Save a status and a reason for that status to a zone
// Author: Samuel Gaus
// Args: title, status, reason

require("config.php");

if(!isset($_GET['title'])){
	die("Don't know what zone to change the status for. Please supply a 'title'");
}elseif(!isset($_GET['status'])){
	die("You must set a status of red, yellow (or orange or amber), green");
}else{

	$title = $_GET['title'];
	$file = "{$settings['data_dir']}/{$title}.zone";

	if(!file_exists($file)){
		die("{$title} does not exist. Cannot change the status of a non-existant zone. What would that even entail?");
	}
	
	$status = $_GET['status'];
	
	switch($status){
		case "red":
			$status = "red";
			break;
		case "orange":
		case "yellow":
		case "amber":
			$status = "orange";
			break;
		case "green":
			$status = "green";
			break;
		default:
			if( (!is_numeric($status)) || ($status>100) || ($status < 0) ){
				die("Status not understood. Red, yellow (or orange or amber), green");
			}
			break;
	}
	
	$zone = simplexml_load_file($file);
	$zone['status'] = $status; //status (red orange or green)
	$zone['reason'] = $_GET['reason']; //any string
	
	$dom = new DOMDocument('1.0');
	$dom->preserveWhiteSpace = false;
	$dom->formatOutput = true;
	$dom->loadXML($zone->asXML());
	
	file_put_contents($file,$dom->saveXML());
	
}

?>
