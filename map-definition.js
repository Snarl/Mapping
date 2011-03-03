var map, listener, zones = new Array();

function Zone(){

	this.polyShape;
	this.polyLineColor = "#3355ff";
	this.polyFillColor = "#335599";
	this.polyPoints = new Array();
	this.markers = new Array();
	this.title = "untitled";
	
	this.getTitle = function(){
		return this.title;
	}
	
	this.setTitle = function(t){
		this.title = t;
	}

	this.addPoint = function(pos) {

		var z = this;

		// Make markersp
		var marker = new google.maps.Marker({
			icon:'./square.png', 
			draggable:true, 
			bouncy:false, 
			position:pos,
		});
		marker.setMap(map);
		this.markers.push(marker);

		// Add events
		google.maps.event.addListener(marker, "drag", function() {
			z.draw();
		});

		google.maps.event.addListener(marker, "click", function() {
			for(var n = 0; n < z.markers.length; n++) {
				if(z.markers[n] == marker) {
					z.markers[n].setMap(null);
					break;
				}
			}
			z.markers.splice(n, 1);
			z.draw();
		});

		this.draw();
	}
	
	this.getBounds = function(){
		var bounds=new google.maps.LatLngBounds;
		for(i = 0; i < this.markers.length; i++){
			bounds.extend(this.markers[i].position);
		}
		return bounds;
	}

	this.panTo = function() {
		map.panTo(this.getBounds().getCenter());
	};
	
	this.zoomTo = function(){
		map.fitBounds(this.getBounds());
	}
	
	this.draw = function() {
		if(this.polyShape){ this.polyShape.setMap(null); }
		this.polyPoints.length = 0;	
		for(i = 0; i < this.markers.length; i++) {
			this.polyPoints.push(this.markers[i].getPosition());
		}
		this.polyShape = new google.maps.Polygon({
			paths: this.polyPoints, 
			strokeColor: this.polyLineColor, 
			strokeWeight: 3, 
			strokeOpacity: .8, 
			fillColor: this.polyFillColor,
			fillOpacity: .3
		});
		this.polyShape.setMap(map);
	}
	
	this.clear = function() {
		for(var n = 0; n<this.markers.length; n++){
			this.markers[n].setMap(null)
		}
		this.polyPoints.length = 0;
		this.markers.length = 0;
	}
	
	this.finishEdit = function() {
		for(var n = 0; n< this.markers.length; n++){
			this.markers[n].dragging = false;
			this.markers[n].draggable = false;
			this.markers[n].visible = false;
		}
	}
	
	this.startEdit = function() {
		for(var n = 0; n< this.markers.length; n++){
			this.markers[n].draggable = true;
			this.markers[n].visible = true;
		}		
	}

	this.save = function(){
		
		$.get("api/saveZone.php", {
			"title": this.title,
			"coords": this.polyPoints.join(";")
		}, function(result){
			alert(result);
		})
	}
	
}

function populateZones(){

}

$(document).ready(function(){
	
	//Create the Google Map
	var $map = $('#map');
	$map.height($(window).height() - $map.offset().top);
	map = new google.maps.Map($map[0], {
		zoom: 16,
		center: new google.maps.LatLng(51.500556, -0.126667),
		mapTypeId: google.maps.MapTypeId.ROADMAP
	});
	
	//Prepare the zone creating button
	$('#zonedefine').click(function(){
		startZone();
	});
	
});

function startZone(){
	
	$btn = $('#zonedefine');
	
	//Get title from user
	var title = prompt("Name your zone");
	if(title==null){
		return false;
	}
	var zone = new Zone();
	zone.setTitle(title);
	zones.push(zone);
	
	//Change the button state
	$btn.addClass('inprogress').attr("oldtext",$btn.text()).text("Finish defining '"+title+"'").attr("active",title).unbind("click").click(function(){
			finishZone(zone);
	});
	
	//Prepare the map for defining a zone
	listener = google.maps.event.addListener(map, "click", function(event){
			zone.addPoint(event.latLng);
	});
	
}

function finishZone(zone){
	
	$btn = $('#zonedefine');
	
	//Make the zone static on the map
	zone.finishEdit();
	zone.save();
	google.maps.event.removeListener(listener);
	
	//Change the button state
	$btn.removeClass('inprogress').text($btn.attr("oldtext")).removeAttr("oldtext").removeAttr("active").unbind("click").click(function(){
			startZone();
	});
	
}
