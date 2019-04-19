<?php

if (trim(shell_exec("whoami")) !== "root") {
	slg_log("Must be run as root!");
	exit(1);
}

print shell_exec("/bin/cp -rfv ".escapeshellarg(__DIR__)." /opt/slg");
print shell_exec("/bin/ln -sfv /opt/slg/slg.service /etc/systemd/system");
print "\nPlease run `systemctl start slg` to start the service.\n";
