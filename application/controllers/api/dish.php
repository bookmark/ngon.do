<?php
class Dish extends REST_Controller {
	public function __construct() {
		parent::__construct();

		//load model
		$this->load->model('api/Dish_model', 'dish');
	}

	/**
	 * Search dish by name
	 */

	public function search_get() {

	}

	public function index_put() {
		$dish_name = trim($this->put('name'));
		$length = strlen($dish_name);

		//valid dish name
		if ($length < 3 || $length > 45) {
			$this->response(array('status' => false, 'error' => 'dish name must be 3->45 chars'), 404);
		}

		//check dish name on db
		if ($this->dish->checkDishExist($dish_name)) {
			$this->response(array('status' => false, 'error' => 'dish name has exist'), 404);
		}

		$dish_id = $this->dish->insert(array('name' => $dish_name));
		$this->response(array('status' => true, 'dish_id' => $dish_id, 'dish_name' => $dish_name), 200);
	}
}
