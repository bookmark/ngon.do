<?php

class Utils
{

	public static function createSalt ($length)
	{
		return substr(md5(uniqid() * rand(0, 1000)), 0, $length);
	}

	public static function convertVietnamesePhoneNumber ($old_phone)
	{
		$phone_number = '';
		$length = strlen($old_phone);
		$prefix = substr($old_phone, 0, 2);

		if (false == is_numeric($phone_number) || $length < 10 || $length > 12 || false == in_array($prefix, array('09', '84'))) {
			return $phone_number;
		}

		if ($prefix == '09') {
			$phone_number = '84' . substr($old_phone, 1);
		}

		return $phone_number;
	}
}
