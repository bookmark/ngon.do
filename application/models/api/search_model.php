<?php

class Search_model extends CI_Model {

	public function __construct() {
		parent::__construct();

		$this->load->database();
		$this->load->helper('utils');
	}

	public function nearby($user_id, $long, $lat, $limit = 10, $max_length = 2) {
		$cell = Utils::convertToCell($long, $lat);
		$query = $this->db->query('SELECT name,spot_distance.distance FROM spots INNER JOIN (SELECT spot_id,( GLength( LineString(( PointFromWKB( POINT(?,?))), ( PointFromWKB( location  ))))) * 100 AS distance FROM spot_geo WHERE (cell_x BETWEEN ? AND ?) AND (cell_y BETWEEN ? AND ?) ORDER BY distance LIMIT ?) AS spot_distance ON spot_distance.spot_id = spots.id', array($long, $lat, $cell[0] - $max_length, $cell[0] + $max_length, $cell[1] - $max_length, $cell[1] + $max_length, $limit));
		return $query->result();
	}

	public function searchName($name, $long, $lat, $limit = 10, $max_length = 2) {
		$cell = Utils::convertToCell($long, $lat);

		$query = $this->db->query('SELECT name,spot_distance.distance FROM spots INNER JOIN (SELECT spot_id,( GLength( LineString(( PointFromWKB( POINT(?,?))), ( PointFromWKB( location  ))))) * 100 AS distance FROM spot_geo WHERE spot_id IN (SELECT spot_id FROM spot_search WHERE MATCH (name) AGAINST (? IN BOOLEAN MODE)) AND (cell_x BETWEEN ? AND ?) AND (cell_y BETWEEN ? AND ?) ORDER BY distance LIMIT ?) AS spot_distance ON spot_distance.spot_id = spots.id', array($long, $lat, $name, $cell[0] - $max_length, $cell[0] + $max_length, $cell[1] - $max_length, $cell[1] + $max_length, $limit));
		echo $this->db->last_query();
		return $query->result();
	}

	public function searchNameOrDish($name = '', $user_id = 0, $long = 0, $lat = 0, $limit = 0, $max_distance = 0, $min_distance = 0) {
		$query = 'SELECT spots.name,(((acos(sin(('.$lat.'*pi()/180)) * sin((latitude*pi()/180))+cos(('.$lat.'*pi()/180)) * cos((latitude*pi()/180)) * cos((('.$long.'- longitude)*pi()/180))))*180/pi())*60*1.1515*1.609344) as distance FROM spots LEFT JOIN location_dish ON spots.id = location_dish.spot_id LEFT JOIN dishes ON location_dish.dish_id = dishes.id LEFT JOIN history ON spots.id = history.spot_id LEFT JOIN users ON users.id = history.user_id ';

		$condition = array();

		// find by name if length >= 3 char
		if (strlen($name) > 2) {
			$condition[] = ' spots.name LIKE \'%'.$name.'%\' OR dishes.name LIKE \'%'.$name.'%\'';
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

		$query .= ' GROUP BY spots.id';

		//set limit
		if ($limit > 0) {
			$query .= ' LIMIT '.$limit;
		}

		return $this->db->query($query)->result();
	}
}
