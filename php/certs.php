<?php

// Certificates
$crtFiles = array(
  '*.blackpinguin.de'      => '/rcl/certs/domain.crt',
  'xmpp.blackpinguin.de'   => '/rcl/certs/xmpp.crt',

  "<a href='https://blackpinguin.de/'>blackpinguin.de</a>"
      => '/rcl/certs/letsencrypt/crt/blackpinguin.de.crt',
  "<a href='https://rcl.blackpinguin.de/'>rcl.blackpinguin.de</a>"
      => '/rcl/certs/letsencrypt/crt/blackpinguin.de/rcl.crt',
  "<a href='https://games.blackpinguin.de/'>games.blackpinguin.de</a>"
      => '/rcl/certs/letsencrypt/crt/blackpinguin.de/games.crt',
  "status.blackpinguin.de"
      => '/rcl/certs/letsencrypt/crt/blackpinguin.de/status.crt',
  "<a href='https://blastwave.blackpinguin.de/'>blastwave.blackpinguin.de</a>"
      => '/rcl/certs/letsencrypt/crt/blackpinguin.de/blastwave.crt',
  "<a href='https://wiki.blackpinguin.de/'>wiki.blackpinguin.de</a>"
      => '/rcl/certs/letsencrypt/crt/blackpinguin.de/wiki.crt',
  "<a href='https://tor.blackpinguin.de/'>tor.blackpinguin.de</a>"
      => '/rcl/certs/letsencrypt/crt/blackpinguin.de/tor.crt',
  "<a href='https://dheap.blackpinguin.de/'>dheap.blackpinguin.de</a>"
      => '/rcl/certs/letsencrypt/crt/blackpinguin.de/dheap.crt',
  "<a href='https://indexer.blackpinguin.de/'>indexer.blackpinguin.de</a>"
      => '/rcl/certs/letsencrypt/crt/blackpinguin.de/indexer.crt',
  "<a href='https://ext.blackpinguin.de/'>ext.blackpinguin.de</a>"
      => '/rcl/certs/letsencrypt/crt/blackpinguin.de/ext.crt',
  "<a href='https://lifeless.blackpinguin.de/'>lifeless.blackpinguin.de</a>"
      => '/rcl/certs/letsencrypt/crt/blackpinguin.de/lifeless.crt',
  "<a href='https://bw2.blackpinguin.de/'>bw2.blackpinguin.de</a>"
      => '/rcl/certs/letsencrypt/crt/blackpinguin.de/bw2.crt',
  "<a href='https://rdb.blackpinguin.de/'>rdb.blackpinguin.de</a>"
      => '/rcl/certs/letsencrypt/crt/blackpinguin.de/rdb.crt',
//  "<a href='https://mcheck.blackpinguin.de/'>mcheck.blackpinguin.de</a>"
//      => '/rcl/certs/letsencrypt/crt/blackpinguin.de/mcheck.crt',
//  "<a href=''>xmpp.blackpinguin.de</a>"
//      => '/rcl/certs/letsencrypt/crt/blackpinguin.de/xmpp.crt',

  'vpn.blackpinguin.de'    => '/rcl/conf/vpn/easy-rsa/keys/server.crt',
//'1000h.vpn.blackpinguin.de'  => '/rcl/conf/vpn/easy-rsa/keys/client_1000h.crt',
// 'vpn2.blackpinguin.de'   => '/svn/vpn/easy-rsa/keys/svn.blackpinguin.de.crt',
// 'rcl.vpn2.blackpinguin.de'   => '/svn/vpn/easy-rsa/keys/robin.svn.blackpinguin.de.crt',
);
if ($_SERVER['HTTP_HOST'] === 'status.localhost') {
    $crtFiles['*.blackpinguin.de'] = '/rcl/www/cert/domain.crt';
    unset($crtFiles['xmpp.blackpinguin.de']);
    unset($crtFiles['vpn.blackpinguin.de']);
    unset($crtFiles['1000h.vpn.blackpinguin.de']);
    unset($crtFiles['vpn2.blackpinguin.de']);
    unset($crtFiles['rcl.vpn2.blackpinguin.de']);
} else if (! in_array($_SERVER['HTTP_HOST'], ['status.server', 'status.blackpinguin.de'])) {
    $crtFiles = [];
}

$cas = array(
  "CAcert Inc." => "https://www.cacert.org/",
  "Let's Encrypt" => "https://letsencrypt.org/",
);
function ca($name){
        global $cas;
        return isset($cas[$name]) ? "<a href='".$cas[$name]."' target='_blank'>$name</a>" : $name;
}



function crtCheck(){
        global $crtFiles;
        $valid = array();
        $cas = array();
        foreach ($crtFiles as $domain => $file) {
                $v = trim(@shell_exec("openssl x509 -noout -dates -in $file | grep 'notAfter' "
                                    . "| grep -o '[^=]*\$' | { read d ; date -d \"\$d\" +%s ; }"));
                $ca = trim(@shell_exec("openssl x509 -noout -issuer -in $file | grep -o '/O=[^/]*/' "
                                     . "| grep -o '[^=/]*/\$' | grep -o '^[^/]*'"));
                $valid[$domain] = $v;
                $cas[$domain] = ca($ca);
        }
        asort($valid);
        foreach ($valid as $domain => $until) {
                $date = date("Y-m-d H:i:s T", $until);
                $ca = $cas[$domain];
                $duration = toDuration(round($until - time()));
		$days = round($until - time()) / (60 * 60 * 24);
                $color = ( $days >= 14 ? "green" : ( $days >= 7 ? "orange" : "red" ) );
                echo "<tr>";
                echo "<td>$domain</td>";
                echo "<td>$ca</td>";
                echo "<td class='$color'>$duration</td>";
                echo "<td>$date</td>";
                echo "</tr>";
        }
}


?>
