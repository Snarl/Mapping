<?php

require("config.php");

if(!isset($_GET['title'])){
	die("Don't know what zone to change the status for. Please supply a 'title'");
}elseif(!isset($_GET['status'])){
	die("You must set a status of red, yellow (or orange or amber), green or a number between 0 (red) and 100 (green)");
}else{

	$title = $_GET['title'];
	$file = "{$settings['data_dir']}/{$title}.zone";

	if(!file_exists($file)){
		die("{$title} does not exist. Cannot change the status of a non-existant zone. What would that even entail?");
	}
	
	$status = $_GET['status'];
	
	switch($status){
		case "red":
			$status = 0;
			break;
		case "orange":
		case "yellow":
		case "amber":
			$status = 50;
			break;
		case "green":
			$status = 100;
			break;
		default:
			if( (!is_numeric($status)) || ($status>100) || ($status < 0) ){
				die("Status not understood. Red, yellow (or orange or amber), green or a number between 0 (red) and 100 (green).");
			}
			break;
	}
	
	$zone = simplexml_load_file($file);
	$zone->addAttribute("status",$status); //expecting number between 0 and 100
	$zone->addAttribute("reason",$_GET['reason']); //any string
	
	$dom = new DOMDocument('1.0');
	$dom->preserveWhiteSpace = false;
	$dom->formatOutput = true;
	$dom->loadXML($zone->asXML());
	
	file_put_contents($file,$dom->saveXML());
	
}

?>
