<?php

require APPPATH.'/libraries/REST_Controller.php';

class Spot extends REST_Controller {

	public function __construct() {
		parent::__construct();

		// load model
		$this->load->model('api/Photo_model', 'photo');
		$this->load->model('api/Spot_model', 'spot');
		$this->load->model('api/Dish_model', 'dish');
		$this->load->model('api/History_model', 'history');
		$this->load->model('api/Review_model', 'review');
		$this->load->model('api/Search_model', 'search');
		$this->load->model('api/Like_model', 'like');
	}

	/**
	 *
	 * Create location
	 */

	public function index_put() {
		$user_id = 1;
		$require_param = array('name', 'long', 'lat');

		// check require parametter
		foreach ($require_param as $param) {
			if (false == array_key_exists($param, $this->put())) {
				$this->response(array('status' => false, 'error' => $param.' is not exist'), 405);
				return;
			}
		}

		$long = floatval($this->put('long'));
		$lat = floatval($this->put('lat'));
		$name = trim($this->put('name'));

		$name_length = strlen($name);
		if ($name_length < 3 || $username_length > 50 || false == Valid::username($username)) {
			$this->response(array('status' => false, 'error' => 'username length is 3-50 chars and allow alphanum and underscore'), 405);
			return;
		}

		$spot_id = $this->spot->create($user_id, $name, $long, $lat);

		$this->response(array('spot_id' => $spot_id), 200);
	}

	public function test_get() {
		set_time_limit(0);

		$num_record = 1000000;

		for ($i = 1; $i < $num_record; $i++) {
			$long = rand(1, 200);
			$lat = rand(1, 200);
			$name = 'Test '.$i;
			$this->spot->create(1, $name, $long, $lat);
		}
	}

	/**
	 * Like spot by spot_id
	 */

	public function like_put() {
		$user_id = 1;
		$spot_id = intval($this->put('spot_id'));

		// valid location id
		if ($spot_id == 0) {
			$this->response(array('status' => false, 'error' => 'spot_id is missing or equal 0'), 404);
		}

		if ($this->like->checkLikeExist($user_id, $spot_id)) {
			$this->response(array('status' => false, 'error' => 'spot is liked'), 404);
		}

		$this->like->insert(array('user_id' => $user_id, 'spot_id' => $spot_id));
		$this->spot->updateLikeNumber($spot_id);
		$this->response(array('status' => true), 200);
	}

	public function like_delete() {
		$user_id = 1;
		$spot_id = intval($this->delete('spot_id'));
		// valid location id
		if ($spot_id == 0) {
			$this->response(array('status' => false, 'error' => 'spot_id is missing or equal 0'), 404);
		}

		$this->like->delete_by(array('user_id' => $user_id, 'spot_id' => $spot_id));
		$this->response(array('status' => true), 200);
	}

	public function review_get() {
		$spot_id = array_key_exists('spot_id', $this->get()) ? intval($this->get('spot_id')) : 0;

		if ($spot_id == 0) {
			$this->response(array('status' => false), 404);
		}

		$limit = array_key_exists('limit', $this->get()) ? intval($this->get('limit')) : 10;
		$reviews = $this->review->getReviewsByLocation($spot_id, $limit);
		$this->response(array('status' => true, 'data' => $reviews), 200);
	}

	public function photo_get() {
		$spot_id = array_key_exists('spot_id', $this->get()) ? intval($this->get('spot_id')) : 0;

		if ($spot_id == 0) {
			$this->response(array('status' => false), 404);
		}

		$limit = array_key_exists('limit', $this->get()) ? intval($this->get('limit')) : 10;
		$photos = $this->photo->getPhotosByLocationId($spot_id, $limit);
		$this->response(array('status' => true, 'data' => $photos), 200);
	}

	public function menu_get() {
		$spot_id = array_key_exists('spot_id', $this->get()) ? intval($this->get('spot_id')) : 0;

		if ($spot_id == 0) {
			$this->response(array('status' => false), 404);
		}

		$menu = $this->dish->getMenuByLocationId($spot_id);
		$this->response(array('status' => true, 'data' => $menu), 200);
	}

	public function toplike_get() {
		$limit = array_key_exists('limit', $this->get()) ? intval($this->get('limit')) : 10;

		$data = $this->spot->getTopLike($limit);
		$this->response($data, 200);
	}

	public function topcheckin_get() {
		$limit = array_key_exists('limit', $this->get()) ? intval($this->get('limit')) : 10;

		$data = $this->spot->getTopCheckin($limit);
		$this->response($data, 200);
	}

	public function topnew_get() {
		$limit = array_key_exists('limit', $this->get()) ? intval($this->get('limit')) : 10;

		$data = $this->spot->getTopNew($limit);
		$this->response($data, 200);
	}

	/**
	 * Search location by name or dish name
	 */
	public function search_get() {
		$user_id = 1;
		$require_param = array('name', 'long', 'lat');

		// check require parametter
		foreach ($require_param as $param) {
			if (false == array_key_exists($param, $this->get())) {
				$this->response(array('status' => false, 'error' => $param.' is not exist'), 405);
				return;
			}
		}

		$name = trim($this->get('name'));
		$limit = array_key_exists('limit', $this->get()) ? intval($this->get('limit')) : 10;
		$long = floatval($this->get('long'));
		$lat = floatval($this->get('lat'));

		$search_result = $this->search->searchName($name, $long, $lat, $limit);
		$this->response(array('data' => $search_result), 200);
	}

	/**
	 *
	 * Search location nearby by long/lat
	 */

	public function nearby_get() {

		$user_id = 1;
		$require_param = array('long', 'lat');

		// check require parametter
		foreach ($require_param as $param) {
			if (false == array_key_exists($param, $this->get())) {
				$this->response(array('status' => false, 'error' => $param.' is not exist'), 405);
				return;
			}
		}

		$limit = array_key_exists('limit', $this->get()) ? intval($this->get('limit')) : 10;
		$long = floatval($this->get('long'));
		$lat = floatval($this->get('lat'));
		$search_result = $this->search->nearby($user_id, $long, $lat, $limit);
		$this->response(array('data' => $search_result), 200);
	}

	/**
	 * Checkin location
	 */

	public function checkin_put() {
		$user_id = 1;
		$spot_id = intval($this->put('spot_id'));

		if ($spot_id == 0 || false == $this->spot->checkLocationIdExist($spot_id)) {
			$this->response(array('status' => false), 404);
		}

		$latest_checkin = $this->history->getLatestHistoryFromSpotId($user_id, $spot_id);
		if ($latest_checkin != null) {
			if (strtotime($latest_checkin->created_datetime) + 86400 < time()) {
				$this->spot->updateCheckinNumber($spot_id);
			}
		}

		$this->history->insert(array('user_id' => $user_id, 'spot_id' => $spot_id));
		$this->response(array('status' => true), 200);
	}

	/**
	 * Get location profile by spot_id
	 */

	public function index_get() {
		$user_id = 1;
		$spot_id = array_key_exists('spot_id', $this->get()) ? intval($this->get('spot_id')) : 0;

		if ($spot_id > 0) {
			$info = $this->spot->getLocationInfo($spot_id);
			$latest_history = $this->history->getLatestHistoryFromLocationId($user_id, $spot_id);

			$data = array('status' => true, 'info' => $info, 'latest_history' => $latest_history->created_datetime);

			$this->response($data, 200);
		} else {
			$this->response(array('status' => false), 404);
		}
	}

}
