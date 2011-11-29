<?php

class Dish_model extends MY_Model {
	protected $_table = 'dishes';

	public function getMenuByLocationId($loc_id) {
		$this->db->select('dishes.id', 'dishes.name')->from('dishes');
		$this->db->join('location_dish', 'dishes.id = location_dish.dish_id', 'inner');
		$this->db->join('locations', 'locations.id = location_dish.location_id', 'inner');
		$this->db->where('locations.id', $loc_id);

		return $this->db->get();
	}
}
