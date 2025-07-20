<?php
session_start();

$max_requests = 15; // Máximo 15 solicitudes/minuto por usuario
$time_frame = 60; // 1 minuto

if (!isset($_SESSION['requests'])) {
    $_SESSION['requests'] = [];
}

// Limpiar registros antiguos
$_SESSION['requests'] = array_filter($_SESSION['requests'], function($time) use ($time_frame) {
    return $time > time() - $time_frame;
});

// Contar solicitudes recientes
if (count($_SESSION['requests']) >= $max_requests) {
    http_response_code(429);
    die("Demasiadas solicitudes. Intenta nuevamente más tarde.");
}

// Registrar nueva solicitud
$_SESSION['requests'][] = time();
?>