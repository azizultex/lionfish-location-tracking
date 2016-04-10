(function($){

  var markerClusterer = null;
  var map = null;
  var imageUrl = 'http://chart.apis.google.com/chart?cht=mm&chs=24x32&' +
      'chco=FFFFFF,008CFF,000000&ext=.png';

  var selected_layer_id = 0, current_lat, current_long;

//Set geo location lat and long
  navigator.geolocation.getCurrentPosition(function (position, html5Error) {
    geo_loc = processGeolocationResult(position);
    currLatLong = geo_loc.split(",");
    initializeCurrent(currLatLong[0], currLatLong[1]);
  });

//Get geo location result
  function processGeolocationResult(position) {
    html5Lat = position.coords.latitude; //Get latitude
    html5Lon = position.coords.longitude; //Get longitude
    html5TimeStamp = position.timestamp; //Get timestamp
    html5Accuracy = position.coords.accuracy; //Get accuracy in meters
    return (html5Lat).toFixed(8) + ", " + (html5Lon).toFixed(8);
  }

//Check value is present or
  function initializeCurrent(latcurr, longcurr) {
    if (latcurr != '' && longcurr != '') {
      current_lat = latcurr;
      current_long = longcurr;
    } else {
      current_lat = 39.91;
      current_long = 116.38;
    }
  }

  console.log(current_lat + ' ' +  current_long);

  function refreshMap() {

    if (markerClusterer) {
      markerClusterer.clearMarkers();
    }

    var markers = [];

    var markerImage = new google.maps.MarkerImage(imageUrl, new google.maps.Size(24, 32));

    var data_length = lionfish_locations.length; // get data from wp_localize_script()

    for (var i = 0; i < data_length; i++) {

      var latLng = new google.maps.LatLng(lionfish_locations[i].lat,
          lionfish_locations[i].long);
      var layers_id = lionfish_locations[i].layers_id[0]
      var marker = new google.maps.Marker({
        position: latLng,
        draggable: true,
        title: lionfish_locations[i].location,
        icon: markerImage
      });

      // create an array of markers
      if( selected_layer_id == 0  ) {
        markers.push(marker);
      } else {
        if(layers_id == selected_layer_id ) {
          markers.push(marker);
        }
      }

      // add infowindow
      infowindow = new google.maps.InfoWindow();
      google.maps.event.addListener(marker, 'click', (function(marker,i){
        return function(){
          infowindow.setContent(
              '<div id="content">'+
              '<div id="siteNotice">'+
              '</div>'+
              '<h3 id="firstHeading" class="firstHeading">' + lionfish_locations[i].location + '</h3>'+
              '<div id="bodyContent">'+
              '<p><b>Lat: </b>' + lionfish_locations[i].lat + '</p>' +
              '<p><b>Long: </b>' + lionfish_locations[i].long + '</p>' +
              '<p><b>Layers: </b>' + lionfish_locations[i].layers_name[0] + '</p>' +
              '<p><b>Fish Number: </b>' + lionfish_locations[i].lionfish_number + '</p>' +
              '</div>'+
              '</div>');
          infowindow.open(map,marker);
        }
      })(marker, i));

    }

    // add clusters
    markerClusterer = new MarkerClusterer(map, markers, {
      zoom: 10,
      maxZoom: 17,
      minZoom: 4,
      gridSize: 150
    });

  }

  function initialize() {

    map = new google.maps.Map(document.getElementById('map'), {
      zoom: 1,
      center: new google.maps.LatLng(39.91, 116.38),
      mapTypeId: google.maps.MapTypeId.SATELLITE
    });

    $('#filter #lionfish_layers').on('change', function(){
      selected_layer_id = $(this).val();
      console.log(selected_layer_id);
      refreshMap();
    });

    refreshMap();

    // Create the search box and link it to the UI element.
    var input = document.getElementById('pac-input');
    var searchBox = new google.maps.places.SearchBox(input);
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

    // Bias the SearchBox results towards current map's viewport.
    map.addListener('bounds_changed', function() {
      searchBox.setBounds(map.getBounds());
    });

    var markers = [];
    // Listen for the event fired when the user selects a prediction and retrieve
    // more details for that place.
    searchBox.addListener('places_changed', function() {
      var places = searchBox.getPlaces();

      if (places.length == 0) {
        return;
      }

      // Clear out the old markers.
      markers.forEach(function(marker) {
        marker.setMap(null);
      });
      markers = [];

      // For each place, get the icon, name and location.
      var bounds = new google.maps.LatLngBounds();
      places.forEach(function(place) {
        var icon = {
          url: place.icon,
          size: new google.maps.Size(71, 71),
          origin: new google.maps.Point(0, 0),
          anchor: new google.maps.Point(17, 34),
          scaledSize: new google.maps.Size(25, 25)
        };

        // Create a marker for each place.
        markers.push(new google.maps.Marker({
          map: map,
          icon: icon,
          title: place.name,
          position: place.geometry.location
        }));

        if (place.geometry.viewport) {
          // Only geocodes have viewport.
          bounds.union(place.geometry.viewport);
        } else {
          bounds.extend(place.geometry.location);
        }
      });
      map.fitBounds(bounds);
    });

  }

  google.maps.event.addDomListener(window, 'load', initialize);

})(jQuery);