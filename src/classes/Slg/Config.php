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
		$c = &$GLOBALS["SSHD_LOGIN_GATE_CONFIG"];

		if (!isset($c)) {
			slg_log("SSHD_LOGIN_GATE_CONFIG is not defined!");
			exit(1);
		}

		if (!isset($c["log_dir"])) {
			slg_log("log_dir is not defined!");
			exit(1);
		}

		if (!isset($c["monitor_bin"])) {
			slg_log("monitor_bin is not defined!");
			exit(1);
		}

		if (!isset($c["whois_bin"])) {
			slg_log("whois_bin is not defined!");
			exit(1);
		}
	}

	/**
	 * @throws \Exception
	 * @param string $key
	 * @return & mixed
	 */
	public function &__get($key)
	{
		if (isset($GLOBALS["SSHD_LOGIN_GATE_CONFIG"][$key])) {
			return $GLOBALS["SSHD_LOGIN_GATE_CONFIG"][$key];
		}
		throw new Exception("{$key} is not defined!");
	}
}
