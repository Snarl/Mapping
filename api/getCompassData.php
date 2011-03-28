<?php

// Title: getCompassData.php
// Description: Returns XML containing set of triplets indicating start-angle, end-angle, exit-status
// Author: Bernie Gaus and Samuel Gaus
// Args: loc="lat,lng", [zone="zonename"]

include ('config.php');
include ('xml2array.php');

if(empty($_GET['loc'])){
	die("No location supplied");
}

$location = explode(',',$_GET['loc']);

$lat = floatval($location[0]);
$lng = floatval($location[1]);

if( empty($_GET['zone']) ){
	$call = "getZoneFromPoint.php?point={$lat},{$lng}";
	$zone = callAPI($call);
}else{
	$zone = $_GET['zone'];
}

if($zone=="Unknown Zone"){
	die("<colourwheel></colourwheel>");
}

$zonefile = "{$settings['data_dir']}/{$zone}.zone";

try {
	$zonexml = simplexml_load_file($zonefile);
} catch (Exception $e) {
	die("Couldn't load zone file: $e");
}

$colourwheel = simplexml_load_string("<colourwheel></colourwheel>");

foreach ($zonexml->exits->exit as $exit){

	$exitfile = "{$settings['data_dir']}/{$exit['title']}.exit";
	$exitxml = simplexml_load_file($exitfile);
	
	$nodes = simpleXMLToArray($exitxml->nodes);
	
	$c_lat = ($nodes['node'][0]['lat'] + $nodes['node'][1]['lat'])/2;
	$c_lng = ($nodes['node'][0]['lng'] + $nodes['node'][1]['lng'])/2;
	
	$status = $exitxml['status'];
	
	switch($status){
		case "red":
			$status = "#ff0000";
			break;
		case "orange":
			$status = "#ffff00";
			break;
		case "green":
		default:
			$status = "#00ff00";
			break;
	}
	
	//todo Work out where this exit leads and pass one
	
	$b1 = round(bearing($lat, $lng, $c_lat, $c_lng)) + 90 - 5;
	$b2 = $b1 + 10;
	if ($b1 > $b2){
		$temp=$b1;
		$b1=$b2;
		$b2 = $temp;
	}
	
	if ($b1 >= 360) { $b1 -= 360; }
	if ($b2 >= 360) { $b2 -= 360; }

	$child = $colourwheel->addChild("exit");
	$child->addAttribute("b1",$b1);
	$child->addAttribute("b2",$b2);
	$child->addAttribute("status",$status);
	$child->addAttribute("leadsto","a");

}

function bearing( $lat1_, $lon1_, $lat2_, $lon2_ ){
	$lat1 =  deg2rad($lat1_);
	$long1 = deg2rad($lon1_);
	$lat2 =  deg2rad($lat2_);
	$long2 = deg2rad($lon2_);
	$bearingradians = atan2(asin($long2-$long1)*cos($lat2),cos($lat1)*sin($lat2) - sin($lat1)*cos($lat2)*cos($long2-$long1));
	$bearingdegrees = rad2deg($bearingradians);
	$bearingdegrees = $bearingdegrees < 0? 360 + $bearingdegrees : $bearingdegrees;
	return $bearingdegrees;
};

$dom = new DOMDocument('1.0');
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;
$dom->loadXML($colourwheel->asXML());

echo $dom->saveXML();

?>
