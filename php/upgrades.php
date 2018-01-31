<?php

$upg_files = [];

function load_upgrades() {
global $now;
global $upg_files;

if ($upg_files) { return; }

$dir = '/rcl/logs/upgrade';

$files = [
  'local'        => 'Server',
  'e1000h.txt'   => 'Laptop',
  'titan.txt'    => 'PC [Titan]',
  'killer.txt'   => 'PC [Killer]',
  'pinguin.txt'  => 'PC [Pinguin]',
  'leitwolf.txt' => 'PC [Leitwolf]',
  'icecube.txt'  => 'PC [IceCube]',
];

$upg_offsite = [
  'killer.txt'   => 1,
  'leitwolf.txt' => 1,
];

$upg_windows = [
  'titan.txt'   => 1,
  'pinguin.txt' => 1,
  'icecube.txt' => 1,
];

$upg_times = [
  // on-site
  'yellow'  => strtotime('-1 weeks', $now),
  'red'     => strtotime('-2 weeks', $now),

  // off-site (normal: every two weeks)
  'yellow2' => strtotime('-2 weeks -2 days', $now),
  'red2'    => strtotime('-4 weeks -2 days', $now),

  // windows (every 2nd tuesday of each month)
  'yellow3' => strtotime('-1 month -2 days', $now),
  'red3'    => strtotime('-1 month -1 week', $now),

  // off-site & windows
  'yellow4' => strtotime('-1 month -2 weeks -4 days', $now),
  'red4'    => strtotime('-1 month -5 weeks -2 days', $now),
];

$apt = trim(@shell_exec('a=$(zcat /var/log/apt/history.log.1.gz ; cat /var/log/apt/history.log) ; d=$(echo "$a" | grep "End-Date:" | tail -n 1 | grep -o ":.*" | grep -Eo "[0-9]{4}(-[0-9]{2}){2}  [0-9]{2}(:[0-9]{2}){2}")  ; date -d "$d" +"%Y-%m-%d %H:%M:%S %Z"'));

foreach ($files as $file => $name) {
	$date = ( $file === 'local' ? $apt : trim(@file_get_contents("$dir/$file")) );
	$time = strtotime($date);
	$age = toDuration($now - $time, 'h');
	if ($time <= 0 || ! $date) { $date = "Unknown"; $age = "Unknown"; }

	$offsite = ! empty($upg_offsite[$file]);
	$windows = ! empty($upg_windows[$file]);
	$special = ( $offsite ? ($windows ? '4' : '2') : ($windows ? '3' : '') );
	$yellow = $upg_times['yellow'. $special];
	$red    = $upg_times['red'. $special];

	$clss = ($time >= $yellow ? 'green' : ($time >= $red ? 'orange' : 'red'));
	$upg_files[$name] = [ 'name' => $name, 'age' => $age, 'date' => toDate($date), 'class' => $clss, 'file' => $file ];
	#echo "<tr> <td>$name</td> <td class='$clss'>$age</td> <td>$date</td> </tr>\n";
}
}

/*function last_backups()
{
load_backups();
global $bu_files;
foreach ($bu_files as $name => $x)
    echo "<tr>"
       . " <td>" . $x['name'] . "</td>"
       . " <td class='" . $x['class'] . "'>" . $x['age'] . "</td>"
       . " <td>" . $x['date'] . "</td>"
       . " </tr>\n"
    ;
};*/
