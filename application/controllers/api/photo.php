<?php

class Photo extends REST_Controller {

	public function __construct() {
		parent::__construct();

		$this->load->library('upload');
		$this->load->model('api/Photo_model', 'photo');
		$this->load->helper('thumbnailer');
	}

	/**
	 *
	 * Upload photo
	 *
	 * @param $user_id
	 * @param $location_id
	 * @param $dishes (optional)
	 */

	public function index_post() {

		$user_id = 1;
		$loc_id = intval($this->post('location_id'));

		// valid location id
		if ($loc_id == 0) {
			$this->response(array('status' => false, 'error' => 'loc_id is missing or equal 0'), 404);
		}

		if (!$this->upload->do_upload('photo')) {
			$this->response(array('status' => false, 'error' => $this->upload->display_errors()), 405);
		} else {
			$upload_data = $this->upload->data();
			$file_name = $upload_data['file_name'];
			$file_path = $upload_data['file_path'];

			// create thumbnail picture
			thumbnailer($file_path, $file_name, 300);

			$insert_data = array('user_id' => $user_id, 'location_id' => $loc_id, 'file_name' => $file_name);
			$photo_id = $this->photo->insert($insert_data);

			$dishes = $this->post('dishes') != null ? explode(',', $this->post('dishes')) : array();

			foreach ($dishes as $dish) {
				$this->photo->tagDishWithPhoto($photo_id, $dish);
			}

			$this->response(array('status' => true, 'photo_id' => $photo_id, 'file_name' => $file_name), 200);
		}
	}
}
