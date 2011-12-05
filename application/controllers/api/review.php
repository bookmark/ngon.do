<?php

class Review extends REST_Controller {

	public function __construct() {
		parent::__construct();

		// load model
		$this->load->model('api/Review_model', 'review');
	}

	/**
	 *
	 * Get review by review_id
	 */

	public function index_get() {
		$user_id = 1;
		$review_id = intval($this->get('review_id'));

		if ($review_id <= 0 || $this->review->count_by(array('id' => $review_id, 'user_id' => $user_id) == 0)) {
			$this->response(array('status' => false, 'error' => 'Review is not exist or has been deleted'), 404);
		}

		$data = array('status' => true, 'data' => $this->review->get($review_id));
		$this->response($data, 200);
	}

	/**
	 * Create review
	 */
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

	/**
	 * Edit review
	 */

	public function index_post() {
		$user_id = 1;
		$review_id = intval($this->post('review_id'));
		$content = $this->post('content');

		if ($review_id <= 0 || $this->review->count_by(array('id' => $review_id, 'user_id' => $user_id) == 0)) {
			$this->response(array('status' => false, 'error' => 'Review is not exist or has been deleted'), 404);
		}

		//edit review	
		$this->review->update($review_id, array('content' => $content));
		$this->response(array('status' => true, 'review_id' => $review_id), 200);
	}

	/**
	 * Delete review
	 */

	public function index_delete() {
		$user_id = 1;
		$review_id = intval($this->delete('review_id'));

		if ($review_id <= 0 || $this->review->count_by(array('id' => $review_id, 'user_id' => $user_id) == 0)) {
			$this->response(array('status' => false, 'error' => 'Review is not exist or user is not allow delete'), 404);
		}

		$this->review->delete($review_id);
		$this->response(array('status' => true), 200);
	}
}
