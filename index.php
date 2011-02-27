<!DOCTYPE html>
<html>

	<head>

		<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />

		<style type="text/css">
			html { height: 100% }
			body { height: 100%; margin: 0px; padding: 0px }
			#map_canvas { height: 100% }

			#container {
				margin: 0 auto;
				width: 100%;
				background: #fff;
			}

			#header {
				background: #ccc;
				padding: 20px;
			}

			#header h1 { margin: 0; }

			#content {
				float: left;
				width: 100%;
			}

			#map-container {
				clear: left;
				float: left;
				width: 70%;
				margin: 0 0 0 0;
				display: inline;
			}

			#map {
				width: 100%;
				min-height: 100px;
			}

			#tools {
				float: right;
				width: 26%;
				margin: 0 0 0 0;
				display: inline;
			}

		</style>

		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.5.1.min.js"></script>
		<script type="text/javascript" src="./map-definition.js"></script>

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
					<button id="zonedefine">Create zone</button>
				</div>
			</div>
		</div>


		
	</body>

</html>
