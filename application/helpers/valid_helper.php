<?php

class Valid
{
	public static function username ($username)
	{
		return preg_match('/^[A-Za-z][A-Za-z0-9]*(?:_[A-Za-z0-9]+)*$/', $username);
	}
}
