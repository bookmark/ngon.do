<?php

class History_model extends MY_Model {
	protected $_table = 'history';

	public function getLatestHistoryFromLocationId($user_id, $location_id) {
		$this->db->select()->from('history');
		$this->db->where('user_id', $user_id);
		$this->db->where('location_id', $location_id);
		$this->db->order_by('created_datetime', 'desc');
		$this->db->limit(1);

		return $this->db->get()->row();
	}
}
