<?php

// Title: saveExit.php
// Description: Save information about an exit
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
	
	die("Don't know what exit to save. Please supply a 'title'");
	
}else{

	$title = stripslashes($_GET['title']);
	$file = "{$settings['data_dir']}/" . str_replace("'","^",$title) . ".exit";

	if(!file_exists($file)){
		$exit = simplexml_load_string("<exit></exit>");
		$exit->addChild("nodes");
		$exit->addChild("zones");
		$exit->addAttribute("title",$title);
		$new = true;
	}else{
		$exit = simplexml_load_file($file);
		$exit['title'] = $title;
		$new = false;
	}

	if(isset($_GET['links'])){
		//Add list of linked zones
		if( (!isset($_GET['append'])) || ($_GET['append']!=true) ){
			unset($exit->zones->zone);
		}
		$zones = explode(";",$_GET['links']);
		foreach($zones as $zone){
			$child = $exit->zones->addChild("zone");
			$child->addAttribute("title",$zone);
			//then add this exit to that zone, but don't bounce around infinitely
			if( (!isset($_GET['int'])) || ($_GET['int'] != true) ){
				//exec("php ./saveZone.php title=".urlencode($zone)." links=".urlencode($exit['title'])." int=true append=true");
				callAPI("saveZone.php?title=".urlencode($zone)."&links=".urlencode($exit['title'])."&int=true&append=true");
			}
		}
	}

	if( (!isset($_GET['nodes'])) || ($_GET['nodes']=='')){
		if($new){
			die("Cannot save a exit without any nodes.");	
		}
	}else{
		//Add list of nodes
		unset($exit->nodes->node);
		$nodes = explode(";",$_GET['nodes']);
		if(count($nodes)==0){
			die("Cannot save a exit without any nodes.");
		}
		foreach($nodes as $node){
			$node = explode(",",$node);
			$child = $exit->nodes->addChild("node");
			$child->addAttribute("lat",$node[0]);
			$child->addAttribute("lng",$node[1]);
		}
	}
	
	$dom = new DOMDocument('1.0');
	$dom->preserveWhiteSpace = false;
	$dom->formatOutput = true;
	$dom->loadXML($exit->asXML());
	
	file_put_contents($file,$dom->saveXML());
	
}

?>
