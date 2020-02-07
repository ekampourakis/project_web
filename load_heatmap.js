var center = [38.230462, 21.753150];

var mymap = L.map('mapid').setView(center, 13);
// Map layers
L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
	attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
	maxZoom: 16,
	id: 'mapbox/streets-v11',
	accessToken: 'pk.eyJ1IjoibWFub3MzNjQiLCJhIjoiY2s0bXdscndhMTR6aTNtbGRvNmxicjNrdyJ9.ua7kEOYdenmyKGKg0na20Q'
}).addTo(mymap);

var cfg = {
	// radius should be small ONLY if scaleRadius is true (or small radius is intended)
	// if scaleRadius is false it will be the constant radius used in pixels
	"radius": 0.0015,
	"maxOpacity": .7,
	// scales the radius based on map zoom
	"scaleRadius": true,
	// if set to false the heatmap uses the global maximum for colorization
	// if activated: uses the data maximum within the current map boundaries
	//   (there will always be a red spot with useLocalExtremas true)
	"useLocalExtrema": false,
	// which field name in your data represents the latitude - default "lat"
	latField: 'lat',
	// which field name in your data represents the longitude - default "lng"
	lngField: 'lng',
	// which field name in your data represents the data value - default "value"
	valueField: 'count'
};

var heatmapLayer = new HeatmapOverlay(cfg).addTo(mymap);
