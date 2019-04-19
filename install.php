<?php

if (trim(shell_exec("whoami")) !== "root") {
	slg_log("Must be run as root!");
	exit(1);
}


print "Enter bot token: ";
$token = trim(fread(STDIN, 1024));

print "Enter telegram chat id: ";
$chatId = trim(fread(STDIN, 1024));
$config = str_replace(
	array("{{bot_token}}", "{{chat_id}}"),
	array($token, $chatId),
	file_get_contents(__DIR__."/config.php.example")
);

print shell_exec("/bin/cp -rfv ".escapeshellarg(__DIR__)." /opt/slg");
print shell_exec("/bin/cp -fv /opt/slg/slg.service /etc/systemd/system");
file_put_contents("/opt/slg/config.php", $config);
print "\n\nPlease run `sudo systemctl start slg` to start the service.\n";
print "\n\nYou can edit the config at /opt/slg/config.php\n\n";
