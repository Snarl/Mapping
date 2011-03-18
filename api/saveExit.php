<?php

require("config.php");

if(!isset($_GET['title'])){
	
	die("Don't know what exit to save. Please supply a 'title'");
	
}else{

	$title = $_GET['title'];
	$file = "{$settings['data_dir']}/{$title}.exit";

	if(!file_exists($file)){
		$exit = simplexml_load_string("<exit></exit>");
		$exit->addChild("nodes");
		$exit->addAttribute("title",$title);
	}else{
		$exit = simplexml_load_file($file);
		$exit->title = $title;
	}

	if( (!isset($_GET['nodes'])) || ($_GET['nodes']=='')){
		die("Cannot save a exit without any nodes.");	
	}else{
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
