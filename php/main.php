<?php

require_once('config.php');
require_once('util.php');

require_once('problems.php');
require_once('ips.php');
require_once('certs.php');
require_once('ports.php');
require_once('disks.php');
require_once('backups.php');

$runtime = trim(@shell_exec("bash/uptime.py"));
$distro = trim(@shell_exec("cat /etc/os-release | grep 'PRETTY_NAME=\"' | sed -re 's|[^\"]+=\"([^\"]+)\"|\\1|g'"));
$runlevel = trim(@shell_exec("who -r | grep -Po 'run-level \\d ' | grep -Po '\\d'"));
$boottime = trim(@shell_exec("x=$(LANG='en_US.utf8'; uptime -s) ; date -d \"\$x\" +'%F %T %Z'"));

$now   = time();

$apt = trim(@shell_exec('sec=$(stat -c %Y /var/cache/apt/) ; date -d "@$sec" +"%Y-%m-%d %H:%M:%S %Z"'));
$aptt  = strtotime($apt);
$aptt1 = strtotime('+1 week', $aptt); // yellow after
$aptt2 = strtotime('+2 week', $aptt); // red after
$aptc  = ( $aptt1 < $now ? ( $aptt2 < $now ? 'red' : 'orange' ) : 'green' );
$apt = "<span class='$aptc'>$apt</span>";

load_backups();
$last_backup = "<span class='" . $bu_files['last.txt']['class'] . "'>" . $bu_files['last.txt']['date'] . "</span>";
