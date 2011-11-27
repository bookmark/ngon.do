<?php


class Location_model extends CI_Model{


	public function __construct(){
		$this->load->database();
	}


	public function add($name, $longitude,$latitude,$owner_id,$address){
		$data = array('name' => $name, 'longitude' => $longitude, 'latitude' => $latitude, 'owner_id' => $owner_id, 'address' => $address);
		$this->db->insert('locations', $data);

		return $this->db->insert_id();
	}
}