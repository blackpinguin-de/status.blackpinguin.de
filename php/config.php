<?php

$halt_on_mysql_error = false;

$mysql_config = array(
      'host' => 'localhost'
    , 'db' => 'blackpinguin'
    , 'user' => 'iplog'
    , 'pw' => 'v8uzO8yeIY+sR8Q0KaJhGLyd8xEzXS'
);

$mysqli = new mysqli($mysql_config['host'], $mysql_config['user'],
                     $mysql_config['pw'],   $mysql_config['db']);
unset($mysql_config['pw']);
unset($mysql_config);

if($mysqli->connect_error && (! isset($halt_on_mysql_error) || $halt_on_mysql_error)){
	die("ERROR: MySQL connection");
}
if(! $mysqli->connect_error){
	@mysqli_query($mysqli, "set names 'utf8';");
}
?>
