<?php

class History_model extends MY_Model {
	protected $_table = 'history';

	public function getLatestHistoryFromSpotId($user_id, $spot_id) {
		$this->db->select()->from('history');
		$this->db->where('user_id', $user_id);
		$this->db->where('spot_id', $spot_id);
		$this->db->order_by('created_datetime', 'desc');
		$this->db->limit(1);

		return $this->db->get()->row();
	}

	public function getHistoryDetail($user_id, $limit = 0) {
		$this->db->select(array('history.spots_id', 'name', 'history.created_datetime', 'dishes'))->from($this->_table);
		$this->db->join('spots', 'spots.id = history.spot_id');
		$this->db->where('user_id', $user_id);
		$this->db->order_by('history.created_datetime', 'desc');
		if ($limit > 0) {
			$this->db->limit($limit);
		}

		return $this->db->get();
	}
}
