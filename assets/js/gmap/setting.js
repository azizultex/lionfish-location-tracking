var markerClusterer = null;
var map = null;
var imageUrl = 'http://chart.apis.google.com/chart?cht=mm&chs=24x32&' +
  'chco=FFFFFF,008CFF,000000&ext=.png';

function refreshMap() {
if (markerClusterer) {
  markerClusterer.clearMarkers();
}

var markers = [];

var markerImage = new google.maps.MarkerImage(imageUrl,
  new google.maps.Size(24, 32));

  var data_length = lionfish_locations.length;
  console.log(data_length);

for (var i = 0; i < data_length; ++i) {
  var latLng = new google.maps.LatLng(lionfish_locations[i].lat,
      lionfish_locations[i].long)
  var marker = new google.maps.Marker({
    position: latLng,
    draggable: true,
    icon: markerImage
  });
  markers.push(marker);
}

markerClusterer = new MarkerClusterer(map, markers, {
  zoom: 7,
  maxZoom: 17,
  minZoom: 4,
  gridSize: 150
});
}

function initialize() {
map = new google.maps.Map(document.getElementById('map'), {
  zoom: 2,
  center: new google.maps.LatLng(39.91, 116.38),
  mapTypeId: google.maps.MapTypeId.SATELLITE
});

refreshMap();
}

function clearClusters(e) {
e.preventDefault();
e.stopPropagation();
markerClusterer.clearMarkers();
}

google.maps.event.addDomListener(window, 'load', initialize);