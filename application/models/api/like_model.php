<?php

class Like_model extends MY_Model {

	protected $_table = 'likes';

	/**
	 *
	 * Check like by location exist
	 * @param int $user_id
	 * @param int $spot_id
	 *
	 * @return true: if already like, false is not exist
	 */

	public function checkLikeExist($user_id, $spot_id) {
		$this->db->select()->from($this->_table)->where('user_id', $user_id)->where('spot_id', $spot_id)->limit(1);
		return (bool) $this->db->count_all_results();
	}

	public function getLikeDetail($user_id, $limit = 0) {
		$this->db->select(array('likes.spot_id', 'name', 'likes.created_datetime'))->from($this->_table);
		$this->db->join('spots', 'spots.id = likes.spot_id');
		$this->db->where('user_id', $user_id);
		$this->db->order_by('likes.created_datetime', 'desc');
		if ($limit > 0) {
			$this->db->limit($limit);
		}

		return $this->db->get();
	}
}
