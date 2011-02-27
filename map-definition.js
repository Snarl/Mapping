var map, listener;

function Zone(){

	this.polyShape;
	this.polyLineColor = "#3355ff";
	this.polyFillColor = "#335599";
	this.polyPoints = new Array();
	this.markers = new Array();
	this.title = "untitled";
	
	this.setTitle = function(t){
		this.title = t;
	}

	this.addPoint = function(pos) {

		var z = this;

		// Make markers
		var marker = new google.maps.Marker({
			icon:'./square.png', 
			draggable:true, 
			bouncy:false, 
			position:pos,
			dragCrossMove:true
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

/*	this.zoomTo = function() {
		if(this.polyShape && this.polyPoints.length > 0) {
			var bounds = polyShape.getBounds();
			map.setCenter(bounds.getCenter());
			map.setZoom(map.getBoundsZoomLevel(bounds));
		}
	}*/
	
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
	
}

$(document).ready(function(){
	var $map = $('#map');
	$map.height($(window).height() - $map.offset().top);
	map = new google.maps.Map($map[0], {
		zoom: 16,
		center: new google.maps.LatLng(51.500556, -0.126667),
		mapTypeId: google.maps.MapTypeId.ROADMAP
	});
	$('#zonedefine').click(function(){
		$this = $(this);
		if(!$this.hasClass('inprogress')){
			var title = prompt("Name your zone");
			if(title==null){
				return;
			}
			$this.addClass('inprogress');
			var zone = new Zone();
			zone.setTitle(title);
			listener = google.maps.event.addListener(map, "click", function(event){
					zone.addPoint(event.latLng);
			});
		}else{
			google.maps.event.removeListener(listener);
			$this.removeClass('inprogress');
		}
	});
});
