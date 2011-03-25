<?php

// Title: saveExitStatus.php
// Description: Save a status and a reason for that status to an exit
// Author: Samuel Gaus
// Args: title, status, reason

require("config.php");

if(!isset($_GET['title'])){
	die("Don't know what exit to change the status for. Please supply a 'title'");
}elseif(!isset($_GET['status'])){
	die("You must set a status of red, yellow (or orange or amber), green or a number between 0 (red) and 100 (green)");
}else{

	$title = $_GET['title'];
	$file = "{$settings['data_dir']}/" . str_replace("'","^",$title) . ".exit";

	if(!file_exists($file)){
		die("{$title} does not exist. You can't set the status of a non-existant exit. Silly.");
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
				die("Status not understood. Red, yellow (or orange or amber), green or a number between 0 (red) and 100 (green).");
			}
			break;
	}
	
	$exit = simplexml_load_file($file);
	$exit['status'] = $status; //expecting number between 0 and 100
	$exit['reason'] = $_GET['reason']; //any string
	
	$dom = new DOMDocument('1.0');
	$dom->preserveWhiteSpace = false;
	$dom->formatOutput = true;
	$dom->loadXML($exit->asXML());
	
	file_put_contents($file,$dom->saveXML());
	
}

?>
