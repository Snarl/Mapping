var map, listeners, zones = new Array();

var colours = {
	"line" : "#3355ff",
	"edit" : "#335599",
	"norm" : "#ff0000"
};

function Zone(){

	this.shape;
	this.points = new Array();
	this.markers = new Array();
	this.title = "untitled";
	
	this.fillColor = colours['norm'];
	
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
	
	this.blendTo = function(to_string, time){
		
		if(time===undefined){
			time = "1000";
		}
		
		var z = this, from_string = this.fillColor, steps = time/50, to = new Array(), from = new Array(), step = new Array();
		
		if(to_string === from_string){
			return true;
		}
		
		$.each([0,1,2], function(k){
			//create array of "to" r,g and b in dec
			to[k]   = parseInt(to_string.substring(1+(2*k),3+(2*k)),16);
			//create array of "from" r,g and b in dec
			from[k] = parseInt(from_string.substring(1+(2*k),3+(2*k)),16);
			//determine which step to use (round to zero)
			v = (to[k] - from[k])/steps;
			step[k] = Math[v > 0 ? "ceil" : "floor"](v);
		});
	
		blendStep = function(count){

			$.each([0,1,2], function(k){
				from[k] = Math[step[k] > 0 ? "min" : "max"](to[k],from[k]+step[k]);
			});

			function padhex(val){
				return (val.length==1)?"0"+val:val;
			}

			z.fillColor = '#'+
				padhex(from[0].toString(16)) +
				padhex(from[1].toString(16)) +
				padhex(from[2].toString(16));
				
			z.draw();
				
			if(count<steps){
				var t = setTimeout(function(){
					blendStep(count+1);
				},50);
			}
			
		};
		
		blendStep(0);
			
	}
	
	this.getBounds = function(){
		var bounds=new google.maps.LatLngBounds;
		for(var i = 0; i < this.markers.length; i++){
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
		var z = this;
		if(this.shape) { var old = this.shape }
		this.points.length = 0;	
		for(var i = 0; i < this.markers.length; i++) {
			this.points.push(this.markers[i].getPosition());
		}
		this.shape = new google.maps.Polygon({
			paths: this.points, 
			strokeColor: colours['line'], 
			strokeWeight: 3, 
			strokeOpacity: .8, 
			fillColor: this.fillColor,
			fillOpacity: .3
		});
		this.shape.setMap(map);
		if(old){ old.setMap(null); }
		google.maps.event.addListener(this.shape, "click", function(){
			//todo Select this zone on the right menu
		});
	}
	
	this.clear = function() {
		for(var n = 0; n<this.markers.length; n++){
			this.markers[n].setMap(null)
		}
		this.points.length = 0;
		this.markers.length = 0;
	};
	
	this.finishEdit = function() {
		for(var n = 0; n< this.markers.length; n++){
			this.markers[n].dragging = false;
			this.markers[n].draggable = false;
			this.markers[n].visible = false;
		}
		//this.blendTo(colours['norm'],1000);
	};

	this.startEdit = function() {
		for(var n = 0; n< this.markers.length; n++){
			this.markers[n].draggable = true;
			this.markers[n].visible = true;
		}
		//this.blendTo(colours['edit'],1000);
	};

	this.save = function(){
		
		$.get("api/saveZone.php", {
			"title": this.title,
			"nodes": this.points.join(";").replace(/ /g,"")
		}, function(result){
			//todo Acknowledge save
		});
		
	};
	
}

function loadZones(){
	$.getJSON("api/getZones.php",function(data){
		$.each(data, function(k, v){
			// Create Zone object
			var zone = new Zone();
			zone.setTitle(k);
			nodes = v.nodes.node;
			for(var i = 0;i < nodes.length;i++){
				zone.addPoint(new google.maps.LatLng(nodes[i].lat, nodes[i].lng));
			}
			zone.finishEdit();
			zones.push(zone);
			// Add to list
			$('#zonelist').append("<option value='"+(zones.length-1)+"'>"+k+"</option>");
		});
	});
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
	
	loadZones();
	
	//Add various functions to various buttons
	
		$('#zonedefine').click(function(){
			startZone();
		});
		
		var $list = $('#zonelist');
		$list.change(function(){
			if($list.val()=="none"){
				$('#zoneoptions').slideUp();
				for(i=0;i<zones.length;i++){
					zones[i].blendTo(colours['norm']);
				}
			}else{
				$('#zoneoptions').slideDown();
				zones[$list.val()].blendTo(colours['edit']);
			}
		});
		
		$('#zoneoptions button.zoom').click(function(){
			zones[$list.val()].zoomTo();
		});
		
		$('#zoneoptions button.pan').click(function(){
			zones[$list.val()].panTo();
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
	
	//Change the button state, make clicking it again finish the zone.
	$btn.addClass('inprogress').attr("oldtext",$btn.text()).text("Finish defining '"+title+"'").attr("active",title).unbind("click").click(function(){
			finishZone(zone);
	});
	
	//Prepare the map for defining a zone
	listeners['click_map'] = google.maps.event.addListener(map, "click", function(event){
			zone.addPoint(event.latLng);
	});
	
}

function finishZone(zone){
	
	$btn = $('#zonedefine');
	
	//Make the zone static on the map
	zone.finishEdit();
	zone.save();
	google.maps.event.removeListener(listeners['click_map']);
	
	//Change the button state
	$btn.removeClass('inprogress').text($btn.attr("oldtext")).removeAttr("oldtext").removeAttr("active").unbind("click").click(function(){
			startZone();
	});
	
}
