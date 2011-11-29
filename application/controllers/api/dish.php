<?php
require APPPATH.'/libraries/REST_Controller.php';

class Dish extends REST_Controller {
	public function __construct() {
		parent::__construct();

		//load model
		$this->load->model('api/Dish_model', 'dish');
	}
}
