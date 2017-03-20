<?php
  ini_set('display_errors', 'On');
  error_reporting(E_ALL);

  session_start();
  if(!$_SESSION['username']) {
    header('location: login.php');
  }

  function sortEvents($events)
  {
    $event_times = array();
    foreach ($events as $key => $row) {
      $event_times[$key] = $row['start_time'];
    }
    array_multisort($event_times, SORT_ASC, $events);

    return $events;
  }

  // function to geocode address, it will return false if unable to geocode address
  function geocode($address){

    // url encode the address
    $address = urlencode($address);

    // google map geocode api url
    $url = "http://maps.google.com/maps/api/geocode/json?address={$address}";

    // get the json response
    $resp_json = file_get_contents($url);

    // decode the json
    $resp = json_decode($resp_json, true);

    // response status will be 'OK', if able to geocode given address
    if($resp['status']=='OK'){

        // get the important data
        $lati = $resp['results'][0]['geometry']['location']['lat'];
        $longi = $resp['results'][0]['geometry']['location']['lng'];
        $formatted_address = $resp['results'][0]['formatted_address'];

        // verify if data is complete
        if($lati && $longi && $formatted_address){

            // put the data in the array
            $data_arr = array();

            array_push(
                $data_arr,
                    $lati,
                    $longi,
                    $formatted_address
                );

            return $data_arr;

        }else{
            return false;
        }

    }else{
        return false;
    }
  }

  $file_contents = file_get_contents('calendar.txt');
  $events = json_decode($file_contents, true);

  $mon_events = array();
  $tues_events = array();
  $wed_events = array();
  $thurs_events = array();
  $fri_events = array();

  foreach ($events as $event) {
    $event_name = $event['event_name'];
    $start = $event['start_time'];
    $end = $event['end_time'];
    $location = $event['location'];
    $day = $event['day'];

    $loc_arr = geocode($location);

    $latitude = null;
    $longitude = null;

    if($loc_arr) {
      $latitude = $loc_arr[0];
      $longitude = $loc_arr[1];
    }

    $event_item = array('event_name' => $event_name,
	            'start_time' => $start,
	            'end_time' => $end,
	            'location' => $location,
	            'day' => $day,
              'lng' => $longitude,
              'lat' => $latitude);

    switch ($day) {
      case 'Monday':
        array_push($mon_events, $event_item);
        break;
      case 'Tuesday':
        array_push($tues_events, $event_item);
        break;
      case 'Wednesday':
        array_push($wed_events, $event_item);
        break;
      case 'Thursday':
        array_push($thurs_events, $event_item);
        break;
      case 'Friday':
        array_push($fri_events, $event_item);
        break;
      default:
        # code...
        break;
    }

    $mon_events = sortEvents($mon_events);
    $tues_events = sortEvents($tues_events);
    $wed_events = sortEvents($wed_events);
    $thurs_events = sortEvents($thurs_events);
    $fri_events = sortEvents($fri_events);
  }
 ?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta name="viewport" content="initial-scale=1.0">
		<meta charset="utf-8">
		<title> My schedule </title>
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>
	<body>

		<div class="nav">
			<nav>
				<a href="calendar.php">Calendar</a>
				<a href="input.php">Input</a>
        <a href="admin.php">Admin</a>
        <a href="logout.php">Log out</a>
			</nav>
		</div>

    <p align="center" style="font-size: 25px;color: white; font-weight: bold;">
      Logged  in as <?php echo $_SESSION['username']?></p>

		<script>
			var $ = function(id) { return document.getElementById(id); }

			function imageLocationShow(id)
			{
				$(id).style.display = 'inline';
			}

			function imageLocationHide(id)
			{
				$(id).style.display = 'none';
			}

			var umn = {lat: 44.974, lng: -93.234};

			var map;
			var infowindow;
			var service;

			function initMap()
			{
				map = new google.maps.Map(document.getElementById('map-container'), {
					center: umn,
					zoom: 16
				});

				infowindow = new google.maps.InfoWindow();
				service = new google.maps.places.PlacesService(map);
				var geocoder = new google.maps.Geocoder();

				document.getElementById('submit').addEventListener('click', function() {
					geocodeAddress(geocoder, map);
				});

				document.getElementById('submit-distance').addEventListener('click', function() {
					getLocations(document.getElementById('distance').value);
				});

				loadInitialMarkers();
			}

			function geocodeAddress(geocoder, resultsMap)
			{
				var address = document.getElementById('address').value;
				geocoder.geocode({'address': address}, function(results, status) {
					if (status === 'OK') {
						resultsMap.setCenter(results[0].geometry.location);
						var marker = new google.maps.Marker({
							map: resultsMap,
							position: results[0].geometry.location
						});
					} else {
						alert('Geocode was not successful for the following reason: ' + status);
					}
				});
			}

			function loadInitialMarkers()
			{
				loadDayMarkers(<?php echo json_encode($mon_events); ?>);
        loadDayMarkers(<?php echo json_encode($tues_events); ?>);
        loadDayMarkers(<?php echo json_encode($mon_events); ?>);
        loadDayMarkers(<?php echo json_encode($thurs_events); ?>);
        loadDayMarkers(<?php echo json_encode($fri_events); ?>);
			}

      function loadDayMarkers(day_events)
      {
        for(var i = 0; i < day_events.length; i++)
        {
          var name = day_events[i].event_name;
          var lat_val = day_events[i].lat;
          var lng_val = day_events[i].lng;

          var locs = {lat: lat_val, lng: lng_val};
          createAnimatedMarker(locs, name);
        }
      }

			function getLocations(distance)
			{
				service.nearbySearch({
					location: umn,
					radius: distance,
					type: ['restaurant']
				}, callback);
			}

			function callback(results, status)
			{
				if (status === google.maps.places.PlacesServiceStatus.OK) {
					for (var i = 0; i < results.length; i++) {
						createMarker(results[i]);
					}
				}
			}

			function createAnimatedMarker(location, name)
			{
				var marker = new google.maps.Marker({
					map: map,
					animation: google.maps.Animation.BOUNCE,
					position: location
				});

				google.maps.event.addListener(marker, 'click', function() {
					infowindow.setContent(name);
					infowindow.open(map, this);
				});
			}

			function createMarker(place)
			{
				var placeLoc = place.geometry.location;
				var marker = new google.maps.Marker({
					map: map,
					position: place.geometry.location
				});

				google.maps.event.addListener(marker, 'click', function() {
					infowindow.setContent('<div><strong>' + place.name + '</strong><br>'
					+ '<br>' + place.vicinity + '</div>');
					infowindow.open(map, this);
				});
			}
		</script>
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBPOQ_g2dglQgoODCmmKmydpFqMTSPK4H4&libraries=places&callback=initMap"
		async defer></script>

    <div id="error-msg-container">
      <span>
        <?php if(!$events) echo "<p> Calendar has no events. Please use the input page to enter some events";?>
      </span> <br/>
    </div>

		<div id="calendar-container">
      <?php
      function generateTableRow($day_events)
      {
        if(!empty($day_events))
        {

          echo "<tr>\n";

          echo "<td class='day-header'><span>".$day_events[0]['day']."</span></td>\n";

          foreach ($day_events as $event) {
            echo "<td>\n";
            echo "<p class='time'>".$event['start_time']." - ".$event['end_time']."</p>\n";
            echo "<p>".$event['event_name']." - ".$event['location']."</p>\n";
            echo "</td>\n";
          }

          echo "</tr>\n";
        }
      }

      echo "<table class='content-table'>\n";
      generateTableRow($mon_events);
      generateTableRow($tues_events);
      generateTableRow($wed_events);
      generateTableRow($thurs_events);
      generateTableRow($fri_events);

      echo "</table><br>\n";
      ?>
		</div>

		<div id="map-input-container" class="center">
			<b>Enter a location:</b>
			<input id="address" type="text">
			<input id="submit" type="button" value="Geocode">
			<br>
			<b>Enter a distance (between 100m and 2000m):</b>
			<input id="distance" type="number" min="100" max="2000">
			<input id="submit-distance" type="button" value="Find Nearby Restaurants">
		</div>

		<div id="map-container"></div>

		<p class="center"> This page has been tested in Chrome. </p>
	</body>
</html>
