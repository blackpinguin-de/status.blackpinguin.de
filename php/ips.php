<?php

// Map<IP, Hostname>
$hosts = array(
  '192.168.4.1' => 'Router',
  '192.168.4.2' => 'PC',        // d8:cb:8a:a3:fb:37 (eth)  Titan-5960X
  '192.168.4.3' => 'Server',    // e0:cb:4e:06:20:7e (eth)  EB1012
  '192.168.4.4' => 'Laptop',    // 00:15:af:d8:ea:2f (wifi) LADIGES-1000H

  '192.168.4.5' => 'GameCube',  // 00:09:bf:01:c5:03 (eth)
//'192.168.4.6' => 'Wii U',     // ---
  '192.168.4.7' => 'PS4',       // 0c:fe:45:03:59:ac (eth)
//'192.168.4.8' => 'XBoxOne',   // ---
  '192.168.4.13' => 'PC [K]',   // 54:04:a6:f2:03:45 (eth)  LADIGES-250X2
  '192.168.4.14' => 'TV [K]',   // f4:7b:5e:46:17:ae (wifi)
//'192.168.4.17' => 'Server VPN',
  '192.168.4.18' => 'Laptop (vpn)',
  '192.168.4.19' => 'PC [Killer] (vpn)',
);

// Map<Phy, Hostname>
$macs = array(
  '00:1f:1f:34:29:4c' => 'Router',
  '00:e0:4c:68:1f:50' => 'PC [Killer]', // Killer-6400XT
  'd8:cb:8a:a3:fb:37' => 'PC [Titan]',  // Titan-5960X
  'e0:cb:4e:06:20:7e' => 'Server',
  '00:15:af:d8:ea:2f' => 'Laptop (wifi)',
  '00:24:8C:25:B2:79' => 'Laptop (eth)',
  '00:09:bf:01:c5:03' => 'Gamecube',
  '0c:fe:45:03:59:ac' => 'PS4 (eth)',
  '60:5b:b4:03:d1:97' => 'PS4 (wifi)',
  '54:04:a6:f2:03:45' => 'PC [K]',
  'f4:7b:5e:46:17:ae' => 'TV [K]',
);

// IPs that are shown when they are offline
$showAlways = array(
  '192.168.4.1',
//  '192.168.4.2',
  '192.168.4.3',
//  '192.168.4.4',
);

// hide IPs from Internet
$showNever = array(
  '192.168.4.0',
  '192.168.4.15',
  '192.168.4.16',
  '192.168.4.17',
  '192.168.4.31',
);

// Ping Hosts to see if they're online
$online = trim(@shell_exec("/rcl/www/status/bash/hosts_online.sh"));

$current_ip = "?";

function breakDate($str){
        return str_replace("-", "&#8209;", str_replace(" ", "&nbsp;", trim($str)));
}

function breakTime($str){
        return str_replace("-", "&#8209;", str_replace(" CE", "&nbsp;CE", trim($str)));
}

function breakDuration($str){
        return str_replace("d&nbsp;", "d ", str_replace(" ", "&nbsp;", $str));
}

function hostCheck(){
        global $online;
        global $showAlways, $showNever;
        global $hosts, $macs;

        // mark all as offline
        $stati = array();
        foreach($showAlways as $ip) {
                $stati[$ip] = false;
        }
	$stati['192.168.4.3'] = true; // localhost

        // find online hosts
        foreach(explode("\n", $online) as $item) {
                $tmp = explode(",", $item);
                $ip = $tmp[0];
                $mac = (count($tmp) >= 2 ? $tmp[2] : "");
                // mark as online
                $stati[$ip] = true;
                // name from mac
                if(array_key_exists($mac, $macs)){
                    $hosts[$ip] = $macs[$mac];
                }
               // unnamed hosts
                if(! array_key_exists($ip, $hosts)){
                        if((int)(explode(".", $ip)[3]) < 16) {
                                $hosts[$ip] = "# Unnamed #";
                        } else {
                                $hosts[$ip] = "# Unnamed VPN #";
                        }
                }
        }

        // Always Show Laptop / PC
        $laptop = false; $pc = false;
        foreach($stati as $ip => $on) {
            $name = $hosts[$ip];
            if(preg_match("|^Laptop|i", $name) && $on) { $laptop = true; continue; }
            if(preg_match("|^PC( \[[^K\]].*\])?|i", $name) && $on) { $pc = true; continue; }
        }
        if(! $laptop) { $stati['192.168.4.4'] = false; }
        if(! $pc) { $stati['192.168.4.2'] = false; }

        // hide hosts
        foreach($showNever as $ip) {
                unset($stati[$ip]);
        }

        // sort by IP adress
        $ips = array_keys($stati);
        usort($ips, function ($a, $b) {
                $a = (int)(explode('.', $a)[3]);
                $b = (int)(explode('.', $b)[3]);
                return ( $a === $b ? 0 : ( $a < $b ? -1 : 1 ) );
        });

        // table echo
        foreach($ips as $ip) { if($ip !== null) {
                echo "<tr>";
                echo "<td>". $hosts[$ip] ."</td>";
                echo "<td>". $ip ."</td>";
                if($stati[$ip]) { echo "<td class='green'>Online</td>"; }
                else  { echo "<td class='red'>Offline</td>"; }
                echo "</tr>";
        }}

	if (count($ips) < 2) {echo "<!-- $online -->";}
}

