<?php

function geocoding($address) {
	$geocode = file_get_contents('http://maps.google.com/maps/api/geocode/json?'.urlencode($address).'&sensor=false');

	$output = json_decode($geocode);

	if (count($output->results)) {
		$lat = $output->results[0]->geometry->location->lat;
		$long = $output->results[0]->geometry->location->lng;

		return array('lat' => $lat, 'long' => $long);
	}

	return false;
}
