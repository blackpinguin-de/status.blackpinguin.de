<?php

require_once('config.php');

require_once('ips.php');
require_once('certs.php');
require_once('ports.php');
require_once('disks.php');

$runtime = trim(@shell_exec("bash/uptime.py"));
$distro = trim(@shell_exec("cat /etc/os-release | grep 'PRETTY_NAME=\"' | sed -re 's|[^\"]+=\"([^\"]+)\"|\\1|g'"));
$apt = trim(@shell_exec('sec=$(stat -c %Y /var/cache/apt/) ; date -d "@$sec" +"%Y-%m-%d %H:%M:%S %Z"'));
$runlevel = trim(@shell_exec("who -r | grep -Po 'run-level \\d ' | grep -Po '\\d'"));
$boottime = trim(@shell_exec("x=$(LANG='en_US.utf8'; who -b | grep -Po 'boot .*' | grep -Po ' .*' | grep -Po '20\\d\\d-\\d\\d-\\d\\d \\d\d:\\d\\d.*') ; date -d \"\$x GMT+2\" +'%F %T %Z'"));
$last_backup = trim(@shell_exec("cat /rcl/logs/last_backup.txt"));

?>
