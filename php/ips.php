<?php

// Map<IP, Hostname>
$hosts = array(
  '192.168.4.1' => 'Router',        // 38:10:d5:b8:7b:b8 (eth)  Fritz!Box 7490
  '192.168.4.2' => 'PC [Titan]',    // 88:88:88:88:87:88 (eth)  Titan-5960X
  '192.168.4.3' => 'Server',        // e0:cb:4e:06:20:7e (eth)  Prime-1700
  '192.168.4.4' => 'Laptop',        // 00:15:af:d8:ea:2f (wifi) e1000h
  '192.168.4.5' => 'GameCube',      // 00:09:bf:01:c5:03 (eth)  GameCube
  '192.168.4.6' => 'Switch',        // 04:03:d6:a5:e6:b4 (wifi) Switch
  '192.168.4.7' => 'PS4 Pro',       // f8:46:1c:d9:f6:89 (eth)  PS4 Pro
//'192.168.4.8' => 'XBoxOne',       // ---
  '192.168.4.12' => 'TV [R]',       // 30:a9:de:50:3e:0e (wifi) OK ODL 326450F-TIB
  '192.168.4.13' => 'TV [K]',       // f4:7b:5e:46:17:ae (wifi) Samsung UE32EH5300
  '192.168.4.14' => 'PC [Pinguin]', // d0:50:99:92:7c:cb (eth)  Pinguin-G1840
//'192.168.4.17' => 'Server VPN',
  '192.168.4.18' => 'Laptop (vpn)',  // (vpn) e1000h
  '192.168.4.19' => 'PC [Killer]',   // (vpn) Killer-6400XT
  '192.168.4.20' => 'PC [Leitwolf]', // (vpn) Leitwolf (Norman)
  '192.168.4.21' => 'PC [IceCube]',  // (vpn) IceCube (Horst)
);

// Map<Phy, Hostname>
$macs = array(
//'00:1f:1f:34:29:4c' => 'Router',         // (eth/wifi) Edimax
  '38:10:d5:b8:7b:b8' => 'Router',         // (eth)  Fritz!Box 7490
  '38:10:d5:b8:7b:ba' => 'Router (wifi)',  // (wifi) Fritz!Box 7490 2.4 GHz
  '38:10:d5:b8:7b:bb' => 'Router (wifi)',  // (wifi) Fritz!Box 7490 5.0 GHz
  '00:e0:4c:68:1f:50' => 'PC [Killer]',    // (eth)  Killer-6400XT
//'d8:cb:8a:a3:fb:37' => 'PC [Titan]',     // (eth)  Titan-5960X (old MoBo)
  '88:88:88:88:87:88' => 'PC [Titan]',     // (eth)  Titan-5960X
  'd0:50:99:92:7c:cb' => 'PC [Pinguin]',   // (eth)  Pinguin-G1840
  '60:45:cb:60:4d:43' => 'Server',         // (eth)  Prime-1700
//'54:04:a6:f2:03:45' => 'PC [K]',         // (eth)  LADIGES-250X2 (K, Trojan)
  'e0:cb:4e:06:20:7e' => 'Server [old]',   // (eth)  EB1012
  '00:15:af:d8:ea:2f' => 'Laptop (wifi)',  // (wifi) EEE1000H
  '00:24:8C:25:B2:79' => 'Laptop (eth)',   // (eth)  EEE1000H
  '00:09:bf:01:c5:03' => 'GameCube',       // (eth)  GameCube
  '04:03:d6:a5:e6:b4' => 'Switch',         // (wifi) Switch
  '0c:fe:45:03:59:ac' => 'PS4 (eth)',      // (eth)  PS4 (Norman)
  '60:5b:b4:03:d1:97' => 'PS4 (wifi)',     // (wifi) PS4 (Norman)
  'f8:46:1c:d9:f6:89' => 'PS4 Pro (eth)',  // (eth)  PS4 Pro
  'e8:9e:b4:a4:8d:17' => 'PS4 Pro (wifi)', // (wifi) PS4 Pro
  '30:a9:de:50:3e:0e' => 'TV [R]',         // (wifi) OK ODL 326450F-TIB
  'f4:7b:5e:46:17:ae' => 'TV [K]',         // (wifi) Samsung UE32EH5300
);

// IPs that are shown when they are offline
$showAlways = array(
  '192.168.4.1',  // Router
  '192.168.4.2',  // Titan-5960X
  '192.168.4.3',  // Prime7
//'192.168.4.4',  // e1000h
//'192.168.4.14', // Pinguin-G1840 (Kristine)
  '192.168.4.19', // (vpn) Killer-6400XT
  '192.168.4.20', // (vpn) Leitwolf (Norman)
//'192.168.4.21', // (vpn) IceCube (Horst)
);

