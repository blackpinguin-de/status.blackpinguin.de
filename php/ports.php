<?php

// Public Services
$pub = array(
  array('HTTP', 80, 'tcp'),
  array('HTTPS', 443, 'tcp'),
  array('SSH', 22, 'tcp'),
  array('SMTP', 25, 'tcp'),
  array('SMTPS', 465, 'tcp'),
  array('SMTP-MSA', 587, 'tcp'),
  array('POP3S', 995, 'tcp'),
  array('XMPP C2S', 5222, 'tcp'),
  array('XMPP S2S', 5269, 'tcp'),
  array('Tor Dir', 60001, 'tcp'),
  array('Tor OR', 60002, 'tcp'),
#  array('OpenVPN 1', 8080, 'tcp'),
  array('OpenVPN', 8080, 'udp'),
);

// Private Services
$priv = array(
//array('FTP', 21, 'tcp'),
  array('DNS', 53, 'udp'),
  array('MySQL', 3306, 'tcp'),
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
$reachablePorts = trim(@shell_exec("/rcl/www/status/bash/reachable_ports.sh"));

function portCheck($row, $pub) {
        global $openPorts;
        global $reachablePorts;
        list($name, $port, $proto) = $row;
        echo "<tr><td>$name</td><td>$port/$proto</td>";
        $open = preg_match("|:$port +$proto|", $openPorts);
        $reachable = ! $pub || preg_match("@(^|[^0-9])$port/$proto +([a-z]+\|)*open( |\n|\r|\||$)@", $reachablePorts);
        if ($open && $reachable) { echo "<td class='green'>Ok</td>"; }
	elseif ($open && ! $reachable) { echo "<td class='red'>Unreachable</td>"; }
        else { echo "<td class='red'>Error</td>"; }
        echo "</tr>";
}

function publicServices(){
        global $pub;
        foreach ($pub as $row) { portCheck($row, true); }
}

function privateServices(){
        global $priv;
        foreach ($priv as $row) { portCheck($row, false); }
}
