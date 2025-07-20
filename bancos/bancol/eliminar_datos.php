<?php
require_once '../bancol/config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    die('Acceso no autorizado');
}

$conn = getConnection();
$uuid = $conn->real_escape_string($_GET['id']);

$stmt = $conn->prepare("DELETE FROM  panel WHERE id = ?");
$stmt->bind_param("s", $uuid);
$stmt->execute();
$stmt->close();
$conn->close();

echo "Registro eliminado correctamente";