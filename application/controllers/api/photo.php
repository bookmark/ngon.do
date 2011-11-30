<?php

require APPPATH.'/libraries/REST_Controller.php';

class Photo extends REST_Controller {

	public function index_put() {
		var_dump($this->put());
	}
}
