<?php

// Title: renameExit.php
// Description: Rename exit and maintain referential integrity
// Author: Samuel Gaus
// Args: title, new, del (true,false)

require("config.php");

$deleting = ( (isset($_GET['del'])) && ($_GET['del']=="true") )?true:false;

$title = $_GET['title'];
$new = $_GET['new'];

$file = "{$settings['data_dir']}/" . str_replace("'","^",$title) . ".exit";

if(!file_exists($file)){
	die("That zone was not found. Cannot rename.");
}

if( ( (!isset($_GET['new'])) || ($new == "") ) && (!$deleting) ){
	die("You must set a new name for the exit.");
}

$exit = simplexml_load_file($file);

foreach($exit->zones->zone as $zone){
	//rename me within my linked zones
	$zfile = "{$settings['data_dir']}/" . str_replace("'","^",$zone['title']) . ".zone";
	$zxml  = simplexml_load_file($zfile);
	foreach($zxml->exits->exit as $e){
		if($e['title']==$title){
			if($deleting){
				$d=dom_import_simplexml($e);
				$d->parentNode->removeChild($d);
			}else{
				$e['title'] = $new;
			}
		}
	}
	$zdom = new DOMDocument('1.0');
	$zdom->preserveWhiteSpace = false;
	$zdom->formatOutput = true;
	$zdom->loadXML($zxml->asXML());
	file_put_contents($zfile,$zdom->saveXML());
}

if($deleting){
	rename($file,"{$settings['data_dir']}/deleted/{$title}." . time() . ".zone");
}else{
	$exit['title'] = $new;
	file_put_contents($file,$exit->saveXML());
	rename($file,"{$settings['data_dir']}/{$new}.exit");
}

?>
