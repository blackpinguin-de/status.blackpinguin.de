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
  "mail.blackpinguin.de"   => '/rcl/certs/letsencrypt/crt/blackpinguin.de/mail.crt',

  'vpn.blackpinguin.de'  => '/rcl/certs/openvpn/vpn.bp.de/keys/vpn.blackpinguin.de.crt',
  'VPN: Laptop'          => '/rcl/certs/openvpn/vpn.bp.de/keys/e1000h.vpn.blackpinguin.de.crt',
  'VPN: PC'              => '/rcl/certs/openvpn/vpn.bp.de/keys/leitwolf.vpn.blackpinguin.de.crt',
  'VPN: Off-site Backup' => '/rcl/certs/openvpn/vpn.bp.de/keys/killer.vpn.blackpinguin.de.crt',

  // S/MIME
  "E-Mail: <a href='//ext.blackpinguin.de/certs/rclbp.smime'>rcl@bp.de</a>"  => '/rcl/www/ext/certs/rclbp.smime',
  "E-Mail: <a href='//ext.blackpinguin.de/certs/rlweb.smime'>r.l@web.de</a>" => '/rcl/www/ext/certs/rlweb.smime',

  // GPG
  "E-Mail: <a href='//ext.blackpinguin.de/certs/rclbp.pgp'>rcl@bp.de</a>"    => '/rcl/www/ext/certs/rclbp.pgp',
  "E-Mail: <a href='//ext.blackpinguin.de/certs/rlweb.pgp'>r.l@web.de</a>"   => '/rcl/www/ext/certs/rlweb.pgp',
  "E-Mail: <a href='//ext.blackpinguin.de/certs/bpweb.pgp'>b_p@web.de</a>"   => '/rcl/www/ext/certs/bpweb.pgp',
  "E-Mail: <a href='//ext.blackpinguin.de/certs/clweb.pgp'>c_l@web.de</a>"   => '/rcl/www/ext/certs/clweb.pgp',
//"E-Mail: <a href='//ext.blackpinguin.de/certs/rlhaw.pgp'>r.l@hawhh.de</a>" => '/rcl/www/ext/certs/rlhaw.pgp',
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
        return isset($cas[$name]) ? "<a href='".$cas[$name]."' target='_blank' rel='noopener'>$name</a>" : $name;
}



function crtCheck(){
        global $crtFiles;
	global $now;
        $valid = array();
        $cas = array();
        foreach ($crtFiles as $domain => $file) {
		if (! file_exists($file)) {
			$valid[$domain] = 0;
			$cas[$domain] = "N/A";
			continue;
		}

		$ext = [];
		preg_match("@[^\.]+$@", $file, $ext);
		$pgp = count($ext) >= 1 && $ext[0] === 'pgp';

		$cmd = "openssl x509 -noout -dates -in $file | grep 'notAfter' | grep -o '[^=]*\$'";
		if ($pgp) {
			$cmd = "pgpdump $file | awk '/key expiration time/{getline; print}' | head -n 1 | sed -n 's/.*Time - //p'";
		}
		$v = trim(@shell_exec("$cmd | { read d ; date -d \"\$d\" +%s ; }"));
		# old format: issuer= /C=US/O=Let's Encrypt/CN=Let's Encrypt Authority X3
		# old format: issuer= /O=CAcert Inc./OU=http://www.CAcert.org/CN=CAcert Class 3 Root
		# new format: issuer=C = US, O = Let's Encrypt, CN = Let's Encrypt Authority X3
		# new format: issuer=O = CAcert Inc., OU = http://www.CAcert.org, CN = CAcert Class 3 Root
		$ca = trim(@shell_exec("openssl x509 -noout -issuer -in $file "
                                    . "| grep -Eo '[/= ]O ?= ?[^=/,]*[/,]' "
                                    . "| grep -o '[^=/,]*[/,]\$' "
                                    . "| grep -o '^[^/,]*'"));
		$valid[$domain] = $v;
		if ($pgp) { $cas[$domain] = "GnuPG"; }
		else { $cas[$domain] = ca($ca); }
        }
        asort($valid);
        foreach ($valid as $domain => $until) {
                $date = ( $until > 0 ? toDate(date("Y-m-d H:i:s T", $until)) : "N/A" );
                $ca = $cas[$domain];
                $duration = ( $until > 0 ? toDuration(round($until - $now)) : "N/A" );
		$days = round($until - $now) / (60 * 60 * 24);
                $color = ( $days >= 22 ? "green" : ( $days >= 20 ? "orange" : "red" ) );
                echo "<tr>";
                echo "<td>$domain</td>";
                echo "<td>$ca</td>";
                echo "<td class='$color'>$duration</td>";
                echo "<td>$date</td>";
                echo "</tr>";
        }
}
