<?php

require APPPATH . '/libraries/REST_Controller.php';

class History extends REST_Controller
{
	public function __construct ()
	{
		parent::__construct();

		//load model
		$this->load->model('api/History_model', 'history');
	}

	/**
	 *
	 * Load history by user_id
	 */

	public function index_get ()
	{
		$user_id = 1;

		$this->response($this->history->get_many_by(array('user_id' => $user_id)));
	}

	public function index_put ()
	{
		$user_id = 1;
		$location_id = $this->put('location_id');

		$history_id = $this->history->insert(array('user_id' => $user_id, 'location_id' => $location_id));

		$this->response(array('history_id' => $history_id));
	}

}
