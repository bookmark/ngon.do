<?php

class User_model extends CI_Model
{

	public function __construct ()
	{
		$this->load->database();
	}

	public function getInfo ($user_id)
	{
		return $this->db->select()->from('users')->where('id', $user_id)->limit(1)->get()->row();
	}

	public function register ($username, $password, $salt, $phone)
	{
		$hash_pass = md5($password . $salt);

		$data = array('username' => $username,
			'password' => $hash_pass,
			'salt' => $salt,
			'phone_number' => $phone);

		$this->db->insert('users', $data);

		return $this->db->insert_id();
	}

	public function delete ($user_id)
	{
		$this->db->delete('users')->where('id', $user_id)->limit(1)->get();
	}

	/**
	 * Check username exist - true if has exist | false if no exist
	 * @param string $username
	 */

	public function checkUsernameExist ($username)
	{
		$this->db->select()->from('users')->where('username', $username)->limit(1);
		return (bool) $this->db->count_all_results();
	}

	/**
	 * Check phone number exist - true if has exist | false if no exist
	 * @param string $phone
	 */

	public function checkPhoneExist ($phone)
	{
		$this->db->select()->from('users')->where('phone_number', $phone)->limit(1);
		return (bool) $this->db->count_all_results();
	}
}
