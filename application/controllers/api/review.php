<?php

require APPPATH.'/libraries/REST_Controller.php';

class Review extends REST_Controller {

	public function __construct() {
		parent::__construct();

		// load model
		$this->load->model('api/Review_model', 'review');
	}

	public function index_get() {
		$user_id = 1;
		$loc_id = $this->get('location_id');
	}

	public function index_put() {

		$user_id = 1;
		$loc_id = intval($this->put('location_id'));
		$content = $this->put('content');

		if ($loc_id > 0) {
			$data = array('user_id' => $user_id, 'location_id' => $loc_id, 'content' => $content);
			$review_id = $this->review->insert($data);

			$this->response(array('status' => true, 'review_id' => $review_id), 200);
		} else {
			$this->response(array('status' => false), 404);
		}
	}
}
