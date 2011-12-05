<?php

require APPPATH.'/libraries/REST_Controller.php';

class Location extends REST_Controller {

	public function __construct() {
		parent::__construct();

		// load model
		$this->load->model('api/Photo_model', 'photo');
		$this->load->model('api/Location_model', 'location');
		$this->load->model('api/Dish_model', 'dish');
		$this->load->model('api/History_model', 'history');
		$this->load->model('api/Review_model', 'review');
		$this->load->model('api/Search_model', 'search');
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
		$name = $this->put('name');
		$location_id = $this->location->create($user_id, $name, $long, $lat);

		$this->response(array('location_id' => $location_id), 200);
	}

	public function test_get() {
		set_time_limit(0);

		$num_record = 1000000;

		for ($i = 1; $i < $num_record; $i++) {
			$long = rand(1, 200);
			$lat = rand(1,200);
			$name = 'Test '.$i;
			$this->location->create(1, $name, $long, $lat);
		}
	}

	public function review_get() {
		$loc_id = intval($this->get('location_id'));
		$limit = $this->get('limit');
		$limit = $limit == null ? 10 : intval($limit);

		if ($loc_id == 0) {
			$this->response(array('status' => false), 404);
		}

		$reviews = $this->review->getReviewsByLocation($loc_id, 5);
		$this->response(array('status' => true, 'data' => $reviews), 200);
	}

	public function photo_get() {
		$loc_id = intval($this->get('location_id'));
		$limit = $this->get('limit');
		$limit = $limit == null ? 10 : intval($limit);

		if ($loc_id == 0) {
			$this->response(array('status' => false), 404);
		}

		$photos = $this->photo->getPhotosByLocationId($loc_id, 10);
		$this->response(array('status' => true, 'data' => $photos), 200);
	}

	public function menu_get() {
		$loc_id = intval($this->get('location_id'));

		if ($loc_id == 0) {
			$this->response(array('status' => false), 404);
		}

		$menu = $this->dish->getMenuByLocationId($loc_id);
		$this->response(array('status' => true, 'data' => $menu), 200);
	}

	public function toplike_get() {
		$limit = $this->get('limit');
		$limit = $limit == null ? 10 : intval($limit);

		$data = $this->location->getTopLike($limit);
		$this->response($data, 200);
	}

	public function topcheckin_get() {
		$limit = $this->get('limit');
		$limit = $limit == null ? 10 : intval($limit);

		$data = $this->location->getTopCheckin($limit);
		$this->response($data, 200);
	}

	public function topnew_get() {
		$limit = $this->get('limit');
		$limit = $limit == null ? 10 : intval($limit);

		$data = $this->location->getTopNew($limit);
		$this->response($data, 200);
	}

	/**
	 * Search location by name or dish name
	 */
	public function search_get() {
		$user_id = 1;
		$name = $this->get('name');

	}

	/**
	 *
	 * Search location nearby by long/lat
	 */

	public function nearby_get() {

		$name = $this->get('name');
		$user_id = 1;
		$limit = 10;
		$long = $this->get('long');
		$lat = $this->get('lat');

		$search_result = $this->search->searchNameOrDish($name, $user_id, $long, $lat, $limit);

		$this->response($search_result, 200);
	}

	/**
	 * Checkin location
	 */

	public function checkin_put() {
		$user_id = 1;
		$loc_id = intval($this->put('location_id'));

		if ($loc_id == 0 || $this->location->checkLocationIdExist($loc_id)) {
			$this->response(array('status' => false), 404);
		}

		$this->history->insert(array('user_id' => $user_id, 'location_id' => $loc_id));
		$this->location->updateCheckinNumber($loc_id);
		$this->response(array('status' => true), 200);
	}

	/**
	 * Get location profile by location_id
	 */

	public function profile_get() {
		$user_id = 1;
		$loc_id = intval($this->get('loc_id'));

		if ($loc_id > 0) {
			$info = $this->location->getLocationInfo($loc_id);
			$latest_history = $this->history->getLatestHistoryFromLocationId($user_id, $loc_id);

			$data = array('status' => true, 'info' => $info, 'latest_history' => $latest_history->created_datetime);

			$this->response($data, 200);
		} else {
			$this->response(array('status' => false), 404);
		}
	}

}
