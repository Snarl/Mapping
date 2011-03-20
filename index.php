<!DOCTYPE html>
<html>

	<head>

		<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />

		<base href="/maps/" />

		<link rel="stylesheet" href="style.css" type="text/css" />

		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
		<script type="text/javascript" src="jquery-1.5.1.js"></script>
		<script type="text/javascript" src="map-definition.js"></script>

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
							<option value="none" selected>select a zone</option>
						</select>
						<div id="zoneoptions" class="options">
							<input type="hidden" id="zoneid" />
							<button class="zoom">Zoom to</button>
							<button class="pan">Pan to</button>
							<button class="edit">Edit</button>
							<button class="remove">Delete</button>
							<br />
							<textarea class="reason">Give a reason and click a colour</textarea>
							<br />
							<button class="red"></button> 
							<button class="orange"></button> 
							<button class="green"></button>
						</div>
					<h4>Exits</h4>
						<button id="exitdefine">Create exit</button> or 
						<select id="exitlist">
							<option value="none" selected>select a exit</option>
						</select>
						<div id="exitoptions" class="options">
							<input type="hidden" id="exitid" />
							<button class="zoom">Zoom to</button>
							<button class="pan">Pan to</button>
							<button class="edit">Edit</button>
							<button class="remove">Delete</button>
							<button class="link">Link</button>
							<br />
							<textarea class="reason">Give a reason and click a colour</textarea>
							<br />
							<button class="red"></button> 
							<button class="orange"></button> 
							<button class="green"></button>
						</div>
				</div>
			</div>
		</div>


		
	</body>

</html>
