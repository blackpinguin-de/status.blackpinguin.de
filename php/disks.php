<?php

function diskColor($p) {
	//$max = 255.0; // 0xff
	$max = 128.0; // 0x80
    $r = dechex(max(0, min($max, round((2.0 - $p * 2.0) * $max))));
    $g = dechex(max(0, min($max, round($p * 2 * $max))));
    if (strlen($r) === 1) { $r = "0" . $r; }
    if (strlen($g) === 1) { $g = "0" . $g; }
    return "#" . $r . $g . "00";
}

function diskBars(){
//  $diskspace = trim(@shell_exec("df | grep '^/dev/' | sed -re 's|.* ([0-9]+) +([0-9]+) +[0-9]+ +[0-9]+% +(/.*)|\\1 \\2 \\3|g'"));
//  $memory = trim(@shell_exec("cat /proc/meminfo | grep 'Mem' | sed -re 's|Mem(.+): +([0-9]+) kB|\\1 \\2|g'"));
//  $swap = trim(@shell_exec("cat /proc/meminfo | grep 'Swap[TF]' | sed -re 's|Swap(.+): +([0-9]+) kB|\\1 \\2|g'"));
    $diskspace = trim(@shell_exec("/rcl/www/status/bash/disks_hdd.sh"));
    $memory = trim(@shell_exec("/rcl/www/status/bash/disks_memory.sh"));
//  $swap = trim(@shell_exec("/rcl/www/status/bash/disks_swap.sh"));
    $lvm_free = trim(@shell_exec("sudo -u root /sbin/vgdisplay --units B | grep -E '(VG Size|Alloc PE)' | grep -Eo '[0-9]+ B' | grep -Eo '[0-9]+'"));

    $load_disks = function ($file) {
      $lines = explode("\n", trim($file));
      foreach($lines as $line){
        $x = explode(' ', trim($line));
        $size = (int) $x[0];
        $used = (int) $x[1];
        $name = $x[2];
        $perc = $used / $size * 100.0;
        yield $name => [ $size, $used, $perc, true ];
      }
    };

    $data = [];

    if (in_array($_SERVER['HTTP_HOST'], ['status.server', 'status.blackpinguin.de'])) {
        // allways show
        $data["/ext/media"]     = [  433491744, 0.0, 0.0, false ];
        $data["/ext/images"]    = [ 1056894132, 0.0, 0.0, false ];
        $data["/ext/encrypted"] = [  186623424, 0.0, 0.0, false ];
        $data["/ext/backup"]    = [  252687936, 0.0, 0.0, false ];

        $data["/hdd/backup"]        = [  308587072, 0.0, 0.0, false ];
        $data["/hdd/encrypted"]     = [  102687416, 0.0, 0.0, false ];
        $data["/hdd/images"]        = [  515010816, 0.0, 0.0, false ];
        $data["/hdd/media/audio"]   = [  102949816, 0.0, 0.0, false ];
        $data["/hdd/media/picture"] = [   51343840, 0.0, 0.0, false ];
        $data["/hdd/media/video"]   = [ 2112729008, 0.0, 0.0, false ];
    }

    $lines = explode("\n",$diskspace);
    foreach($load_disks($diskspace) as $name => $d) {
      $data[$name] = $d;
    }

    ksort($data);

    // unmounted disks
    $unmounted = [];
    foreach ($data as $name => $d) { if (! $d[3]) { $unmounted[$name] = true; }; }
    if ($unmounted) {
      $old_disks = file_get_contents("/rcl/www/status/bash/disks_hdd2.txt");
      foreach($load_disks($old_disks) as $name => $d) {
        if (isset($unmounted[$name])) {
          $d[3] = false;
          $data[$name] = $d;
        }
      }
    }

    // Swap / Memory
    $data["swap"] = array(0, 0, 0, true);
    $data["memory"] = array(0, 0, null, true);

    foreach(explode("\n", $memory) as $line){
        $x = explode(" ", $line);
        if ($x[0] === "M") {
            $data["memory"][0] = (int) $x[1];
            $data["memory"][1] = (int) $x[2];
        }
        else if($x[0] === "b"){ $data["memory"][2] = (int) $x[2]; }
        else if($x[0] === "S"){ $data["swap"][0] = (int) $x[1]; $data["swap"][2] = (int) $x[3]; }
    }

    $data["swap"][1] = ($data["swap"][0] - $data["swap"][2]);
    if ($data["memory"][2] !== null) { $data["memory"][1] = ($data["memory"][0] - $data["memory"][2]); }
    $data["swap"][2] = $data["swap"][1] / $data["swap"][0] * 100.0;
    $data["memory"][2] = $data["memory"][1] / $data["memory"][0] * 100.0;

    // LVM
    $data["lvm"] = [0, 0, 100.0, false];
    foreach (explode("\n", $lvm_free) as $i => $line) {
        $data["lvm"][0] += ((int) $line)  / 1024.0 * ($i % 2 === 0 ? 1 : -1);
        $data["lvm"][1] += ($i %2 === 0 ? ((int) $line)  / 1024.0 : 0);
    }
    $data["lvm"][2] = $data["lvm"][0] / $data["lvm"][1] * 100.0;

    // Output
    $max = max(array_map(function($x){return $x[0];}, $data));
    echo "<div class='disk-img'>";
    foreach($data as $name => $x){
        $size  = $x[0] / $max * 100.0;
        $used  = round($x[1] / 1048576, 2);
        $total = round($x[0] / 1048576, 2);
        $free  = round(($x[0] - $x[1]) / 1048576, 2);
        // mounted?
        if ($x[3]) {
            $title = "For '$name' $used GiB from a total of $total GiB is in use ("
		. round($x[2], 2)
                . "%), which leaves $free GiB free ("
                . round(100.0 - $x[2], 2)."%)."
            ;
            $color = diskColor(4.0 - $x[2] * 0.04);
            echo "<div style='width:$size%; background-color: $color;' title=\"$title\">";
            echo "<div style='width:". $x[2] ."%;'>";
            echo "<span>$name<span>";
            echo "$used of $total";
            echo " GiB</span></span></div></div>";
        }
        // unmounted
        elseif ($name !== "lvm") {
            $title = "The device '$name' is currently not mounted. It has a total size of $total GiB";
            // with data from last mount
            if ($x[2] !== 0) {
                $title .= " and used $used GiB (" . round($x[2], 2) . "%) the last time it was mounted, "
                  . "which leaved $free GiB free (" . round(100.0 - $x[2], 2) . "%)"
                ;
                echo "<div style='width:$size%' class='unmounted known' title=\"$title.\">";
                echo "<div style='width:". $x[2] ."%;'>";
                echo "<span>$name<span>$used of $total";
                echo " GiB - not mounted</span></span></div></div>";
            } else {
                echo "<div style='width:$size%' class='unmounted' title=\"$title.\">";
                echo "<span>$name<span>$total";
                echo " GiB - not mounted</span></span></div>";
            }
        }
        elseif ($name === "lvm") {
            $title = "There is currently $total GiB (" . round($x[2], 2) . "%) unallocated space for LVM";
            echo "<div style='width:$size%' class='lvm' title=\"$title.\">";
            echo "<span>$name<span>$total";
            echo " GiB - not allocated</span></span></div>";
        }
    }
    echo "</div>";
}

