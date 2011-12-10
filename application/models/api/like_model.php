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

}
