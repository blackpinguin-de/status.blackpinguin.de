<?php

require_once('config.php');
require_once('util.php');

require_once('problems.php');
require_once('backups.php');
require_once('upgrades.php');
require_once('ips.php');
require_once('certs.php');
require_once('ports.php');
require_once('disks.php');

$runtime = trim(@shell_exec("bash/uptime.py"));
$distro = trim(@shell_exec("cat /etc/os-release | grep 'PRETTY_NAME=\"' | sed -re 's|[^\"]+=\"([^\"]+)\"|\\1|g'"));
$runlevel = trim(@shell_exec("who -r | grep -Po 'run-level \\d ' | grep -Po '\\d'"));
$boottime = toDate(trim(@shell_exec("x=$(LANG='en_US.utf8'; uptime -s) ; date -d \"\$x\" +'%F %T %Z'")));

$now   = time();

load_upgrades();
$apt = "<span class='" . $upg_files['Server']['class'] . "'>" . $upg_files['Server']['date'] . "</span>";

load_backups();
$last_backup = "<span class='" . $bu_files['Server']['class'] . "'>" . $bu_files['Server']['date'] . "</span>";
