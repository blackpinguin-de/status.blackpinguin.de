<?php

require_once('config.php');

require_once('ips.php');
require_once('certs.php');
require_once('ports.php');
require_once('disks.php');

$runtime = trim(@shell_exec("bash/uptime.py"));
$distro = trim(@shell_exec("cat /etc/os-release | grep 'PRETTY_NAME=\"' | sed -re 's|[^\"]+=\"([^\"]+)\"|\\1|g'"));
$runlevel = trim(@shell_exec("who -r | grep -Po 'run-level \\d ' | grep -Po '\\d'"));
$boottime = trim(@shell_exec("x=$(LANG='en_US.utf8'; who -b | grep -Po 'boot .*' | grep -Po ' .*' | grep -Po '20\\d\\d-\\d\\d-\\d\\d \\d\d:\\d\\d.*') ; date -d \"\$x GMT+2\" +'%F %T %Z'"));

$apt = trim(@shell_exec('sec=$(stat -c %Y /var/cache/apt/) ; date -d "@$sec" +"%Y-%m-%d %H:%M:%S %Z"'));
$last_backup = trim(@shell_exec("cat /rcl/logs/last_backup.txt"));
$now   = time();
$aptt  = strtotime('+1 week', strtotime($apt));
$backt = strtotime('+4 days', strtotime($last_backup));
$aptt2  = strtotime('+2 week', strtotime($apt));
$backt2 = strtotime('+8 days', strtotime($last_backup));
$apt = "<span class='".($aptt  < $now ? ($aptt2 < $now ? 'red' : 'orange') : 'green')."'>$apt</span>";
$last_backup = "<span class='".($backt < $now ? ($backt2 < $now ? 'red' : 'orange') : 'green')."'>$last_backup</span>";


?>
