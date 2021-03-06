jQuery(document).ready(function($){
  var spotted_markers = null,
      removed_markers = null,
      markerClusterer = null,
      map,
      selected_layer,
      marker = null;

  function refreshMap() {

    if(spotted_markers) {
      spotted_markers.clearMarkers();
    }
    if(removed_markers) {
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
      var location_type = lionfish_locations[i].location_type;

      if(location_type == 'spotted') {
        marker = new google.maps.Marker({
          position: latLng,
          draggable: true,
          title: lionfish_locations[i].location,
          icon: ajax_post_obj.LIONFISH_PLUGINURL + 'assets/img/marker-green.png'
        });

        // create an array of markers
        markers_spotted.push(marker);

      } else if ( location_type == 'removed' ) {

        marker = new google.maps.Marker({
          position: latLng,
          draggable: true,
          title: lionfish_locations[i].location,
          icon: ajax_post_obj.LIONFISH_PLUGINURL + 'assets/img/marker-red.png'
        });

        // create an array of markers
        markers_removed.push(marker);

      }


      // add infowindow
      infowindow = new google.maps.InfoWindow();
      google.maps.event.addListener(marker, 'click', (function(marker,i){
        return function(){
          var title = encodeURIComponent('Lionfish location updated');
          var url = 'http://lionfish.info/lionfish-tracking-map/';
          infowindow.setContent(
              '<div id="content">'+
              '<div id="siteNotice">'+
              '</div>'+
              '<h3 id="firstHeading" class="firstHeading">' + lionfish_locations[i].location + ' (' + lionfish_locations[i].location_type + ')' + '</h3>'+
              '<div id="bodyContent">'+
              '<p><b>Lat: </b>' + lionfish_locations[i].lat + '</p>' +
              '<p><b>Long: </b>' + lionfish_locations[i].long + '</p>' +
              '<p><b>Time: </b>' + lionfish_locations[i].time + '</p>' +
              '<p><b>date: </b>' + lionfish_locations[i].date + '</p>' +
              '<p><b>Depth in metres: </b>' + lionfish_locations[i].depth + '</p>' +
              '<p><b>Fish Number: </b>' + lionfish_locations[i].lionfish_number + '</p>' +
              '<div class="lf-share">' + 
                  '<ul>' +
                      '<li>' +
                        '<a href="http://www.facebook.com/share.php?u='+ url +'&title='+ title +'" target="_blank"><i class="fa fa-facebook"></i></a>' +
                      '</li>' + 
                      '<li>' +
                        '<a href="https://twitter.com/share?url='+ url +'&text='+ title +'&hashtags=lionfish" target="_blank"><i class="fa fa-twitter"></i></a>' +
                      '</li>' +
                      '<li>' +
                        '<a href="https://plus.google.com/share?url='+ url +'" target="_blank"><i class="fa fa-google-plus"></i></a>' +
                      '</li>' +  
                      '<li>' +
                        '<a href="https://linkedin.com/shareArticle?url='+ url +'&title='+ title +'" target="_blank"><i class="fa fa-linkedin"></i></a>' +
                      '</li>' +
                  '</ul>' +
              '</div>' +
              '</div>'+
              '</div>');
          infowindow.open(map,marker);
        }
      })(marker, i));
    
    }

    // cluster with green
    spotted_markers = new MarkerClusterer(map, markers_spotted, {
      styles:[
        {
          url: ajax_post_obj.LIONFISH_PLUGINURL + 'assets/img/cluster-green.png',
          height: 52,
          width: 53,
          anchor: [0, 0]
        },
        {
          url: ajax_post_obj.LIONFISH_PLUGINURL + 'assets/img/cluster-green.png',
          height: 52,
          width: 53,
          anchor: [0, 0]
        },
        {
          url: ajax_post_obj.LIONFISH_PLUGINURL + 'assets/img/cluster-green.png',
          height: 52,
          width: 53,
          anchor: [0, 0]
        }
      ]
    });

    // clusters with red
    removed_markers = new MarkerClusterer(map, markers_removed, {
      styles:[
        {
          url: ajax_post_obj.LIONFISH_PLUGINURL + 'assets/img/cluster-red.png',
          height: 52,
          width: 53,
          anchor: [0, 0]
        },
        {
          url: ajax_post_obj.LIONFISH_PLUGINURL + 'assets/img/cluster-red.png',
          height: 52,
          width: 53,
          anchor: [0, 0]
        },
        {
          url: ajax_post_obj.LIONFISH_PLUGINURL + 'assets/img/cluster-red.png',
          height: 52,
          width: 53,
          anchor: [0, 0]
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
      center: new google.maps.LatLng(18.372909, -64.697700),
      mapTypeId: google.maps.MapTypeId.HYBRID,
      disableDefaultUI: false
    });

    refreshMap();
    searchBox();

    $('#filter #lionfish_layers').on('change', function(){
      selected_layer = $(this).val();
      if(selected_layer == 'spotted') {
        refreshMap();
        removed_markers.clearMarkers();
      } else if (selected_layer == 'removed') {
        refreshMap();
        spotted_markers.clearMarkers();
      } else {
        refreshMap();
      }
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
          icon: ajax_post_obj.LIONFISH_PLUGINURL + 'assets/img/marker.png'
        });
      }
    }

    google.maps.event.addListener(map, "click", function(event) {
      var latitude = event.latLng.lat();
      var longitude = event.latLng.lng();

      $("input[name=lat]").val(latitude);
      $("input[name=long]").val(longitude);

      placeMarker(event.latLng);

      infowindow.setContent(
          '<div class="show-latlong"> <p> Values are added to the form </p> ' +
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
});