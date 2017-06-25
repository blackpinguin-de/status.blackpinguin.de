<?php

// Public Services
$pub = array(
  array('HTTP', 80, 'tcp'),
  array('HTTPS', 443, 'tcp'),
  array('SSH', 22, 'tcp'),
  array('SMTP', 25, 'tcp'),
  array('SMTP-MSA', 587, 'tcp'),
  array('POP3S', 995, 'tcp'),
  array('XMPP C2S', 5222, 'tcp'),
  array('XMPP S2S', 5269, 'tcp'),
  array('Tor Dir', 60001, 'tcp'),
  array('Tor OR', 60002, 'tcp'),
  array('OpenVPN 1', 8080, 'tcp'),
  array('OpenVPN 2', 8080, 'udp'),
);

// Private Services
$priv = array(
//array('FTP', 21, 'tcp'),
  array('DNS', 53, 'udp'),
  array('MySQL', 3306, 'tcp'),
//array('SMTPS', 465, 'tcp'),
  array('Samba', 445, 'tcp'),
  array('Cups', 631, 'tcp'),
//array('Sane', 6566, 'tcp'),
//array('Openfire', 9091, 'tcp'),
  array('SVN', 3690, 'tcp'),
  array('UPnP', 1900, 'udp'),
//array('MiniDLNA', 8200, 'tcp'),
  array('BOINC', 31416, 'tcp'),
);

// Services that are currently running
$openPorts = trim(@shell_exec("/rcl/www/status/bash/open_ports.sh"));

function portCheck($row){
        global $openPorts;
        echo "<tr><td>".$row[0]."</td><td>".$row[1]."/".$row[2]."</td>";
        $status = preg_match("|:".$row[1]." ".$row[2]."|", $openPorts);
        if($status) { echo "<td class='green'>Ok</td>"; }
        else { echo "<td class='red'>Error</td>"; }
        echo "</tr>";
}

function publicServices(){
        global $pub;
        foreach($pub as $row){ portCheck($row); }
}

function privateServices(){
        global $priv;
        foreach($priv as $row){ portCheck($row); }
}

