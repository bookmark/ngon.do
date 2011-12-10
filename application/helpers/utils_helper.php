<?php

class Utils {

	public static function createSalt($length) {
		return substr(md5(uniqid() * rand(0, 1000)), 0, $length);
	}

	public static function convertVietnamesePhoneNumber($old_phone) {
		$phone_number = '';
		$length = strlen($old_phone);
		$prefix = substr($old_phone, 0, 2);

		if (false == is_numeric($phone_number) || $length < 10 || $length > 12 || false == in_array($prefix, array('09', '84'))) {
			return $phone_number;
		}

		if ($prefix == '09') {
			$phone_number = '84'.substr($old_phone, 1);
		}

		return $phone_number;
	}

	public static function convertToCell($long, $lat) {
		$root_lat = 21.025180754297857;
		$root_long = 105.84337145090103;
		$length_standard = 1; //km

		$cell_x = self::distance($lat, $root_long, $root_lat, $root_long, 'K');
		$cell_x = ($lat > $root_lat) ? $cell_x : $cell_x * -1;
		$cell_x = ceil($cell_x / $length_standard);

		$cell_y = self::distance($root_lat, $long, $root_lat, $root_long, 'K');
		$cell_y = ($long > $root_long) ? $cell_y : $cell_y * -1;
		$cell_y = ceil($cell_y / $length_standard);

		return array($cell_x, $cell_y);
	}

	public static function distance($lat1, $lon1, $lat2, $lon2, $unit) {

		$theta = $lon1 - $lon2;
		$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
		$dist = acos($dist);
		$dist = rad2deg($dist);
		$miles = $dist * 60 * 1.1515;
		$unit = strtoupper($unit);

		if ($unit == "K") {
			return ($miles * 1.609344);
		} else
			if ($unit == "N") {
				return ($miles * 0.8684);
			} else {
				return $miles;
			}
	}
}
