<?php
//MYSQL CONNECTION STRING OOP

$host = 'localhost';
$user = 'root';
$password = 'ipc';
$database = 'sys_service_management';

$mysqli = new mysqli($host, $user, $password, $database);
 
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') '
            . $mysqli->connect_error);
}
//~ 
//~ //IPC MAIN

$database = 'ipc_central';

$ipc_central = new mysqli($host, $user, $password, $database);
 
if ($ipc_central->connect_error) {
    die('Connect Error (' . $ipc_central->connect_errno . ') '
            . $ipc_central->connect_error);
}

?>
