<?php

namespace Slg;

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

		$this->initConfig();
	}

	/**
	 * @return void
	 */
	private function initConfig()
	{
		$this->cfg = new Config;
	}
}
