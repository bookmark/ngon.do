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
	}

	/**
	 *
	 * Create location
	 */

	public function index_put() {

	}

	/**
	 *
	 * Search location nearby by long/lat
	 */

	public function nearby_get() {

	}

	/**
	 * Checkin location
	 */

	public function checkin_put() {

	}

	/**
	 * Get location profile by location_id
	 */

	public function profile_get() {
		$user_id = 1;
		$loc_id = intval($this->get('loc_id'));

		if ($loc_id > 0) {
			$info = $this->location->getLocationInfo($loc_id);
			$photos = $this->photo->getPhotosByLocationId($loc_id, 10); //limit 10 photos
			$menu = $this->dish->getMenuByLocationId($loc_id);
			$reviews = $this->review->getReviewsByLocation($loc_id, 5);
			$latest_history = $this->history->getLatestHistoryFromLocationId($user_id, $loc_id);

			$data = array('status' => true, 'info' => $info, 'photos' => $photos, 'menu' => $menu, 'reviews' => $reviews, 'latest_history' => $latest_history->created_datetime);

			$this->response($data, 200);
		} else {
			$this->response(array('status' => false), 404);
		}
	}

}
