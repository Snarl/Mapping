var map, listeners = new Array(), zones = new Array();

listeners["zoneclick"] = new Array();

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
		listeners["zoneclick"][this.title] = google.maps.event.addListener(this.shape, "click", function(){
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
	
	this.finishEdit = function(prim) {
		for(var n = 0; n< this.markers.length; n++){
			this.markers[n].dragging = false;
			this.markers[n].setDraggable(false);
			this.markers[n].setVisible(false);
		}
		if(prim!==true){
			google.maps.event.clearListeners(map,"click");
			//google.maps.event.removeListener(listeners['mapclick']);
		}
	};

	this.startEdit = function() {
		var z = this;
		for(var n = 0; n< this.markers.length; n++){
			this.markers[n].setDraggable(true);
			this.markers[n].setVisible(true);
		}
		listeners["mapclick"] = google.maps.event.addListener(map, "click", function(event){
			z.addPoint(event.latLng);
		});
	};

	this.save = function(){
		
		$.get("api/saveZone.php", {
			"title": this.title,
			"nodes": this.points.join(";").replace(/ /g,"")
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
			var zone = new Zone();
			zone.setTitle(k);
			nodes = v.nodes.node;
			for(var i = 0;i < nodes.length;i++){
				zone.addPoint(new google.maps.LatLng(nodes[i].lat, nodes[i].lng));
			}
			zone.finishEdit(true);
			zones.push(zone);
			// Add to list
			$('#zonelist').append("<option>"+k+"</option>");
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
			createZone();
		});
		
		var $list = $('#zonelist');
		var $id = $('#zoneid');
		$list.change(function(){
			if($list.val()=="none"){
				$('#zoneoptions').slideUp();
				for(i=0;i<zones.length;i++){
					zones[i].blendTo(colours['norm']);
				}
			}else{
				for(i=0;i<zones.length;i++){
					if(zones[i].getTitle()==$('option:selected',$list).text()){
						$id.val(i);
						break;
					}
				}
				$('#zoneoptions').slideDown();
				for(i=0;i<zones.length;i++){
					zones[i].blendTo(colours['norm'],0);
				}
				zones[$id.val()].blendTo(colours['edit']);
			}
		});
		
		$('#zoneoptions button.zoom').click(function(){
			zones[$id.val()].zoomTo();
		});
		
		$('#zoneoptions button.pan').click(function(){
			zones[$id.val()].panTo();
		});
		
		$('#zoneoptions button.edit').toggle(
			function(){
				zones[$id.val()].startEdit();
				$list.attr('disabled','disabled');
				$(this).text('Finish')
			},function(){
				zones[$id.val()].finishEdit();
				zones[$id.val()].save();
				$list.removeAttr('disabled');
				$(this).text('Edit');
			}
		);
		
		$('#zoneoptions button.remove').click(function(){
			zones[$id.val()].remove();
			zones.splice($id.val(),1);
			$('#zoneoptions').slideUp();
		});
		
});

function createZone(){
	
	//Get title from user
	var title = prompt("Name your zone");
	if(title==null){
		return false;
	}
	
	var zone = new Zone();
	zone.setTitle(title);
	zones.push(zone);

	// Add to list and prepare edit 
	$('#zonelist').append("<option value='"+(zones.length-1)+"'>"+title+"</option>").val(zones.length-1).change();
	console.dir($('#zoneoptions button.edit'));
	$('#zoneoptions button.edit').click();
	
}
