<?php

// Ping Hosts to see if they're online
echo trim(@shell_exec("/rcl/www/status/bash/hosts_online.sh"));
?>
