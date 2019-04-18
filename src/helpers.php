<?php

if (!defined("__SLG_HELPERS")):
	define("__SLG_HELPERS", true);

	/**
	 * @param string $format
	 * @param ...	 $vargs
	 * @return void
	 */
	function slg_log($format)
	{
		global $__global_log_stream;
		if (is_array($__global_log_stream)) {
			$vargs = func_get_args();
			foreach ($__global_log_stream as $stream) {
				if (is_resource($stream)) {
					fprintf($stream, "[%s] %s\n", date("Y-m-d H:i:s"), vsprintf($format, $vargs));
				}
			}
		}
	}

endif;
