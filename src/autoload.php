<?php

if (!defined("__INIT_SSHD_LOGIN_GATE")):

	define("__INIT_SSHD_LOGIN_GATE", true);

	if (!isset($_GLOBALS["SSHD_LOGIN_GATE_CONFIG"])) {
		printf("SSHD_LOGIN_GATE_CONFIG is not defined!\n");
		exit(1);
	}

	/**
	 * @param string $class
	 */
	function internalAutoloader($class)
	{
		if (file_exists($f = __DIR__."/classes/".str_replace("\\", "/", $class).".php")) {
			require $f;
		}
	}

	spl_autoload_register("internalAutoloader");

	require __DIR__."/helpers.php";

endif;
