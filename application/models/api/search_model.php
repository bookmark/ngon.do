<?php

class Search_model extends CI_Model {

	public function __construct() {
		parent::__construct();

		$this->load->database();
	}

	public function searchNameOrDish($name = '', $user_id = 0, $long = 0, $lat = 0, $limit = 0, $max_distance = 0, $min_distance = 0) {
		$query = 'SELECT locations.name,(((acos(sin(('.$lat.'*pi()/180)) * sin((latitude*pi()/180))+cos(('.$lat.'*pi()/180)) * cos((latitude*pi()/180)) * cos((('.$long.'- longitude)*pi()/180))))*180/pi())*60*1.1515*1.609344) as distance FROM locations LEFT JOIN location_dish ON locations.id = location_dish.location_id LEFT JOIN dishes ON location_dish.dish_id = dishes.id LEFT JOIN history ON locations.id = history.location_id LEFT JOIN users ON users.id = history.user_id ';

		$condition = array();

		// find by name if length >= 3 char
		if (strlen($name) > 2) {
			$condition[] = ' locations.name LIKE \'%'.$name.'%\' OR dishes.name LIKE \'%'.$name.'%\'';
		}

		if ($max_distance > 0) {
			$condition[] = ' distance =< '.$max_distance;
		}

		if ($min_distance > 0) {
			$condition[] = ' distance >= '.$min_distance;
		}

		if (count($condition)) {
			$query .= ' WHERE'.implode(' AND ', $condition);
		}

		$query .= ' GROUP BY locations.id';

		//set limit
		if ($limit > 0) {
			$query .= ' LIMIT '.$limit;
		}

		return $this->db->query($query)->result();
	}
}
