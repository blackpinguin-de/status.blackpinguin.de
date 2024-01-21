<!doctype html>
<html lang="en">
<head>
    <title><?= $_SERVER['HTTP_HOST'] ?></title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta http-equiv="content-language" content="en">
    <meta name="DC.Language" content="en">
    <meta name="author" content="Robin Christopher Ladiges">
    <meta name="DC.Creator" content="Robin Christopher Ladiges">
    <meta name="robots" content="all">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" title="Default (blue / yellow)" type="text/css" href="/style.css">
</head>
<body>
<div id="main">
    <div id="round" class="round">

        <!-- Title -->
        <h1 id="title" class="round sub">
            <?= $_SERVER['HTTP_HOST'] ?>
            <a class="round sub imp" href="https://rcl.blackpinguin.de/legal">Impressum</a>
        </h1>

        <!-- Services -->
        <div class="half">
            <?php require_once('php/main.php'); ?>

            <div class="left">

                <div class="round sub">
                    <h2 class="header">Server</h2>
                    <table>
                        <tr> <td>Current Runtime:</td> <td><?= $runtime ?></td> </tr>
                        <tr> <td>Current Time:</td> <td><?= date('Y-m-d H:i:s T') ?></td> </tr>
                        <tr> <td style="visibility: hidden;">&nbsp;</td> </tr>
                        <tr> <td>Distribution:</td> <td><?= $distro ?></td> </tr>
                        <tr> <td>Runlevel:</td> <td><?= $runlevel ?></td> </tr>
                        <tr> <td>Installation:</td> <td>2017-06-27 20:20 CEST</td> </tr>
                        <tr> <td>Last Upgrade:</td> <td><?= $apt ?></td> </tr>
                        <tr> <td>Last Backup:</td> <td><?= $last_backup ?></td> </tr>
                        <tr> <td style="visibility: hidden;">&nbsp;</td> </tr>
                        <tr> <td>Hardware:</td> <td>Ryzen 7 1700, ASUS Prime B350M-A</td> </tr>
                        <tr> <td>Acquisition:</td> <td>2017-06-04</td> </tr>
                        <tr> <td>Last Maintenance:</td> <td>2020-02-27</td> </tr>
                        <tr> <td>Last Reboot:</td> <td><?= $boottime ?></td> </tr>
                    </table>
                </div>

                <div class="round sub">
                    <h2 class="header">Private Services</h2>
                    <table>
                        <tr> <th>Service</th> <th>Port</th> <th>Status</th> </tr>
                        <?php privateServices(); ?>
                    </table>
                </div>

            </div>

            <div class="right">

                <div class="round sub">
                    <h2 class="header">Public Services</h2>
                    <table>
                        <tr> <th>Service</th> <th>Port</th> <th>Status</th> </tr>
                        <?php publicServices(); ?>
                    </table>
                </div>

            </div>

        </div>

        <!-- Hosts -->
        <div class="round sub">
            <h2 class="header">Network Hosts</h2>
            <table>
                <tr> <th>Name</th> <th>IP</th> <th>Status</th> <th>Last Backup</th> <th>Age</th> <th>Last Upgrade</th> <th>Age</th> </tr>
                <?php hostCheck(); ?>
            </table>
        </div>

        <!-- Current Problems -->
        <div class="round sub">
            <h2 class="header">Unresolved Problems</h2>
            <table>
                <?php open_problems(); ?>
            </table>
        </div>

        <!-- Disk Usage -->
        <div class="round sub">
            <h2 class="header">Disk Usage</h2>
            <?php diskBars(); ?>
        </div>

        <!-- Certificates -->
        <div class="round sub">
            <h2 class="header">X.509 and PGP Certificates</h2>
            <table>
            <tr> <th>Domain</th> <th>Certificate Authority</th> <th>Valid For</th> <th>Valid Until</th> </tr>
            <?php crtCheck(); ?>
            </table>
        </div>

        <!-- IPs -->
        <div class="round sub">
            <h2 class="header">IPv4 Addresses of the last 7 days</h2>
            <table>
            <?php lastIPs(); ?>
            </table>
        </div>

        <!-- IP Distribution -->
        <div class="round sub">
            <h2 class="header">IPv4 Address Distribution - Networks By Duration</h2>
            <table>
            <tr> <th>Network</th> <th>Duration</th> <th># Alloc.</th> <th># IPs</th> <th>First Allocation</th> <th>Last Allocation</th> </tr>
            <?php topIPs(); ?>
            </table>
            <br/>
            <div><?php ipDowntime(); echo " "; ipDowntimeLastYear(); ?></div>
        </div>

    </div>

    <!-- author -->
    <!-- author and creative commons licence information -->
    <div class="footer">
        <a id="w3c" title="Valid HTML 4.01 Transitional" href="http://validator.w3.org/check?uri=referer" target="_blank" rel="noopener"></a>
        This page was created by <a class="footer" href="https://rcl.blackpinguin.de/" target="_blank" rel="noopener">Robin&nbsp;Christopher&nbsp;Ladiges</a>
        <a id="cc" title="cc by-sa" href="https://creativecommons.org/licenses/by-sa/3.0/de/" target="_blank" rel="noopener"></a>
        <!-- This page (not the page and content it's linking to) is under creative commons by-sa 3.0 de license, you can read it here: https://creativecommons.org/licenses/by-sa/3.0/de/legalcode -->
    </div>

</div>

</body>
</html>
