<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');
session_start();


error_reporting(E_ALL); 
ini_set('display_errors', 1); 

function getConnection() {
    $host = '77.37.127.147';
    $user = 'u491053988_servi';
    $pass = 'Admin1122@kasikasi';
    $db = 'u491053988_servi';
    
    $conn = new mysqli($host, $user, $pass, $db);
    
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }
    
    return $conn;
}
?>
