var center = [38.230462, 21.753150];

var mymap = L.map('mapid').setView(center, 13);
// Map layers
L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
	attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
	maxZoom: 16,
	id: 'mapbox/streets-v11',
	accessToken: 'pk.eyJ1IjoibWFub3MzNjQiLCJhIjoiY2s0bXdscndhMTR6aTNtbGRvNmxicjNrdyJ9.ua7kEOYdenmyKGKg0na20Q'
}).addTo(mymap);
// Patras region
var circle = L.circle(center, {color: 'red', fillOpacity: 0, radius: 10000}).addTo(mymap);

// Initialise the FeatureGroup to store editable layers
var editableLayers = new L.FeatureGroup();
mymap.addLayer(editableLayers);

var drawPluginOptions = {
	position: 'topright',
	draw: {
		polygon: false,
		polyline: false,
		circle: false,
		rectangle: true,
		marker: false
	},
  	edit: { 
		  featureGroup: editableLayers, 
		  remove: true 
	}
};

// Initialise the draw control and pass it the FeatureGroup of editable layers
var drawControl = new L.Control.Draw(drawPluginOptions);
mymap.addControl(drawControl);

mymap.on('draw:created', function(e) {
	var type = e.layerType, layer = e.layer;
	editableLayers.addLayer(layer);
	// Iterate all drawn things and JSON them into the areas input
	var rectangles = editableLayers.toGeoJSON().features;
	var rectanglesLength = rectangles.length;
	var coords = [];
	for (var i = 0; i < rectanglesLength; i++) {
		var rectangle = new Object();
		rectangle.p1 = rectangles[i].geometry.coordinates[0][0];
		rectangle.p2 = rectangles[i].geometry.coordinates[0][1];
		rectangle.p3 = rectangles[i].geometry.coordinates[0][2];
		rectangle.p4 = rectangles[i].geometry.coordinates[0][3];
		coords.push(rectangle);		
	}
	document.getElementById('areas').value = JSON.stringify(coords);
});

mymap.on('draw:edited draw:deleted', function(e) {
	// Iterate all drawn things and JSON them into the areas input
	var rectangles = editableLayers.toGeoJSON().features;
	var rectanglesLength = rectangles.length;
	var coords = [];
	for (var i = 0; i < rectanglesLength; i++) {
		var rectangle = new Object();
		rectangle.p1 = rectangles[i].geometry.coordinates[0][0];
		rectangle.p2 = rectangles[i].geometry.coordinates[0][1];
		rectangle.p3 = rectangles[i].geometry.coordinates[0][2];
		rectangle.p4 = rectangles[i].geometry.coordinates[0][3];
		coords.push(rectangle);
	}
	document.getElementById('areas').value = JSON.stringify(coords);
});