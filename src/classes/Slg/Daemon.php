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

		if (!function_exists("pcntl_fork")) {
			printf("pcntl extension required!\n");
			exit(1);
		}

		if (!function_exists("shmop_open")) {
			printf("shmop extension required!\n");
			exit(1);
		}

		slg_log("Initializing config...");
		$this->cfg = new Config;

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
