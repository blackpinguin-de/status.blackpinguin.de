<?php

function diskBars(){
//	$diskspace = trim(@shell_exec("df | grep '^/dev/' | sed -re 's|.* ([0-9]+) +([0-9]+) +[0-9]+ +[0-9]+% +(/.*)|\\1 \\2 \\3|g'"));
//	$memory = trim(@shell_exec("cat /proc/meminfo | grep 'Mem' | sed -re 's|Mem(.+): +([0-9]+) kB|\\1 \\2|g'"));
//	$swap = trim(@shell_exec("cat /proc/meminfo | grep 'Swap[TF]' | sed -re 's|Swap(.+): +([0-9]+) kB|\\1 \\2|g'"));
	$diskspace = trim(@shell_exec("/rcl/www/status/bash/disks_hdd.sh"));
	$memory = trim(@shell_exec("/rcl/www/status/bash/disks_memory.sh"));
//	$swap = trim(@shell_exec("/rcl/www/status/bash/disks_swap.sh"));

	$data = array();
	$data["/backup"] = array(72247368, 0.0, 0.0, false);
	$data["/ext/media"] = array(433491744, 0.0, 0.0, false);
	$data["/ext/images"] = array(1056894132, 0.0, 0.0, false);
	$data["/ext/encrypted"] = array(186623424, 0.0, 0.0, false);
	$data["/ext/backup"] = array(252687936, 0.0, 0.0, false);

	$lines = explode("\n",$diskspace);
	foreach($lines as $line){
		$x = explode(' ', $line);
		$size = (int)$x[0];
		$used = (int)$x[1];
		$name = $x[2];
		$perc = $used / $size * 100.0;
		$data[$name] = array($size, $used, $perc, true);
	}
	$max = max(array_map(function($x){return $x[0];}, $data));

	ksort($data);

	$data["swap"] = array(0, 0, 0, true);
	$data["memory"] = array(0, 0, 0, true);

	foreach(explode("\n", $memory) as $line){
		$x = explode(" ", $line);
		if($x[0] === "M"){ $data["memory"][0] = (int) $x[1]; }
		else if($x[0] === "b"){ $data["memory"][2] = (int) $x[2]; }
		else if($x[0] === "S"){ $data["swap"][0] = (int) $x[1]; $data["swap"][2] = (int) $x[3]; }
	}

	$data["swap"][1] = ($data["swap"][0] - $data["swap"][2]);
	$data["memory"][1] = ($data["memory"][0] - $data["memory"][2]);
	$data["swap"][2] = $data["swap"][1] / $data["swap"][0] * 100.0;
	$data["memory"][2] = $data["memory"][1] / $data["memory"][0] * 100.0;

	echo "<div class='disk-img'>";
	foreach($data as $name => $x){
		$size = $x[0] / $max * 100.0;
		if($x[3]){
			$used = round($x[1]/1048576, 2);
			$total = round($x[0]/1048576, 2);
			$free = round(($x[0]-$x[1])/1048576, 2);
			$title = "For '$name' $used GiB from a total of $total GiB is in use (".round($x[2],2)."%), which leaves $free GiB free (".round(100.0 - $x[2],2)."%).";
			echo "<div style='width:$size%' title=\"$title\">";
			echo "<div style='width:". $x[2] ."%;'>";
			echo "<span>$name<span>";
			echo "$used of $total";
			echo " GiB</span></span></div></div>";
		}
		else{
			$total = round($x[0]/1048576, 2);
			$title = "The device '$name' is currently not mounted. It has a total size of $total GiB.";
			echo "<div style='width:$size%' class='unmounted' title=\"$title\">";
			echo "<span>$name<span>$total";
			echo " GiB - not mounted</span></span></div>";
		}
	}
	echo "</div>";
}

?>
