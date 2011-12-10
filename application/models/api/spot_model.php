<?php

class Spot_model extends MY_Model {

	protected $_table = 'spots';

	public function __construct() {
		parent::__construct();
		$this->load->helper('utils');
	}

	public function create($owner_id, $name, $long, $lat) {
		$cell = Utils::convertToCell($long, $lat);
		$this->db->query('INSERT INTO `spots` (`owner_id`, `name`) VALUES (?, ?)', array($owner_id, $name));
		$spot_id = $this->db->insert_id();
		$this->db->query('INSERT INTO `spot_geo` (`spot_id`, `location`, `cell_x`, `cell_y`) VALUES (?, GeomFromText(\'POINT(? ?)\'), ?, ?)', array($spot_id, $long, $lat, $cell[0], $cell[1]));
		$this->db->query('INSERT INTO `spot_search` (`spot_id`, `name`) VALUES (?, ?)', array($spot_id, $name));
		return $spot_id;
	}

	public function getSpotInfo($spot_id) {
		$this->db->select('name', 'address', 'like', 'checkin');
		$this->db->from($this->_table)->where('id', $spot_id);
		$this->db->limit(1);

		return $this->db->get()->row();
	}

	public function getTopLike($limit, $cache = TRUE) {
		$this->db->select()->from($this->_table);

		if ($cache) {
			$this->db->join('top_like', 'top_like.spot_id = spots.id');
		} else {
			$this->db->order_by('like', 'desc');
		}

		//set limit
		if ($limit > 0) {
			$this->db->limit($limit);
		}

		return $this->db->get();
	}

	public function getTopCheckin($limit, $cache = TRUE) {
		$this->db->select()->from($this->_table);

		if ($cache) {
			$this->db->join('top_checkin', 'top_checkin.spot_id = spots.id');
		} else {
			$this->db->order_by('checkin', 'desc');
		}

		if ($limit > 0) {
			$this->db->limit($limit);
		}

		return $this->db->get();
	}

	public function getTopNew($limit) {
		$this->db->select()->from($this->_table);
		$this->db->order_by('id', 'desc');
		if ($limit > 0) {
			$this->db->limit($limit);
		}

		return $this->db->get();
	}

	public function checkLocationIdExist($spot_id) {
		$this->db->select()->from($this->_table)->where('id', $spot_id)->limit(1);
		return (bool) $this->db->count_all_results();
	}

	public function updateCheckinNumber($spot_id, $num = 1) {
		$this->db->query('UPDATE `spots` SET `checkin` = `checkin` + ? WHERE `id` = ? LIMIT 1', array($num, $spot_id));
	}

	public function updateLikeNumber($spot_id, $num = 1) {
		$this->db->query('UPDATE `spots` SET `like` = `like` + ? WHERE `id` = ? LIMIT 1', array($num, $spot_id));
	}
}
