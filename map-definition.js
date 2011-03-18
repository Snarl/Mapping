var map, map_click, zones = new Array(), exits = new Array(); 

var colours = {
	"line" : "#3355ff",
	"edit" : "#335599",
	"norm" : "#ff0000",
	"exit" : "#00ff00",
};

function Zone(){

	this.shape, this.click;
	this.points = new Array();
	this.markers = new Array();
	this.title = "untitled node";
	
	this.fillColor = colours['norm'];
	
	this.getTitle = function(){
		return this.title;
	}
	
	this.setTitle = function(t){
		this.title = t;
		//todo Will need to rename the save file, else title change will only be for the session.
	}

	this.addPoint = function(pos) {

		var z = this;

		// Make markers
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
		
		if(this.shape===undefined){
			return true;
		}
		
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
				
			z.shape.setOptions({
				fillColor: padhex(from[0].toString(16)) + padhex(from[1].toString(16)) + padhex(from[2].toString(16))
			});
				
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
	
	this.getIndex = function() {
		return $.inArray(this,zones);
	}
	
	this.draw = function() {
		var z = this;
		if(this.shape) { var old = this.shape }
		this.points.length = 0;
		//todo Implement Sutherland-Hodgman (probably) clipping.
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
		this.click = google.maps.event.addListener(this.shape, "click", function(){
			var index = z.getIndex();
			$('#zonelist').val(index).change();
		});
	}
	
	this.clear = function() {
		for(var n = 0; n<this.markers.length; n++){
			this.markers[n].setMap(null)
		}
		this.points.length = 0;
		this.markers.length = 0;
	};
	
	this.finishEdit = function(prim) {
		for(var n = 0; n< this.markers.length; n++){
			this.markers[n].dragging = false;
			this.markers[n].setDraggable(false);
			this.markers[n].setVisible(false);
		}
		if(prim!==true){
			google.maps.event.clearListeners(map,"click");
			//google.maps.event.removeListener(map_click);
		}
	};

	this.startEdit = function() {
		var z = this;
		for(var n = 0; n< this.markers.length; n++){
			this.markers[n].setDraggable(true);
			this.markers[n].setVisible(true);
		}
		map_click = google.maps.event.addListener(map, "click", function(event){
			z.addPoint(event.latLng);
		});
	};

	this.save = function(){
		
		var pos = new Array();
		
		for(i=0;i<this.markers.length;i++){
			pos.push(this.markers[i].getPosition().toUrlValue(14));
		}

		$.get("api/saveZone.php", {
			"title": this.title,
			"nodes": encodeURI(pos.join(';'))
		}, function(result){
			if(result!=""){
				alert(result);
			}
		});
		
	};
	
	this.remove = function(){
		$.get("api/removeZone.php", {id: this.title});
		this.finishEdit();
		this.shape.setMap(null);
		delete this.shape;
		var title = this.title;
		$('#zonelist').children().each(function(){
			var $this = $(this);
			if($this.text()==title){
				$this.remove();
				return false;
			}
		});
	}
	
}

function loadZones(){
	$.getJSON("api/getZones.php",function(data){
		$.each(data, function(k, v){
			// Create Zone object
			zone = createZone(k);
			nodes = v.nodes.node;
			for(var i = 0;i < nodes.length;i++){
				zone.addPoint(new google.maps.LatLng(nodes[i].lat, nodes[i].lng));
			}
			zone.finishEdit(true);
		});
	});
}

function createZone(title){
	
	var click = false;
	if(title===undefined){
		click = true;
		var title = prompt("Name your zone");
		if(title==null){
			return false;
		}
	}
	
	// Create object
	var zone = new Zone();
	zone.setTitle(title);
	zones.push(zone);
	// Add to list
	$('#zonelist').append("<option value='"+(zones.length-1)+"'>"+title+"</option>").val(zones.length-1).change();
	
	if(click){
		$('#zoneoptions button.edit').click();
	}
	
	return zones[zones.length-1];
	
}

function Exit(){

	this.shape, this.click;
	this.points = new Array();
	this.markers = new Array();
	this.title = "untitled exit";
	
	this.getTitle = function(){
		return this.title;
	}
	
	this.setTitle = function(t){
		this.title = t;
		//todo Will need to rename the save file, else title change will only be for the session.
	}

	this.addPoint = function(pos) {

		var z = this;
		
		if(this.points.length>=2){
			return false;
		}

		// Make markers
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
	
	this.getIndex = function() {
		return $.inArray(this,exits);
	}
	
	this.draw = function() {
		var z = this;
		if(this.shape) { var old = this.shape }
		this.points.length = 0;	
		for(var i = 0; i < this.markers.length; i++) {
			this.points.push(this.markers[i].getPosition());
		}
		this.shape = new google.maps.Polyline({
			path: this.points, 
			strokeColor: colours['line'], 
			strokeWeight: 3, 
			strokeOpacity: 0.8
		});
		this.shape.setMap(map);
		if(old){ old.setMap(null); }
		this.click = google.maps.event.addListener(this.shape, "click", function(){
			var index = z.getIndex();
			$('#exitlist').val(index).change();
		});
	}
	
	this.clear = function() {
		for(var n = 0; n<this.markers.length; n++){
			this.markers[n].setMap(null)
		}
		this.points.length = 0;
		this.markers.length = 0;
	};
	
	this.finishEdit = function(prim) {
		for(var n = 0; n< this.markers.length; n++){
			this.markers[n].dragging = false;
			this.markers[n].setDraggable(false);
			this.markers[n].setVisible(false);
		}
		if(prim!==true){
			google.maps.event.clearListeners(map,"click");
			//google.maps.event.removeListener(map_click);
		}
	};

	this.startEdit = function() {
		var z = this;
		for(var n = 0; n< this.markers.length; n++){
			this.markers[n].setDraggable(true);
			this.markers[n].setVisible(true);
		}
		map_click = google.maps.event.addListener(map, "click", function(event){
			z.addPoint(event.latLng);
		});
	};

	this.save = function(){
		
		var pos = new Array();
		
		for(i=0;i<this.markers.length;i++){
			pos.push(this.markers[i].getPosition().toUrlValue(14));
		}

		$.get("api/saveExit.php", {
			"title": this.title,
			"nodes": encodeURI(pos.join(';'))
		}, function(result){
			if(result!=""){
				alert(result);
			}
		});
		
	};
	
	this.remove = function(){
		$.get("api/removeExit.php", {id: this.title});
		this.finishEdit();
		this.shape.setMap(null);
		delete this.shape;
		var title = this.title;
		$('#zonelist').children().each(function(){
			var $this = $(this);
			if($this.text()==title){
				$this.remove();
				return false;
			}
		});
	}
	
}

function loadExits(){
	$.getJSON("api/getExits.php",function(data){
		$.each(data, function(k, v){
			// Create exit object
			exit = createExit(k);
			nodes = v.nodes.node;
			for(var i = 0;i < nodes.length;i++){
				exit.addPoint(new google.maps.LatLng(nodes[i].lat, nodes[i].lng));
			}
			exit.finishEdit(true);
		});
	});
}

function createExit(title){
	
	var click = false;
	if(title===undefined){
		click = true;
		var title = prompt("Name your exit");
		if(title==null){
			return false;
		}
	}
	
	// Create object
	var exit = new Exit();
	exit.setTitle(title);
	exits.push(exit);
	// Add to list
	$('#exitlist').append("<option value='"+(exits.length-1)+"'>"+title+"</option>").val(exits.length-1).change();
	
	if(click){
		$('#exitoptions button.edit').click();
	}
	
	return exits[exits.length-1];

	
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
	
	// ZONES
	
		loadZones();
		
		//Add various functions to various buttons
		
		$('#zonedefine').click(function(){
			createZone();
		});
		
		var $zonelist = $('#zonelist');
		var $zoneid = $('#zoneid');
		$zonelist.change(function(){
			if($zonelist.val()=="none"){
				$('#zoneoptions').slideUp();
				for(i=0;i<zones.length;i++){
					zones[i].blendTo(colours['norm']);
				}
			}else{
				for(i=0;i<zones.length;i++){
					if(zones[i].getTitle()==$('option:selected',$zonelist).text()){
						$zoneid.val(i);
						break;
					}
				}
				$('#zoneoptions').slideDown();
				for(i=0;i<zones.length;i++){
					zones[i].blendTo(colours['norm']);
				}
				zones[$zoneid.val()].blendTo(colours['edit']);
			}
		});
		
		$('#zoneoptions button.zoom').click(function(){
			zones[$zoneid.val()].zoomTo();
		});
		
		$('#zoneoptions button.pan').click(function(){
			zones[$zoneid.val()].panTo();
		});
		
		$('#zoneoptions button.edit').toggle(
			function(){
				zones[$zoneid.val()].startEdit();
				$zonelist.attr('disabled','disabled');
				$(this).text('Finish')
			},function(){
				zones[$zoneid.val()].finishEdit();
				zones[$zoneid.val()].save();
				$zonelist.removeAttr('disabled');
				$(this).text('Edit');
			}
		);
		
		$('#zoneoptions button.remove').click(function(){
			zones[$zoneid.val()].remove();
			zones.splice($zoneid.val(),1);
			$('#zoneoptions').slideUp();
		});
		
	// EXITS
	
		loadExits();
		
		//Add various functions to various buttons
		
		$('#exitdefine').click(function(){
			createExit();
		});
		
		var $exitlist = $('#exitlist');
		var $exitid = $('#exitid');
		$exitlist.change(function(){
			if($exitlist.val()=="none"){
				$('#exitoptions').slideUp();
				for(i=0;i<exits.length;i++){
					exits[i].blendTo(colours['norm']);
				}
			}else{
				for(i=0;i<exits.length;i++){
					if(exits[i].getTitle()==$('option:selected',$exitlist).text()){
						$exitid.val(i);
						break;
					}
				}
				$('#exitoptions').slideDown();
			}
		});
		
		$('#exitoptions button.zoom').click(function(){
			exits[$exitid.val()].zoomTo();
		});
		
		$('#exitoptions button.pan').click(function(){
			exits[$exitid.val()].panTo();
		});
		
		$('#exitoptions button.edit').toggle(
			function(){
				exits[$exitid.val()].startEdit();
				$exitlist.attr('disabled','disabled');
				$(this).text('Finish')
			},function(){
				exits[$exitid.val()].finishEdit();
				exits[$exitid.val()].save();
				$exitlist.removeAttr('disabled');
				$(this).text('Edit');
			}
		);
		
		$('#exitoptions button.remove').click(function(){
			exits[$exitid.val()].remove();
			exits.splice($exitid.val(),1);
			$('#exitoptions').slideUp();
		});

		
});
