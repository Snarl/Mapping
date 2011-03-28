<?php	

// Title: updateZoneAndExitKml.php
// Description: Creates KML of zones and exits
// Author: Samuel Carlisle
// Args:

$zonedir = "../data/";				//sets the directory in which XML files are saved (relative to API Directory).
$zoneext = ".zone";				//extension for zone files.
$zonelist = $zonedir."zonelist.xml";		//XML file containing directory of zones.

$exitdir = "../data/";				//sets the directory in which XML files are saved (relative to API Directory).
$exitext = ".exit";				//extension for exit files.
$exitlist = $exitdir."zonelist.xml";		//XML file containing directory of exit.

$mapext = ".map";				//extension for map files.

$exitdir = "../data/";
$exitext = ".exit";
$exitlist = $exitdir."exitlist.xml";

$kmldir = "../../"; 			//sets the directory in which KML files are saved (relative to API Directory).
$kmlext = ".kml";				//extension for kml files.

$msg  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
$msg .= "<kml xmlns=\"http://www.opengis.net/kml/2.2\">\n";
$msg .= "\t<Document>\n";
$msg .= "\t<name>Sukey Live Map View</name>\n";
$msg .= "\t\t<description>Avoid the junction if it is red!</description>\n";
/* 
$msg .= "\t\t<Style id=\"sukey\">\n"; //sets the style rule for placemarks with <styleUrl>#sukey</styleUrl> i.e. equivalent of css being applied to id="#sukey"
$msg .= "\t\t\t<IconStyle>\n";
$msg .= "\t\t\t\t<Icon>\n";
$msg .= "\t\t\t\t\t<href>http://sukey.org/images/Sukey_River.png</href>\n";
$msg .= "\t\t\t\t</Icon>\n";
$msg .= "\t\t\t</IconStyle>\n";
$msg .= "\t\t</Style>\n";
*/
/*
$msg .= "\t\t<Style id=\"greenLine\">\n";
$msg .= "\t\t<LineStyle>\n";
$msg .= "\t\t\t<color>00FF00FF</color>\n";
$msg .= "\t\t\t\t<colorMode>normal</colorMode>\n";
$msg .= "\t\t\t\t<width>6</width>\n";
$msg .= "\t\t\t\t<gx:outerColor>00FF00FF</gx:outerColor>\n";
$msg .= "\t\t\t\t<gx:outerWidth>1</gx:outerWidth>\n";
$msg .= "\t\t\t\t<gx:physicalWidth>1</gx:physicalWidth>\n";
$msg .= "</LineStyle>\n";
$msg .= "\t\t</Style>\n";

$msg .= "\t\t<Style id=\"orangeLine\">\n";
$msg .= "\t\t<LineStyle>\n";
$msg .= "\t\t\t<color>FFFF00FF</color>\n";
$msg .= "\t\t\t\t<colorMode>normal</colorMode>\n";
$msg .= "\t\t\t\t<width>6</width>\n";
$msg .= "\t\t\t\t<gx:outerColor>FFFF00FF</gx:outerColor>\n";
$msg .= "\t\t\t\t<gx:outerWidth>1</gx:outerWidth>\n";
$msg .= "\t\t\t\t<gx:physicalWidth>1</gx:physicalWidth>\n";
$msg .= "</LineStyle>\n";
$msg .= "\t\t</Style>\n";

$msg .= "\t\t<Style id=\"redLine\">\n";
$msg .= "\t\t<LineStyle>\n";
$msg .= "\t\t\t<color>FF0000FF</color>\n";
$msg .= "\t\t\t\t<colorMode>normal</colorMode>\n";
$msg .= "\t\t\t\t<width>6</width>\n";
$msg .= "\t\t\t\t<gx:outerColor>FF0000FF</gx:outerColor>\n";
$msg .= "\t\t\t\t<gx:outerWidth>1</gx:outerWidth>\n";
$msg .= "\t\t\t\t<gx:physicalWidth>1</gx:physicalWidth>\n";
$msg .= "</LineStyle>\n";
$msg .= "\t\t</Style>\n";
*/

