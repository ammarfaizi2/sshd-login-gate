<?php

namespace Slg;

use Exception;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \Slg
 * @version 0.0.1
 */
final class Config
{
	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$c = &$_GLOBALS["SSHD_LOGIN_GATE_CONFIG"];

		if (!isset($c)) {
			printf("SSHD_LOGIN_GATE_CONFIG is not defined!\n");
			exit(1);
		}

		if (!isset($c)) {
			# code...
		}
	}

	/**
	 * @throws \Exception
	 * @param string $key
	 * @return & mixed
	 */
	public function &__get($key)
	{
		if (isset($_GLOBALS["SSHD_LOGIN_GATE_CONFIG"][$key])) {
			return $_GLOBALS["SSHD_LOGIN_GATE_CONFIG"][$key];
		}
		throw new Exception("{$key} is not defined!\n");
	}
}
