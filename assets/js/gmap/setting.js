(function($){

  var spotted_markers = null,
      removed_markers = null,
      map = null,
      selected_layer_id = 0;

  function refreshMap() {

    // clear all markers
    if (spotted_markers) {
      spotted_markers.clearMarkers();
    }

    if (removed_markers) {
      removed_markers.clearMarkers();
    }

    // locate to users location
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function (position) {
        initialLocation = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
        map.setCenter(initialLocation);
      });
    }

    // create markers
    var markers_spotted = [];
    var markers_removed = [];

    var markerImage = new google.maps.MarkerImage( ajax_post_obj.LIONFISH_PLUGINURL + 'assets/img/marker.png', new google.maps.Size(24, 32));
    var data_length = lionfish_locations.length; // get data from wp_localize_script()
    for (var i = 0; i < data_length; i++) {
      var latLng = new google.maps.LatLng(lionfish_locations[i].lat,
          lionfish_locations[i].long);
      var layers_id = lionfish_locations[i].layers_id[0];
      var location_type = lionfish_locations[i].location_type;

      if(location_type == 'spotted') {
        var marker = new google.maps.Marker({
          position: latLng,
          draggable: true,
          title: lionfish_locations[i].location,
          icon: ajax_post_obj.LIONFISH_PLUGINURL + 'assets/img/marker.png'
        });

        // create an array of markers
        if( selected_layer_id == 0  ) {
          markers_spotted.push(marker);
        } else {
          if(layers_id == selected_layer_id ) {
            markers_spotted.push(marker);
          }
        }

      } else if ( location_type == 'removed' ) {

        var marker = new google.maps.Marker({
          position: latLng,
          draggable: true,
          title: lionfish_locations[i].location,
          icon: ajax_post_obj.LIONFISH_PLUGINURL + 'assets/img/marker.png'
        });

        // create an array of markers
        if( selected_layer_id == 0  ) {
          markers_removed.push(marker);
        } else {
          if(layers_id == selected_layer_id ) {
            markers_removed.push(marker);
          }
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
              '<p><b>Time: </b>' + lionfish_locations[i].time + '</p>' +
              '<p><b>date: </b>' + lionfish_locations[i].date + '</p>' +
              '<p><b>Depth in metres: </b>' + lionfish_locations[i].depth + '</p>' +
              '<p><b>Layers: </b>' + lionfish_locations[i].layers_name[0] + '</p>' +
              '<p><b>Fish Number: </b>' + lionfish_locations[i].lionfish_number + '</p>' +
              '</div>'+
              '</div>');
          infowindow.open(map,marker);
        }
      })(marker, i));

    }

    // Add marker clustering with default styles
    var spotted_markers = new MarkerClusterer(map, markers_spotted);

// Custom styles
    var removed_markers = new MarkerClusterer(map, markers_removed, {
      styles:[
        {
          url: ajax_post_obj.LIONFISH_PLUGINURL + 'assets/img/m3.png',
          height: 55,
          width: 55,
          opt_anchor: [16, 0],
          opt_textColor: '#FFFFFF'
        },
        {
          url: ajax_post_obj.LIONFISH_PLUGINURL + 'assets/img/m3.png',
          height: 54,
          width: 55,
          opt_anchor: [16, 0],
          opt_textColor: '#FFFFFF'
        },
        {
          url: ajax_post_obj.LIONFISH_PLUGINURL + 'assets/img/m3.png',
          height: 54,
          width: 55,
          opt_anchor: [16, 0],
          opt_textColor: '#FFFFFF'
        }
      ]
    });


  }

  // create search box
  function searchBox() {
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

  // initialize the map
  function initialize() {
    map = new google.maps.Map(document.getElementById('map'), {
      zoom: 10,
      center: new google.maps.LatLng(39.91, 116.38),
      mapTypeId: google.maps.MapTypeId.HYBRID,
      disableDefaultUI: false
    });

    refreshMap();
    searchBox();

    $('#filter #lionfish_layers').on('change', function(){
      selected_layer_id = $(this).val();
      refreshMap();
    });

    // get lat long on click map
    var getlatlong;
    function placeMarker(location) {
      if (getlatlong) {
        //if marker already was created change positon
        getlatlong.setPosition(location);
      } else {
        //create a marker
        getlatlong = new google.maps.Marker({
          position: location,
          map: map,
          draggable: true,
          icon: ajax_post_obj.LIONFISH_PLUGINURL + 'assets/img/marker2.png'
        });
      }
    }

    google.maps.event.addListener(map, "click", function(event) {
      var latitude = event.latLng.lat();
      var longitude = event.latLng.lng();

      placeMarker(event.latLng);

      infowindow.setContent(
          '<div class="show-latlong">' +
          '<label>Latitude: </label><input type="text" name="lat" value="' + latitude + '">' +
          '<label>Longitude: </label><input type="text" name="long" value="' + longitude + '">' +
          '</div>'
      );
      infowindow.open(map, getlatlong);

      // Center of map
      map.panTo(new google.maps.LatLng(latitude,longitude));

    }); //end addListener


  }

  // load map
  google.maps.event.addDomListener(window, 'load', initialize);

})(jQuery);