// hide IPs from Internet
$showNever = array(
  '192.168.4.0',  // loopback
  '192.168.4.15', // broadcast
  '192.168.4.16', // loopback
  '192.168.4.17', // (vpn) Prime7
  '192.168.4.31', // broadcast
);

// Ping Hosts to see if they're online
$online = trim(@shell_exec("/rcl/www/status/bash/hosts_online.sh"));

$current_ip = "?";

function hostCheck(){
        global $online;
        global $showAlways, $showNever;
        global $hosts, $macs;
        global $bu_files, $upg_files;

        load_backups();
        load_upgrades();

        // mark all as offline
        $stati = array();
        foreach($showAlways as $ip) {
                $stati[$ip] = false;
        }
        $stati['192.168.4.3'] = true; // localhost

        // find online hosts
        foreach(explode("\n", $online) as $item) {
                $tmp = explode(",", $item);
                $ip = trim($tmp[0]);
                $mac = trim(count($tmp) >= 2 ? $tmp[2] : "");
                if (empty($ip)) { continue; }
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
            //if(preg_match("|^PC$|i", $name) && $on) { $pc = true; continue; }
            //if(preg_match("|^PC \[[^P].*\]|i", $name) && $on) { $pc = true; continue; }
            //if(preg_match("|^PC \[K[^\]].*\]|i", $name) && $on) { $pc = true; continue; }
        }
        if(! $laptop) { $stati['192.168.4.4'] = false; }
        //if(! $pc) { $stati['192.168.4.2'] = false; }

        // hide hosts
        foreach($showNever as $ip) {
                unset($stati[$ip]);
        }

        // sort by IP adress
        $ips = array_keys($stati);
        usort($ips, function ($a, $b) {
                $a = (int)(explode('.', $a)[3]) + (int)(explode('.', $a)[2]) * 1000;
                $b = (int)(explode('.', $b)[3]) + (int)(explode('.', $b)[2]) * 1000;
                return ( $a === $b ? 0 : ( $a < $b ? -1 : 1 ) );
        });

        // table echo
        foreach($ips as $ip) { if($ip !== null) {
                $host = $hosts[$ip];
                $name = preg_replace('@( \((vpn|eth|wifi)\))+$@', '', $host);
                $backup = $bu_files[$name] ?? [];
                $upgrade = $upg_files[$name] ?? [];

                echo "<tr>";
                echo "<td>". $host ."</td>";
                echo "<td>". $ip ."</td>";
                if($stati[$ip]) { echo "<td class='green'>Online</td>"; }
                else  { echo "<td class='red'>Offline</td>"; }

                if ($backup) {
                    $bud = ($backup['date'] ?? '');
                    $buc = ($backup['class'] ?? '');
                    $bua = ($backup['age'] ?? '');
                    echo "<td>$bud</td> <td class='$buc'>$bua</td>";
                } else { echo "<td/><td/>"; }

                if ($upgrade) {
                    $upgd = ($upgrade['date'] ?? '');
                    $upgc = ($upgrade['class'] ?? '');
                    $upga = ($upgrade['age'] ?? '');
                    echo "<td>$upgd</td> <td class='$upgc'>$upga</td>";
                } else { echo "<td/><td/>"; }

                echo "</tr>";
        }}
        $rem = $bu_files['Off-site Mirror'] ?? [];
        $remd = ($rem['date'] ?? '');
        $remc = ($rem['class'] ?? '');
        $rema = ($rem['age'] ?? '');
        echo "<tr> <td>Off-site Mirror</td> <td/> <td/> <td>$remd</td> <td class='$remc'>$rema</td> </tr>";


    if (count($ips) < 2) {echo "<!-- $online -->";}
}

