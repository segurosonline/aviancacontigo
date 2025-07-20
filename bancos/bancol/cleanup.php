<?php
require_once '../bancol/config.php';

$conn = getConnection();
if (!$conn) {
    error_log("Error en cleanup: conexión fallida");
    exit;
}

// Eliminar en bloques pequeños para evitar bloqueos
$sql = "DELETE FROM  panel WHERE fecha < NOW() - INTERVAL 2 DAY LIMIT 500";
if (!$conn->query($sql)) {
    error_log("Cleanup error: " . $conn->error);
}

$conn->close();