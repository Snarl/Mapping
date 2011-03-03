<?php

	require("config.php");

	$zones = array_values(array_filter(scandir($settings['data_dir']), function($v){
		return (array_pop(explode(".",$v)))?true:false;
	}));

	print_r($zones);

?>