/*
$msg .= "<Style id=\"orangeLine\>\n";
$msg .= "<LineStyle>\n";
$msg .= "<color>ffffff00</color>\n"; //opacity xx RR GG BB
$msg .= "<width>6</width>\n";
$msg .= "</LineStyle>\n";
$msg .= "</Style>\n";

$msg .= "<Style id=\"redLine\>\n";
$msg .= "<LineStyle>\n";
$msg .= "<color>ffff0000</color>\n"; //opacity xx RR GG BB
$msg .= "<width>6</width>\n";
$msg .= "</LineStyle>\n";
$msg .= "</Style>\n";
*/
/*
function isZoneFile($f){
	return(substr($f,-5)==".zone");
}

$zones = array_filter(scandir($zonedir),"isZoneFile");

foreach ($zones as $zonefile) {
	$zonestring = file_get_contents($zonedir.$zonefile);
	preg_match_all("|lat=\"(-?[0-9]+.[0-9]+)\" lng=\"(-?[0-9]+.[0-9]+)\"|",$zonestring,$matches);
	$name  = substr($zonefile,0,-5);
	$msg  .= "\t\t<Placemark>\n";
	$msg  .= "\t\t\t<name>{$name}</name>\n";
	$msg  .= "\t\t\t<visibility>0</visibility>\n";
	$msg  .= "\t\t\t<styleUrl>#transRedPoly</styleUrl>\n";
	$msg  .= "\t\t\t<Polygon>\n";
	$msg  .= "\t\t\t\t<extrude>1</extrude>\n";
	$msg  .= "\t\t\t\t<altitudeMode>relativeToGround</altitudeMode>\n";
	$msg  .= "\t\t\t\t<outerBoundaryIs>\n";
	$msg  .= "\t\t\t\t\t<LinearRing>\n";
	$msg  .= "\t\t\t\t\t\t<coordinates>\n";
	foreach($matches[1] as $key => $lat){
		$msg .= "\t\t\t\t\t\t\t{$matches[2][$key]},{$lat}\n";
	}
	$msg  .= "\t\t\t\t\t\t</coordinates>\n";
	$msg  .= "\t\t\t\t\t</LinearRing>\n";
	$msg  .= "\t\t\t\t</outerBoundaryIs>\n";
	$msg  .= "\t\t\t</Polygon>\n";
	$msg  .= "\t\t</Placemark>\n";
}
*/
function isExitFile($f){
	return(substr($f,-5)==".exit");
}

$exits = array_filter(scandir($exitdir),"isExitFile");

foreach ($exits as $exitfile) {
	$exitstring = file_get_contents($exitdir.$exitfile);
	preg_match_all("|lat=\"(.*?)\" lng=\"(.*?)\"|",$exitstring,$match);
	preg_match("|status=\"(.*?)\"|",$exitstring,$status);

	$colour = $status[1];
	
	switch($colour){
		case "red":
			$colour = "0000ff";
			break;
		case "orange":
			$colour = "ff8c00";
			break;
		case "green":
		default:
			$colour = "00ff00";
			break;
	}

	$name  = substr($exitfile,0,-5);
	$msg  .= "\t\t<Placemark>\n";
	$msg  .= "\t\t\t<name>{$name}</name>\n";
	$msg .= "<Style>\n";
	$msg .= "<LineStyle>\n";
	$msg .= "<color>ff{$colour}</color>\n"; //opacity xx RR GG BB
	$msg .= "<width>6</width>\n";
	$msg .= "</LineStyle>\n";
	$msg .= "</Style>\n";
//	$msg  .= "\t\t\t<styleUrl>#{$colour}Line</styleUrl>\n";
	$msg  .= "\t\t\t<LineString>\n";
	$msg  .= "\t\t\t\t<coordinates>\n";
	$msg  .= "\t\t\t\t\t{$match[2][0]},{$match[1][0]}\n";
	$msg  .= "\t\t\t\t\t{$match[2][1]},{$match[1][1]}\n";
	$msg  .= "\t\t\t\t</coordinates>\n";
	$msg  .= "\t\t\t</LineString>\n";
	$msg  .= "\t\t</Placemark>\n";
}

/*
<LineString>
      <coordinates>-122.1,37.4,0 -122.0,37.4,0 -122.0,37.5,0 -122.1,37.5,0 -122.1,37.4,0</coordinates>
    </LineString>
*/

$msg .= "\n</Document>";
$msg .= "</kml>\n";
$filename = "{$kmldir}mapview{$kmlext}";
file_put_contents($filename,$msg);
echo "done!";
?>