function lastIPs(){
        global $mysqli;
        global $current_ip;

        if($mysqli->connect_error){ echo "<tr><td colspan='4'>MySQL connection error</td></tr>"; return; }

        $qr = "
                SELECT von, bis, ip, duration
                  /*, (SELECT COUNT(*) FROM `iplog` b WHERE a.ip = b.ip) as anzahl*/
                FROM iplog a
                WHERE bis >= DATE_SUB(CURDATE(), interval 7 day)
                ORDER BY bis desc;
        ";
        $res = $mysqli->query($qr);
        if($res === FALSE){exit("ERROR: MySQL query error");}
        if($res === FALSE){ echo "<tr><td colspan='4'>MySQL query error</td></tr>"; return; }

        $first = true;
        while($row = $res->fetch_assoc()){
                echo  "<tr";
                if($first){
                        echo " class='green'";
                        $current_ip = $row['ip'];
                }
                echo ">"
                . "<td>".$row['ip']."</td>"
                . "<td>".$row['duration']."</td>"
                . "<td>".breakDate($row['von'])
                ;
                if($row['von'] !== $row['bis']) {
                        echo "&nbsp;&ndash; ".breakDate($row['bis']);
                }
                echo "</td></tr>\n";
                $first = false;
        }
}

function topIPs(){
        global $mysqli;
        global $current_ip;

        if($mysqli->connect_error){ echo "<tr><td colspan='6'>MySQL connection error</td></tr>"; return; }

        $qr = "
                SELECT
                  CONCAT(x.ip >> 24, '.', x.ip >> 16 & 255, '.', ((x.ip >> 8 & 255)>>4)<<4, '.0 /20') ip
                  , COUNT(*) anzahl
                  , COUNT(distinct x.ip) ips
                  , SUM(TIME_TO_SEC(TIMEDIFF(x.bis,x.von))) ord
                  , round_timesec(SUM(TIME_TO_SEC(TIMEDIFF(x.bis,x.von)))) duration
                  , with_timezone(min(x.von)) first
                  , with_timezone(max(x.bis)) last
                FROM iplog_raw x
                GROUP BY (x.ip >> 12)
                ORDER BY ord DESC
        ";
        $res = $mysqli->query($qr);
        if($res === FALSE){ echo("<tr><td colspan='6'>MySQL query error</td></tr>"); return; }

        $first = true;
        while($row = $res->fetch_assoc()){
                echo  "<tr";
                $ip = $row['ip'];
                if(strncmp($current_ip, $ip, 6) == 0){
                        $a = (int)explode('.',$ip)[2];
                        $b = (int)explode('.',$current_ip)[2];
                        if($a >> 4 === $b >> 4){echo " class='green'";}
                }
                echo "><td>" . $row['ip'] . "</td>";
                echo "<td>" . breakDuration($row['duration']) . "</td>";
                echo "<td>" . $row['anzahl'] . "</td>";
                echo "<td>" . $row['ips'] . "</td>";
                echo "<td>" . breakTime($row['first']) . "</td>";
                echo "<td>" . breakTime($row['last']) . "</td></tr>\n";
        }
}


?>
