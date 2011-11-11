<?php

// Title: strip.php
// Description: Strips XML of statuses and reasons for reusing data.
// Author: Samuel Gaus
// Args: 

require("config.php");

$files = scandir($settings['data_dir']);

foreach($files as $filename){
	$ext = pathinfo($filename, PATHINFO_EXTENSION);
	if($ext == "zone" || $ext == "exit"){
		$file = "{$settings['data_dir']}/{$filename}";
		$content = file_get_contents($file);
		$content = preg_replace("|status=\"[^\"]*\" ?|","",$content,-1,$count);
		$content = preg_replace("|reason=\"[^\"]*\" ?|","",$content,-1,$count2);
		$count += $count2;
		if($count > 0){
			$r = file_put_contents($file,$content);
			if(!$r){
				// Write filename instead of file for security(?). Seems safer.
				die("Error writing to file: {$filename}");
			}
		}

	}
}

?>
