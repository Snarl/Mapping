<?php

// Title: renameZone.php
// Description: Rename zone and maintain referential integrity
// Author: Samuel Gaus
// Args: title, new, del (true,false)

require("config.php");

$deleting = ( (isset($_GET['del'])) && ($_GET['del']=="true") )?true:false;

$title = stripslashes($_GET['title']);
$new = stripslashes($_GET['new']);

$file = "{$settings['data_dir']}/" . str_replace("'","^",$title) . ".zone";

if(!file_exists($file)){
	die("That zone was not found. Cannot rename.");
}

if( ( (!isset($_GET['new'])) || ($new == "") ) && (!$deleting) ){
	die("You must set a new name for the zone.");
}

$zone = simplexml_load_file($file);

foreach($zone->exits->exit as $exit){
	//rename me within my linked exits
	$efile = "{$settings['data_dir']}/" . str_replace("'","^",$exit['title']) . ".exit";
	$exml  = simplexml_load_file($efile);
	foreach($exml->zones->zone as $z){
		if($z['title']==$title){
			if($deleting){
				$d=dom_import_simplexml($z);
				$d->parentNode->removeChild($d);
			}else{
				$z['title'] = $new;
			}
		}
	}
	$edom = new DOMDocument('1.0');
	$edom->preserveWhiteSpace = false;
	$edom->formatOutput = true;
	$edom->loadXML($exml->asXML());
	file_put_contents($efile,$edom->saveXML());
}

if($deleting){
	rename($file,"{$settings['data_dir']}/deleted/{$title}." . time() . ".zone");
}else{
	$zone['title'] = $new;
	file_put_contents($file,$zone->saveXML());
	rename($file,"{$settings['data_dir']}/{$new}.zone");
}

?>
