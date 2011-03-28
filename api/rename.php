<?php

// Title: rename.php
// Description: Rename and maintain referential integrity
// Author: Samuel Gaus
// Args: 
// 		type = zone|exit,
// 		title = title,
// 		new = newtitle,
// 		del = true|false

require("config.php");

$type=$_GET['type'];
$other = ($type=="zone")?"exit":"zone";

$deleting = ( (isset($_GET['del'])) && ($_GET['del']=="true") )?true:false;
$title = stripslashes($_GET['title']);
$new = stripslashes($_GET['new']);
$file = "{$settings['data_dir']}/" . str_replace("'","^",$title) . ".{$type}";

if(empty($type) || ( ($type!="zone") && ($type!="exit") ) ){
	die("You must specify a valid type");
}

if(!file_exists($file)){
	die("$title ($type) was not found. Cannot rename.");
}

if( empty($new) && (!$deleting) ){
	die("You must also set a new name.");
}

$xml = simplexml_load_file($file);

foreach($xml->xpath("/{$type}/{$other}s/{$other}") as $link){
	if($link['title']!=""){
		//rename me within my linked zones
		$lfile = "{$settings['data_dir']}/" . str_replace("'","^",$link['title']) . ".{$other}";
		$lxml  = simplexml_load_file($lfile);
		//todo MAKE THIS WORK UNDER NEW IMP
		foreach($lxml->xpath("/{$other}/{$type}s/{$type}") as $e){
			if($e['title']==$title){
				if($deleting){
					$d=dom_import_simplexml($e);
					$d->parentNode->removeChild($d);
				}else{
					$e['title'] = $new;
				}
			}
		}
		$ldom = new DOMDocument('1.0');
		$ldom->preserveWhiteSpace = false;
		$ldom->formatOutput = true;
		$ldom->loadXML($lxml->asXML());
		file_put_contents($lfile,$ldom->saveXML());
	}else{
		$d=dom_import_simplexml($link);
		$d->parentNode->removeChild($d);
	}
}

if($deleting){
	rename($file,"{$settings['data_dir']}/deleted/{$title}." . time() . ".{$type}");
}else{
	$xml['title'] = $new;
	file_put_contents($file,$xml->saveXML());
	rename($file,"{$settings['data_dir']}/{$new}.{$type}");
}

?>
