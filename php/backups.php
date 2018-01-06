<?php

$bu_files = [];

function load_backups() {
global $now;
global $bu_files;

if ($bu_files) { return; }

$dir = '/rcl/logs/backup';

$files = [
  'last.txt' => 'Server',
  'e1000h.txt' => 'Laptop',
  'titan.txt' => 'PC [Titan]',
  'killer.txt' => 'PC [Killer]',
  'last_offsite.txt' => 'Off-site Mirror',
];

$bu_offsite = [
  'last_offsite.txt' => 1,
  'killer.txt' => 1,
];

$bu_times = [
  // on-site
  'yellow'  => strtotime('-4 days', $now),
  'red'     => strtotime('-8 days', $now),

  // off-site
  'yellow2' => strtotime('-16 days', $now),
  'red2'    => strtotime('-30 days', $now),
];

foreach ($files as $file => $name) {
	$date = trim(@file_get_contents("$dir/$file"));
	$time = strtotime($date);
	$age = toDuration($now - $time, 'h');
	if ($time <= 0 || ! $date) { $date = "Unknown"; $age = "Unknown"; }

	$offsite = (empty($bu_offsite[$file]) ? '' : '2');
	$yellow = $bu_times['yellow'. $offsite];
	$red    = $bu_times['red'. $offsite];

	$clss = ($time >= $yellow ? 'green' : ($time >= $red ? 'orange' : 'red'));
	$bu_files[$name] = [ 'name' => $name, 'age' => $age, 'date' => toDate($date), 'class' => $clss, 'file' => $file ];
	#echo "<tr> <td>$name</td> <td class='$clss'>$age</td> <td>$date</td> </tr>\n";
}
}

function last_backups()
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
};
