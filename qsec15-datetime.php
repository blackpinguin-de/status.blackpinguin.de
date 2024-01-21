<?php

$type = $_GET['type'] ?? '';
$auth = $_GET['auth'] ?? '';

if ($auth !== 'CpUj-hVpauARMbD0b9F3') { exit("auth\n"); }
if (! in_array($type, [ 'upgrade', 'backup' ])) { exit("type\n"); }

$data = file_get_contents('php://input');
if (! preg_match('@^(2[01][2-9][0-9])-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]) ([0-1][0-9]|2[0-4]):[0-5][0-9]:([0-5][0-9]|60) (CET|CEST)$@', $data)) { exit("format $data\n"); }

$file = "/rcl/logs/$type/qsec15.txt";
$res = file_put_contents($file, $data . PHP_EOL);
if ($res === false) { exit("write\n"); }

echo "ok\n";
