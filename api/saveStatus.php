<?php

// Title: saveStatus.php
// Description: Save a status and a reason for that status
// Author: Samuel Gaus
// Args: 
// 		type=zone|exit, 
//		title=title, 
//		status=red|green|orange|yellow|amber, 
// 		reason=reason

require("config.php");

// Check that required variables have been set
if(empty($_GET['type'])){
	die("You must specify a type (either 'zone' or 'exit')");
}elseif(empty($_GET['title'])){
	die("Don't know what to change the status for. Please supply a title.");
}elseif(empty($_GET['status'])){
	die("You must set a status ('red','orange' or 'green'");
}

// Easify variables
$title = $_GET['title'];
$type = $_GET['type'];
$reason = $_GET['reason'];
$status = preg_replace("/yellow|amber/","orange",$_GET['status']);
$file = "{$settings['data_dir']}/" . str_replace("'","^",$title) . ".{$type}";

// Check that type is recognised
if( ($type!="exit") && ($type!="zone") ){
	die("The type must be either 'zone' or 'exit'");
}

if( ($status!="red") && ($status!="orange") && ($status!="green") ){
	die("Your status must be either 'red', 'orange' or 'green'.");
}

if(!file_exists($file)){
	die("{$title} (as a {$type}) does not exist.");
}

$xml = simplexml_load_file($file);
$xml['status'] = $status; //status (red orange or green)
$xml['reason'] = $reason; //any string
$xml['timestamp'] = time(); //time to check for staleness

$dom = new DOMDocument('1.0');
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;
$dom->loadXML($xml->asXML());

file_put_contents($file,$dom->saveXML());

?>
