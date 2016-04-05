(function($){

  var markerClusterer = null;
  var map = null;
  var imageUrl = 'http://chart.apis.google.com/chart?cht=mm&chs=24x32&' +
      'chco=FFFFFF,008CFF,000000&ext=.png';

  var selected_layer_id = '';

////Set geo location lat and long
//  navigator.geolocation.getCurrentPosition(function (position, html5Error) {
//    geo_loc = processGeolocationResult(position);
//    currLatLong = geo_loc.split(",");
//    initializeCurrent(currLatLong[0], currLatLong[1]);
//  });
//
////Get geo location result
//  function processGeolocationResult(position) {
//    html5Lat = position.coords.latitude; //Get latitude
//    html5Lon = position.coords.longitude; //Get longitude
//    html5TimeStamp = position.timestamp; //Get timestamp
//    html5Accuracy = position.coords.accuracy; //Get accuracy in meters
//    return (html5Lat).toFixed(8) + ", " + (html5Lon).toFixed(8);
//  }
//
////Check value is present or
//  function initializeCurrent(latcurr, longcurr) {
//    if (latcurr != '' && longcurr != '') {
//      current_lat = latcurr;
//      current_long = longcurr;
//    } else {
//      current_lat = 39.91;
//      current_long = 116.38;
//    }
//  }

  function refreshMap() {

    if (markerClusterer) {
      markerClusterer.clearMarkers();
    }

    var markers = [];

    var markerImage = new google.maps.MarkerImage(imageUrl, new google.maps.Size(24, 32));

    var data_length = lionfish_locations.length; // get data from wp_localize_script()

    for (var i = 0; i < data_length; ++i) {

      var layers = lionfish_locations[i].layers[0];

      var latLng = new google.maps.LatLng(lionfish_locations[i].lat,
          lionfish_locations[i].long)
      var marker = new google.maps.Marker({
        position: latLng,
        draggable: true,
        icon: markerImage
      });
      if( selected_layer_id != '' ) {
        if(layers == selected_layer_id ) {
          markers.push(marker);
        }
      } else {
        markers.push(marker);
      }
    }

    markerClusterer = new MarkerClusterer(map, markers, {
      zoom: 10,
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

    $('#filter #lionfish_layers').on('change', function(){
      selected_layer_id = $(this).val();
      refreshMap();
    });

    refreshMap();
  }


  google.maps.event.addDomListener(window, 'load', initialize);

})(jQuery);