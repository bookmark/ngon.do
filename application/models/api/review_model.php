<?php

class Review_model extends MY_Model {

	protected $_table = 'reviews';

	public function getReviewsByLocation($limit = 0) {
		$this->select('users.username', 'reviews.content')->from($this->_table);
		$this->db->join('users', 'users.id = reviews.user_id', 'inner');
		$this->db->order_by('reviews.created_datetime', 'desc');

		if ($limit != 0) {
			$this->db->limit($limit);
		}

		return $this->db->get();
	}
}
