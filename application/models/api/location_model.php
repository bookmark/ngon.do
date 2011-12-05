<?php

class Location_model extends MY_Model {

	protected $_table = 'locations';

	public function create($owner_id, $name, $long, $lat) {
		$this->db->query('INSERT INTO `locations` (`owner_id`, `location`, `name`) VALUES (?, GeomFromText(\'POINT(? ?)\'), ?)', array($owner_id, $long, $lat, $name));
		return $this->db->insert_id();
	}

	public function getLocationInfo($loc_id) {
		$this->db->select('name', 'address', 'like', 'checkin');
		$this->db->from($this->_table)->where('id', $loc_id);
		$this->db->limit(1);

		return $this->db->get()->row();
	}

	public function getTopLike($limit) {
		$this->db->select()->from($this->_table);
		$this->db->order_by('like', 'desc');
		if ($limit > 0) {
			$this->db->limit($limit);
		}

		return $this->db->get()->row();
	}

	public function getTopCheckin($limit) {
		$this->db->select()->from($this->_table);
		$this->db->order_by('checkin', 'desc');
		if ($limit > 0) {
			$this->db->limit($limit);
		}

		return $this->db->get()->row();
	}

	public function getTopNew($limit) {
		$this->db->select()->from($this->_table);
		$this->db->order_by('id', 'desc');
		if ($limit > 0) {
			$this->db->limit($limit);
		}

		return $this->db->get()->row();
	}

	public function checkLocationIdExist($loc_id) {
		$this->db->select()->from($this->_table)->where('location_id', $loc_id)->limit(1);
		return (bool) $this->db->count_all_results();
	}

	public function updateCheckinNumber($loc_id, $num = 1) {
		$this->db->query('UPDATE locations SET checkin = checkin + ? ', array($num))->get();
	}
}
