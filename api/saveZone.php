<?php

require("config.php");

if(!isset($_GET['title'])){
	
	die("Don't know what zone to save. Please supply a 'title'");
	
}else{

	$title = $_GET['title'];
	$file = "{$settings['data_dir']}/{$title}.zone";

	if(!file_exists($file)){
		$zone = simplexml_load_string("<zone></zone>");
		$zone->addChild("nodes");
		$zone->addChild("exits");
	}else{
		$zone = simplexml_load_file($file);
	}
	
	$zone->addAttribute("title",$title);
	
	if(isset($_GET['nodes'])){
		unset($zone->nodes->node);
		$nodes = explode(";",$_GET['nodes']);
		foreach($nodes as $node){
			$node = explode(",",substr($node,1,-1));
			$child = $zone->nodes->addChild("node");
			$child->addAttribute("lat",$node[0]);
			$child->addAttribute("lng",$node[1]);
		}
	}
	
	$dom = new DOMDocument('1.0');
	$dom->preserveWhiteSpace = false;
	$dom->formatOutput = true;
	$dom->loadXML($zone->asXML());
	
	file_put_contents($file,$dom->saveXML());
	
}

?>
