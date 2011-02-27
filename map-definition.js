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

		// Icon definition
		var sq  = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAsAAAALCAIAAAAmzuBxAAAAB3RJTUUH1wUfFBAj8P8mAQAAAAZiS0dEAP8A/wD/oL2nkwAAAAlwSFlzAAALEwAACxMBAJqcGAAAABhJREFUGNNjYGD4Twgx/McDRlVgVYEfAQDP6Bn11lX+1QAAAABJRU5ErkJggg==";
		//var sqo = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAsAAAALCAIAAAAmzuBxAAAABGdBTUEAAFjH/EfgAgAAABtJREFUGNNjkJX9gx8xAPGr589woVEVWFXgRwDu3AxnX5K/ewAAAABJRU5ErkJggg==";

		// Make markers
		var marker = new google.maps.Marker({
			icon:sq, 
			draggable:true, 
			bouncy:false, 
			position:pos,
			dragCrossMove:true
		});
		this.markers.push(marker);

		// Add events
		google.maps.event.addListener(marker, "drag", function() {
			this.draw();
		});

		google.maps.event.addListener(marker, "click", function() {
			for(var n = 0; n < this.markers.length; n++) {
				if(this.markers[n] == marker) {
					this.markers[n].setMap(null);
					break;
				}
			}
			this.markers.splice(n, 1);
			this.draw();
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
			$this.addClass('inprogress');
			title = prompt("Name your zone");
			var zone = new Zone();
			zone.setTitle(title);
			listener =  google.maps.event.addListener(map, "click", function(event){
					zone.addPoint(event.latLng);
			});
		}else{
			 google.maps.event.removeListener(listener);
			$this.removeClass('inprogress');
		}
	});
});
