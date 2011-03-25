<?php

// Title: saveZone.php
// Description: Save information about a zone
// Author: Samuel Gaus
// Args: title, nodes ("lat,lng;lat,lng;....")

require("config.php");

if(isset($argv)){
	array_shift($argv);
	foreach($argv as $v){
		$q = explode("=",$v);
		$_GET[$q[0]] = urldecode($q[1]);
	}
}

if(!isset($_GET['title'])){
	
	die("Don't know what zone to save. Please supply a 'title'");
	
}else{

	$title = stripslashes($_GET['title']);
	$file = "{$settings['data_dir']}/" . str_replace("'","^",$title) . ".zone";
	
	if(!file_exists($file)){
		$zone = simplexml_load_string("<zone></zone>");
		$zone->addChild("nodes");
		$zone->addChild("exits");
		$zone->addAttribute("title",$title);
		$new = true;
	}else{
		$zone = simplexml_load_file($file);
		$zone['title'] = $title;
		$new = false;
	}
	
	if(isset($_GET['links'])){
		//Add list of linked exits
		if( (!isset($_GET['append'])) || ($_GET['append']!=true) ){
			unset($zone->exits->exit);
		}
		$exits = explode(";",$_GET['links']);
		foreach($exits as $exit){
			$child = $zone->exits->addChild("exit");
			$child->addAttribute("title",$exit);
			//then add this exit to that zone
			if( (!isset($_GET['int'])) || ($_GET['int']!=true) ){
				exec("php ./saveExit.php title=".urlencode($exit)." links=".urlencode($zone['title'])." int=true append=true");
			}
		}
	}

	if( (!isset($_GET['nodes'])) || ($_GET['nodes']=='')){
		if($new){
			die("Cannot save a zone without any nodes.");	
		}
	}else{
		unset($zone->nodes->node);
		$nodes = explode(";",$_GET['nodes']);
		if(count($nodes)==0){
			die("Cannot save a zone without any nodes.");
		}
		$maxlat = $maxlng = $minlat = $min_lng = "";
		foreach($nodes as $node){
			list($lat,$lng) = explode(",",$node);
			$maxlat = (empty($maxlat)||($lat>$maxlat))?$lat:$maxlat;
			$maxlng = (empty($maxlng)||($lng>$maxlng))?$lng:$maxlng;
			$minlat = (empty($minlat)||($lat<$minlat))?$lat:$minlat;
			$minlng = (empty($minlng)||($lng<$minlng))?$lng:$minlng;
			$child = $zone->nodes->addChild("node");
			$child->addAttribute("lat",$lat);
			$child->addAttribute("lng",$lng);
		}
		$zone->nodes["maxlat"] = $maxlat;
		$zone->nodes["maxlng"] = $maxlng;
		$zone->nodes["minlat"] = $minlat;
		$zone->nodes["minlng"] = $minlng;
	}
	
	$dom = new DOMDocument('1.0');
	$dom->preserveWhiteSpace = false;
	$dom->formatOutput = true;
	$dom->loadXML($zone->asXML());
	
	file_put_contents($file,$dom->saveXML());
	
}

?>
