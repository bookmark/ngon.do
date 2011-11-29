<?php

class Photo_model extends MY_Model {

	protected $_table = 'photos';

	public function getPhotosByLocationId($loc_id, $limit) {
		$this->db->select('file_name')->from('photos');
		$this->db->where($this->_table);
		$this->db->order_by('created_datetime', 'desc')->limit($limit);

		return $this->db->get();
	}
}