function lastIPs(){
    global $mysqli;
    global $current_ip;

    if($mysqli->connect_error){ echo "<tr><td colspan='4'>MySQL connection error</td></tr>"; return; }

    $qr = "
            SELECT von, bis, ip, duration, offline
              /*, (SELECT COUNT(*) FROM `iplog` b WHERE a.ip = b.ip) as anzahl*/
            FROM iplog a
            WHERE bis >= DATE_SUB(CURDATE(), interval 7 day)
            ORDER BY bis desc;
    ";
    $res = $mysqli->query($qr);
    //if($res === FALSE){ exit("ERROR: MySQL query error"); }
    if($res === FALSE){ echo "<tr><td colspan='4'>MySQL query error</td></tr>"; return; }

    $first = true;
    $one_offline = false;
    $body = "";
    $from = null;
    $to = null;
    
    while($row = $res->fetch_assoc()){
        // long offline period between IPs
        $from = strtotime($row['bis']);
        if ($from && ($dur = $to - $from) >= 30*59) {
			$dur = breakDuration(toDuration($dur, 'h'));
			$body .= "<tr class='red'><td></td><td></td><td>$dur</td><td>";
			$body .= breakDate(date("Y-m-d H:i:s T",$from));
			$body .= "&nbsp;&ndash; ";
			$body .= breakDate(date("Y-m-d H:i:s T",$to));
			$body .= "</td></tr>\n";
			$one_offline = true;
		}
        $to = strtotime($row['von']);
		
		$h = 24 * (int) preg_replace("|^(?:([0-9]+)d )?[0-9]+h [0-9]+m$|","$1",$row['duration']);
		$h += (int) preg_replace("|^(?:[0-9]+d )?([0-9]+)h [0-9]+m$|","$1",$row['duration']);
		$offline = $row['offline'];

        $body .= "<tr";
        if ($first) {
            $body .= " class='green'";
            $current_ip = $row['ip'];
        } else if ($h <= 6 || $offline >= 30) {
			$body .= " class='red'";
		} else if ($h <= 22) {
			$body .= " class='orange'";
		}
        $body .= ">"
        . "<td>" . $row['ip'] . "</td>"
        . "<td>" . $row['duration'] . "</td>"
        ;
        if ($offline >= 30) {
	        $body .= "<td class='red'>" . breakDuration(toDuration($offline, 'h')) . "</td>";
	        $one_offline = true;
		} else {
			$body .= "<td/>";
		}
        $body .= "<td>". breakDate($row['von'])
        ;
        if($row['von'] !== $row['bis']) {
            $body .= "&nbsp;&ndash; ".breakDate($row['bis']);
        }
        $body .= "</td></tr>\n";
        $first = false;
    }
    echo "<tr> <th>IP</th> <th>Duration</th> ".($one_offline ? "<th>Offline</th> " : "<th/>")." <th>Timeframe</th> </tr>";
    echo $body;
}

function topIPs(){
        global $mysqli;
        global $current_ip;

        if($mysqli->connect_error){ echo "<tr><td colspan='6'>MySQL connection error</td></tr>"; return; }

        $qr = "
                SELECT
                  CONCAT(x.ip >> 24 & 255, '.', x.ip >> 16 & 255, '.', ((x.ip >> 8 & 255)>>4)<<4, '.0 /20') ip
                  , COUNT(*) anzahl
                  , COUNT(distinct x.ip) ips
                  , SUM(TIME_TO_SEC(TIMEDIFF(x.bis,x.von))) ord
                  , SUM(TIME_TO_SEC(TIMEDIFF(x.bis,x.von))) duration
                  , with_timezone(min(x.von)) first
                  , with_timezone(max(x.bis)) last
                FROM iplog_raw x
                GROUP BY (x.ip >> 12)
                ORDER BY ord DESC
        ";
        $res = $mysqli->query($qr);
        if($res === FALSE){ echo("<tr><td colspan='6'>MySQL query error</td></tr>"); return; }

        while($row = $res->fetch_assoc()){
                echo  "<tr";
                $ip = $row['ip'];
                if(strncmp($current_ip, $ip, 6) == 0){
                        $a = (int)explode('.',$ip)[2];
                        $b = (int)explode('.',$current_ip)[2];
                        if($a >> 4 === $b >> 4){echo " class='green'";}
                }
                echo "><td>" . $row['ip'] . "</td>";
                echo "<td>" . breakDuration(toDuration($row['duration'])) . "</td>";
                echo "<td>" . $row['anzahl'] . "</td>";
                echo "<td>" . $row['ips'] . "</td>";
                echo "<td>" . breakTime($row['first']) . "</td>";
                echo "<td>" . breakTime($row['last']) . "</td></tr>\n";
        }
}

function ipDowntime(){
	global $mysqli;
    global $current_ip;

    if($mysqli->connect_error){ echo "MySQL connection error"; return; }

    $qr = "
		SELECT
		    first
		  -- , last
		  -- , total as total
		  -- , uptime as uptime
		  , total - uptime as downtime
		  , ROUND(uptime / total * 100, 2) as online
		FROM
        (
        SELECT
            1 as id
          , with_timezone(MIN(von)) first
          -- , with_timezone(MAX(bis)) last
          , UNIX_TIMESTAMP(MAX(bis)) - UNIX_TIMESTAMP(MIN(von)) as total
          , SUM(LEAST(runtime, UNIX_TIMESTAMP(bis) - UNIX_TIMESTAMP(von) )) as uptime
        FROM iplog_raw
        GROUP BY 1
        ) as x
    ";
    $res = $mysqli->query($qr);
    if($res === FALSE){ echo("<tr><td colspan='6'>MySQL query error</td></tr>"); return; }

    while($row = $res->fetch_assoc()){
        echo "Since ".$row['first']." this server was offline for at least <span class='red'>".toDuration($row['downtime'])."</span>, implying it was online <span class='green'>".$row['online']."%</span> of the time.";
        return;
    }
}
