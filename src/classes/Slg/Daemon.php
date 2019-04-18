<?php

namespace Slg;

$__global_config_var_ref = null;
$__global_log_stream = array();

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \Slg
 * @version 0.0.1
 */
final class Daemon
{
	/**
	 * @var \Slg\Config
	 */
	private $slg;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		if (isset($_GLOBALS["SSHD_LOGIN_GATE_CONFIG"]["stdout_log"]) && $_GLOBALS["SSHD_LOGIN_GATE_CONFIG"]["stdout_log"]) {
			$__global_log_stream[] = fopen("php://stdout", "w");
		}

		if (getmyuid() != 0) {
			slg_log("SSHD Login Gate must be run as root!\n");
			exit(1);
		}

		if (!function_exists("pcntl_fork")) {
			slg_log("pcntl extension is required!\n");
			exit(1);
		}

		if (!function_exists("shmop_open")) {
			slg_log("shmop extension is required!\n");
			exit(1);
		}

		slg_log("Initializing config...");
		$this->cfg = new Config;

		slg_log("Config OK!");
		global $__global_config_var_ref;
		$__global_config_var_ref = &$this->cfg;
	}

	/**
	 * @return void
	 */
	public function run()
	{

	}
}
