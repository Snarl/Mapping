<?php

require("config.php");

if(!isset($_GET['title'])){
	
	die("Don't know what zone to save. Please supply a 'title'");
	
}else{

	$title = $_GET['title'];
	$file = $sfile = "{$settings['data_dir']}/{$title}.zone";

	if(!file_exists($file)){
		$file = "{$settings['data_dir']}/zone.example";
	}

	$zone = simplexml_load_file($file);
	
	$zone->title = $title;
	
	if(isset($_GET['coords'])){
		//wipe old coords and add new
	}
	
	print_r($xml);
	
}

?>
