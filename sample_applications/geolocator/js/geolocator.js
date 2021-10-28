var map;
var mapCluster;
var mapClusterOptions = {gridSize: 50, maxZoom: 15};

var mapType = google.maps.MapTypeId.ROADMAP;
var mapLevel = 6;
var mapLevelDetail = 8;

// Rome
var mapCity = {'latitude':41.913815462572174, 'longitude':12.582089699999983};

var infowindow = new google.maps.InfoWindow();

var markersArray = [];

var loadingCounter = 0;

function resetMarkers() {
	setCheckbox('checked', false);
	setCheckbox('disabled', true);
	$('#protocolAny').attr('checked', true);
	$("#list").html('');
	$("span").html('');
	markersArray.length = 0;
	mapCluster.clearMarkers();
	map.setZoom(mapLevel);
}

function clearMarkers() {
	setCheckbox('disabled', false);
    for (i in markersArray) {
    	markersArray[i].marker.setMap(null);
    }
	mapCluster.clearMarkers();
}

function setCheckbox(attr, bool) {
	var boxes = $('input[type=checkbox]');
	boxes.each(function() {
		$('input[type=checkbox]').attr(attr, bool)
	});
}

function setMarkers(obj) {
	var protocol = obj.id;
	var counter = 0;

	var span_count = 'span.#count_' + protocol;
	var span_list = 'li.' + protocol; 

	if (protocol == 'protocolAny') {
		setCheckbox('disabled', true);
		setCheckbox('checked', false);
	    for (i in markersArray) {
	    	markersArray[i].marker.setMap(map);
	    	mapCluster.addMarker(markersArray[i].marker);
	    }
    }
	else {
		for (i in markersArray) {
			if (markersArray[i].protocol == protocol) {
				// display requested markers
				if (obj.checked) {
					counter++;
					markersArray[i].marker.setMap(map);
					mapCluster.addMarker(markersArray[i].marker);
					$(span_list).addClass('highlight');
				}
				else {
					markersArray[i].marker.setMap(null);
					mapCluster.removeMarker(markersArray[i].marker);
					$(span_list).removeClass('highlight');
					$(span_count).text('');
				}
			}
			else {
				$(span_count).text('').removeClass('protocolCounter');
			}
		}
		if (obj.checked) $(span_count).text(counter).addClass('protocolCounter');
	}
}

function getInfoWindowEvent(marker, content) {
    infowindow.close()
    infowindow.setContent(content);
    infowindow.open(map, marker);
}

function getGeoIp(host, data) {
	var provider = 'http://freegeoip.net/json/';
	var callback = '?callback=?';
	var url = provider + host + callback;
	var json;

	$.getJSON(url, function(json) {
		loadingCounter--;
		addMarker(json, data);
	});
}

function addMarker(geoip, data) {
	if (loadingCounter == 0) { hideLoading() }
		if (geoip.latitude != 0) {
			// Marker icon
			var icon = 'desktop.png';
			if (data.extension == "ActiveSync") {
				icon = 'mobileDevice.png';
			}
			var markerIcon = new google.maps.MarkerImage('images/'+icon,
				// This marker is 32 pixels wide by 37 pixels tall.
				new google.maps.Size(32, 37),
				// The origin for this image is 0,0.
				new google.maps.Point(0,0),
				// The anchor for this image is the base of the flagpole at 0,32.
				new google.maps.Point(0, 37));
			
	        // Marker
			var markerPos = new google.maps.LatLng(geoip.latitude, geoip.longitude);

			var markerMarker = {
					'marker' : new google.maps.Marker({
						icon: markerIcon,
						position: markerPos,
						title: geoip.ip + ' (' + data.user + ')',
						map: map
					}),
					'protocol': data.proto+data.extension,
					'position': markerPos
			};

			// Marker info bubble
		 	var infoText =
				'<h1>' + data.user + '</h1>'+
				'<div id="bodyContent">'+
				'IP: ' + geoip.ip + '<br>'+
				'Protocol: ' + data.proto + '<br>'+
				'Extension: ' + data.extension + '<br>'+
				data.description + '<br>'+
				'</div>';

			// Assign info bubble to marker
			google.maps.event.addListener(markerMarker.marker, 'click', function() {
				getInfoWindowEvent(markerMarker.marker, infoText);
			});

			markersArray.push(markerMarker);
			mapCluster.addMarker(markerMarker.marker);

			// add marker to menu
			$('<li class="'+ data.proto + data.extension +'"/>') 
				.html(geoip.ip + ' (' + data.user + ')') 
				.click(function(){
					getInfoWindowEvent(markerMarker.marker, infoText);
					map.panTo(markerPos) 
			}) 
			.appendTo("#list");

			$('span.#countTotal').text(markersArray.length).addClass('protocolCounter');
			$('#protocolCustom').attr('disabled', false);

		} // enf if latitude
} // end function

function getConnections() {
	resetMarkers();

 	var server = document.getElementsByName('kerio-connect')[0].value;
 	var url = 'getConnections.php?server=' + server;
	
	showLoading();
 	$.getJSON(url, function(json) {
 		loadingCounter = json.length;
	 	$.each(json, function(i, item) {
	 		var host = item.from.split(':')[0];
	 		getGeoIp(host, item);
 		})
	    if (loadingCounter == 0) { hideLoading()}
    });
}

function initialize() {
	var myOptions = {
		zoom: mapLevel,
		mapTypeId: mapType
	};
	map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	mapCluster =  new MarkerClusterer(map);
    doGeolocation();
}

function doGeolocation() {
	if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(function(position) {
			var pos = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
			map.setCenter(pos);
		},
		function() {
			doManualGeolocation();
		});
	}
	else {
		 doManualGeolocation();
	}
}

function doManualGeolocation() {
	var pos = new google.maps.LatLng(mapCity.latitude, mapCity.longitude);
	map.setZoom(mapLevel);
	map.setCenter(pos);
}

function showLoading() {
	$("#loading").html('<img src="images/ajax-loader.gif" />').show();
}

function hideLoading() {
	$("#loading").hide();
}

// Load map
google.maps.event.addDomListener(window, 'load', initialize);
