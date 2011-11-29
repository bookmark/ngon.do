<?php

class Location_model extends MY_Model {

	protected $_table = 'locations';

	public function getLocationInfo($loc_id) {
		$this->db->select('name', 'address', 'like', 'checkin');
		$this->db->from('locations')->where('id', $loc_id);
		$this->db->limit(1);

		return $this->db->get()->row();
	}
}
