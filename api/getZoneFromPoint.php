<?php

// Title: getZoneFromPoint.php
// Description: Determine in what zone a specific point lies (if any)
// Author: Samuel Gaus
// Args: point ("lat,lng")

require("config.php");

if(isset($_GET['point'])){
	list($lat,$lng) = explode(',',$_GET['point']);
}
//Loop through zones and if we're within the bounding box, test that zone.
$zonefiles = array_values(array_filter(scandir($settings['data_dir']), function($v){
	return (array_pop(explode(".",$v))=="zone")?true:false;
}));

foreach($zonefiles as $zone){
	$z = simplexml_load_file("{$settings['data_dir']}/{$zone}");
	$n = $z->nodes;
	if( ($lat <= $n['maxlat']) && ($lat >= $n['minlat']) && ($lng <= $n['maxlng']) && ($lng >= $n['minlng']) ){
		//It's in the bounding box so test the point
		if(isPointInZone($lat,$lng,$n)){
			die($z['title']);
		}
	}
}

function isPointInZone($x, $y, $xml){
	$crosses = 0;
	$o['x'] = $o['y'] = null;
    foreach($xml->node as $node){
		// Determine start and end points of the line
		$a['x'] = $o['x'];
		$a['y'] = $o['y'];   
		$o['x'] = (double) $node['lat'];
		$o['y'] = (double) $node['lng'];
		if($a['x'] == null){
			$last = $xml->node[($xml->node->count())-1];
			$a['x'] = (double) $last['lat'];
			$a['y'] = (double) $last['lng'];
		}
		// Make sure a is 'below' b
		//echo "{$a['x']},{$a['y']} and {$o['x']},{$o['y']}";
		
		if($a['y']<$o['y']){
			$b = $o;
		}else{
			//echo " (flip) ";
			$b = $a;
			$a = $o;
		}
		//echo " changed to {$a['x']},{$a['y']} and {$b['x']},{$b['y']}\n";
		// Avoid breaking if person is exactly on vertex
		if($y == $a['y'] || $y == $b['y']){
			$y += 0.0000000001; //9 zeroes after dp
		}
		// Now work it out
		if( ($y < $a['y']) || ($y > $b['y']) ){
			continue;
		}elseif($x > max($a['x'],$b['x'])){
			continue;
		}else{
			if($x < min($a['x'],$b['x'])){
				$crosses++;
			}else{
				$m1 = ($a['x']!=$b['x'])?(($b['y']-$a['y'])/($b['x']-$a['x'])):INF;
				$m2 = ($a['x']!=$x     )?(($y     -$a['y'])/($p     -$a['x'])):INF;
				if ($m2 >= $m1){
					$crosses++;
				}
			}
		}
	}
	return ($crosses%2!=0);
}

die("Unknown Zone");

?>
