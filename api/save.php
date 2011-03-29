<?php

// Title: save.php
// Description: Save information about a zone or exit
// Author: Samuel Gaus
// Args: 
// 		type  	= String(zone|exit)
// 		title 	= String()
// 		nodes 	= String(Lat,Lng;Lat,Lng;...)
// 		links 	= String(Title;Title;...)
// 		append 	= Boolean()
// 		int		= Boolean()

require("config.php");

$title 	= stripslashes($_GET['title']);
$type 	= $_GET['type'];
$nodes 	= $_GET['nodes'];
$links 	= $_GET['links'];
$append = $_GET['append'];
$int 	= $_GET['int'];

$other = ($type=="zone")?"exit":"zone";

if(empty($title)){	
	die("Don't know what to save. Please supply a 'title'");	
}

if(empty($type) || ($type!="zone" && $type!="exit") ){
	die("Type must be zone or exit");
}

$file = "{$settings['data_dir']}/" . str_replace("'","^",$title) . ".{$type}";

if(!file_exists($file)){
	$xml = simplexml_load_string("<{$type}></{$type}>");
	$xml->addChild("nodes");
	$xml->addChild("{$other}s");
	$xml->addAttribute("title",$title);
	$new = true;
}else{
	$xml = simplexml_load_file($file);
	$xml['title'] = $title;
	$new = false;
}

if(!empty($links)){
	//Add list of links
	if( empty($append) || ($append==false) ){
		$old = $xml->xpath("//{$type}/{$other}s/{$other}");
		foreach($old as $oldlink){
			$d=dom_import_simplexml($oldlink);
			$d->parentNode->removeChild($d);
		}
	}
	$links = explode(";",$links);
	$others = $xml->xpath("//{$type}/{$other}s");
	foreach($links as $link){
		$child = $others[0]->addChild($other);
		$child->addAttribute("title",$link);
		//then add this exit to that zone
		if( empty($int) || ($int==false) ){
			callAPI("./save.php?type={$other}&title=".urlencode($link)."&links=".urlencode($xml['title'])."&int=true&append=true");
		}
	}
}

//todo Use append rules for nodes too. Don't know why though...
if(empty($nodes)){
	if($new){
		die("Cannot save a {$type} without any nodes.");	
	}
}else{
	unset($xml->nodes->node);
	$nodes = explode(";",$nodes);
	if(count($nodes)==0){
		die("Cannot save a {$type} without any nodes.");
	}
	$maxlat = $maxlng = $minlat = $min_lng = "";
	foreach($nodes as $node){
		list($lat,$lng) = explode(",",$node);
		$maxlat = (empty($maxlat)||($lat>$maxlat))?$lat:$maxlat;
		$maxlng = (empty($maxlng)||($lng>$maxlng))?$lng:$maxlng;
		$minlat = (empty($minlat)||($lat<$minlat))?$lat:$minlat;
		$minlng = (empty($minlng)||($lng<$minlng))?$lng:$minlng;
		$child = $xml->nodes->addChild("node");
		$child->addAttribute("lat",$lat);
		$child->addAttribute("lng",$lng);
	}
	$xml->nodes["maxlat"] = $maxlat;
	$xml->nodes["maxlng"] = $maxlng;
	$xml->nodes["minlat"] = $minlat;
	$xml->nodes["minlng"] = $minlng;
}

$dom = new DOMDocument('1.0');
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;
$dom->loadXML($xml->asXML());

file_put_contents($file,$dom->saveXML());

?>
