<!DOCTYPE html>
<html>

	<head>

		<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />

		<link rel="stylesheet" href="/style.css" type="text/css" />

		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.5.1.min.js"></script>
		<script type="text/javascript" src="/map-definition.js"></script>
		<base href="http://localhost/maps/"
	</head>

	<body>

		<div id="container">
			<div id="header">
				<h1>Snarl</h1>
			</div>
			<div id="content">
				<div id="map-container">
					<div id="map"></div>
				</div>
				<div id="tools">
					<h3>Tools</h3>
					<h4>Zones</h4>
					<button id="zonedefine">Create zone</button> or 
					<select id="zonelist">
						<option>select a zone</option>
					</select>
					<h4>Exits</h4>
				</div>
			</div>
		</div>


		
	</body>

</html>
