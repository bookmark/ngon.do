<?php

class User extends REST_Controller {
	private $require_param = array('username', 'password', 'phone');

	public function __construct() {
		parent::__construct();

		//load model
		$this->load->model('api/User_model', 'user');
		$this->load->model('api/Like_model', 'like');
		$this->load->model('api/History_model', 'history');
		$this->load->helper('utils');
		$this->load->helper('valid');
	}

	/**
	 * Get profile by user_id
	 */

	public function index_get() {
		$user_id = intval($this->get('user_id'));

		if ($user_id == 0) {
			$this->response(array('status' => false, 'error' => 'user_id missing'), 404);
		}

		$data = array();
		$data['status'] = true;
		$data['info'] = $this->user->getInfo($user_id);
		$data['like_number'] = $this->like->count_by(array('user_id' => $user_id));
		$data['checkin_number'] = $this->history->count_by(array('user_id' => $user_id));

		$this->response($data, 200);
	}

	/**
	 *
	 * Create user
	 */

	public function index_put() {
		// check require parametter
		foreach ($this->require_param as $param) {
			if (false == array_key_exists($param, $this->put())) {
				$this->response(array('status' => false, 'error' => $param.' is not exist'), 405);
				return;
			}
		}

		$username = $this->put('username');
		$password = $this->put('password');

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

		$phone = array_key_exists('phone', $this->put()) ? Utils::convertVietnamesePhoneNumber($this->put('phone')) : null;

		if ($phone != null) {
			// valid phone number
			if (strlen($phone) == 0) {
				$this->response(array('status' => false, 'error' => 'phone number wrong format'), 405);
				return;
			}

			if ($this->user->checkPhoneExist($phone)) {
				$this->response(array('status' => false, 'error' => 'phone number has been exist'), 405);
				return;
			}
		}

		$email = array_key_exists('email', $this->put()) ? $this->put('email') : null;

		if ($email != null) {
			if (Valid::email($email) == false) {
				$this->response(array('status' => false, 'error' => 'Email is wrong format'), 405);
				return;
			}

			if ($this->user->checkEmailExist($email)) {
				$this->response(array('status' => false, 'error' => 'Email has been exist'), 405);
				return;
			}
		}

		$data = array();
		$data['username'] = $username;
		$data['salt'] = Utils::createSalt(5);
		$data['password'] = md5($password.$data['salt']);
		$data['email'] = $email;
		$data['phone'] = $phone;

		$user_id = $this->user->insert($data);

		$this->response(array('status' => true, 'user_id' => $user_id), 201);
	}

	public function like_get() {
		$user_id = intval($this->get('user_id'));

		if ($user_id == 0) {
			$this->response(array('status' => false, 'error' => 'user_id missing'), 404);
		}

		$limit = array_key_exists('limit', $this->get()) ? intval($this->get('limit')) : 10;

		$data = array();
		$data['status'] = true;
		$data['data'] = $this->like->getLikeDetail($user_id, $limit);

		$this->response($data, 200);
	}

	public function history_get() {
		$user_id = intval($this->get('user_id'));

		if ($user_id == 0) {
			$this->response(array('status' => false, 'error' => 'user_id missing'), 404);
		}

		$limit = array_key_exists('limit', $this->get()) ? intval($this->get('limit')) : 10;

		$data = array();
		$data['status'] = true;
		$data['data'] = $this->history->getHistoryDetail($user_id, $limit);

		$this->response($data, 200);
	}

}
