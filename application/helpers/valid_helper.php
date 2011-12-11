<?php

class Valid {
	public static function username($username) {
		return preg_match('/^[A-Za-z][A-Za-z0-9]*(?:_[A-Za-z0-9]+)*$/', $username);
	}

	public static function email($email) {
		if (preg_match("/[a-zA-Z0-9_-.+]+@[a-zA-Z0-9-]+.[a-zA-Z]+/", $email) > 0)
			return true;
		return false;
	}
}
