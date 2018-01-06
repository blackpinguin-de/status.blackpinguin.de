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
  'leitwolf.txt' => 'PC [Leitwolf]',
];

$upg_offsite = [
  'killer.txt'   => 1,
  'leitwolf.txt' => 1,
];

$upg_times = [
  // on-site
  'yellow'  => strtotime('-1 weeks', $now),
  'red'     => strtotime('-2 weeks', $now),

  // off-site
  'yellow2' => strtotime('-16 days', $now),
  'red2'    => strtotime('-30 days', $now),
];

$apt = trim(@shell_exec('sec=$(stat -c %Y /var/cache/apt/) ; date -d "@$sec" +"%Y-%m-%d %H:%M:%S %Z"'));

foreach ($files as $file => $name) {
	$date = ( $file === 'local' ? $apt : trim(@file_get_contents("$dir/$file")) );
	$time = strtotime($date);
	$age = toDuration($now - $time, 'h');
	if ($time <= 0 || ! $date) { $date = "Unknown"; $age = "Unknown"; }

	$offsite = (empty($upg_offsite[$file]) ? '' : '2');
	$yellow = $upg_times['yellow'. $offsite];
	$red    = $upg_times['red'. $offsite];

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
