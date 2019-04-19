<?php

namespace Slg;

use Exception;

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
	 * @var int
	 */
	private $monitorParserPid = -1;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		cli_set_process_title("slg: master-daemon");

		$this->cfg = new Config;
		slg_log("Checking required extensions...");
		if (trim(shell_exec("whoami")) !== "root") {
			slg_log("SSHD Login Gate must be run as root!");
			exit(1);
		}

		if (!function_exists("pcntl_fork")) {
			slg_log("pcntl extension is required!");
			exit(1);
		}

		if (!file_exists($this->cfg->monitor_bin)) {
			slg_log("monitor_bin does not exist: %s", $this->cfg->monitor_bin);
			exit(1);
		}

		if (!is_executable($this->cfg->monitor_bin)) {
			slg_log("monitor_bin is not executable: %s", $this->cfg->monitor_bin);
		}

		slg_log("All required extensions are installed!");
	}

	/**
	 * @return void
	 */
	public function run()
	{
		slg_log("Running monitor parser...");
		$this->runMonitorParser();

		$status = null;

		/**
		 * Healt check.
		 */
		while (true) {
			sleep(500);
			// Monitor parser health check.
			if (pcntl_waitpid($this->monitorParserPid, $status, WNOHANG) === -1) {
				slg_log("monitor parser has stopped!");
				slg_log("Trying to respawn monitor parser...");
				$this->runMonitorParser();
				sleep(3);
				if (pcntl_waitpid($this->monitorParserPid, $status, WNOHANG) === -1) {
					slg_log("An error occured!");
					slg_log("Aborting...");
					goto abort_all;
				}
				slg_log("monitor parser is now running...");
			} else {
				slg_log("monitor parser is still running...");
			}
		}

abort_all:

		if (pcntl_waitpid($this->monitorParserPid, $status, WNOHANG) !== -1) {
			shell_exec(sprintf("/bin/kill -9 %d", $this->monitorParserPid));
		}
		slg_log("Aborted!");
		exit(1);
	}

	/**
	 * @return void
	 */
	private function runMonitorParser()
	{
		if (!($pid = pcntl_fork())) {

			pcntl_signal(SIGCHLD, SIG_IGN);

			cli_set_process_title("slg: monitor-parser");
			$fd = array(
				array("pipe", "r"),
				array("pipe", "w"),
				array("pipe", "w")
			);
			$res = proc_open("exec {$this->cfg->monitor_bin} -xf", $fd, $pipes);
			while (!feof($pipes[1])) {
				$line = fgets($pipes[1]);
				if (preg_match("/(?:sshd\[)(\d+)(?:\]\:)(?: Accepted (.+) for )(\S+)(?: from )(\S+)(?: port )(\d+)(?: ssh)/USi", $line, $m)) {
					slg_log("Detected ssh login!");
					$this->notify((int)$m[1], $m[2], $m[3], $m[4], (int)$m[5]);
				}
			}
			foreach ($pipes as $pipe) {
				fclose($pipe);
			}
			proc_close($res);
		}
		$this->monitorParserPid = $pid;
	}

	/**
	 * @return void
	 */
	private function notify($pid, $authMethod, $username, $ipAddress, $sourcePort)
	{
		if (!pcntl_fork()) {
			cli_set_process_title("slg: telegram-notifier");
			slg_log("Sending notification to telegram...");
			$r1 = array("{ip}", "{source_port}", "{username}", "{auth_method}", "{time}", "{pid}", "{whois_ip}");
			$r2 = array($ipAddress, $sourcePort, $username, $authMethod, date("Y-m-d H:i:s"), $pid, $this->whoisIp($ipAddress));
			$ch = curl_init("https://api.telegram.org/bot{$this->cfg->login_notification["telegram"]["bot_token"]}/sendMessage");
			curl_setopt_array($ch, array(
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_SSL_VERIFYHOST => false,
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => http_build_query(array(
					"chat_id" => $this->cfg->login_notification["telegram"]["chat_id"],
					"text" => str_replace($r1, $r2, $this->cfg->login_notification["telegram"]["format"]),
					"parse_mode" => "HTML"
				))
			));
			$out = curl_exec($ch);
			curl_close($ch);
			print $out;
			print http_build_query(array(
				"chat_id" => $this->cfg->login_notification["telegram"]["chat_id"],
				"text" => str_replace($r1, $r2, $this->cfg->login_notification["telegram"]["format"])
			));
			exit(0);
		}
	}

	/**
	 * @param string $ipAddress
	 * @return string
	 */
	private function whoisIp($ipAddress)
	{
		$ipAddress = escapeshellarg($ipAddress);
		$a = shell_exec("{$this->cfg->whois_bin} {$ipAddress} 2>&1");
		$str = "";
		preg_match("/inetnum: .+/i", $a, $m) and $str .= trim($m[0])."\n";
		preg_match("/cidr: .+/i", $a, $m) and $str .= trim($m[0])."\n";
		preg_match("/netname: .+/i", $a, $m) and $str .= trim($m[0])."\n";
		preg_match("/country: .+/i", $a, $m) and $str .= trim($m[0])."\n";
		if (preg_match("/(descr: .+)\n\w+\:/Usi", $a, $m)) {
			$str .= $m[1]."\n";
		}
		preg_match("/orgname: .+/i", $a, $m) and $str .= trim($m[0])."\n";
		preg_match("/address: .+/i", $a, $m) and $str .= trim($m[0])."\n";
		return "<pre>".htmlspecialchars($str)."</pre>";
	}
}
