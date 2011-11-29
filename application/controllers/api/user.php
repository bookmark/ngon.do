<?php

require APPPATH . '/libraries/REST_Controller.php';

class User extends REST_Controller
{
	private $require_param = array('username', 'password', 'phone');

	public function __construct ()
	{
		parent::__construct();

		//load model
		$this->load->model('api/User_model', 'user');
		$this->load->helper('utils');
		$this->load->helper('valid');
	}

	/**
	 * Get profile by user_id
	 * Enter description here ...
	 */

	public function profile_get ()
	{
		$user_id = intval($this->get('id'));

		if ($user_id == 0)
			$this->response(array('status' => false, 'error' => 'user_id missing'), 404);
		else
			$this->response(($this->user->getInfo($user_id)), 200);
	}

	/**
	 *
	 * Create user
	 */

	public function index_put ()
	{
		// check require parametter
		foreach ($this->require_param as $param) {
			if (false == array_key_exists($param, $this->put())) {
				$this->response(array('status' => false, 'error' => $param . ' is not exist'), 405);
				return;
			}
		}

		$username = $this->put('username');
		$password = $this->put('password');
		$phone = Utils::convertVietnamesePhoneNumber($this->put('phone'));

		// valid password
		$password_length = strlen($password);

		if ($password_length < 5 || $password_length > 20) {
			$this->response(array('status' => false, 'error' => 'password length is 5-20 chars'), 405);
			return;
		}

		// valid username
		$username_length = strlen($username);

		if ($username_length < 3 || $username_length > 50 || false == Valid::username($username)) {
			$this->response(array('status' => false, 'error' => 'username length is 3-50 chars and allow alphanum and underscore'), 405);
			return;
		}

		if ($this->user->checkUsernameExist($username)) {
			$this->response(array('status' => false, 'error' => 'username has been exist'), 405);
			return;
		}

		// valid phone number
		if (strlen($phone) == 0) {
			$this->response(array('status' => false, 'error' => 'phone number wrong format'), 405);
			return;
		}

		if ($this->user->checkPhoneExist($phone)) {
			$this->response(array('status' => false, 'error' => 'phone number has been exist'), 405);
			return;
		}

		$salt = Utils::createSalt(5);
		$user_id = $this->user->register($username, $password, $salt, $phone);

		// unset variable
		unset($username, $username_length, $password, $password_length, $phone, $salt);

		$this->response(array('status' => true, 'user_id' => $user_id), 201);
	}

}
