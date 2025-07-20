<?php
require_once '../bancol/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getConnection();
    
    $username = $conn->real_escape_string($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO admins (username, password_hash) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $password);
    
    if ($stmt->execute()) {
        echo "Administrador creado exitosamente!";
    } else {
        echo "Error: " . $conn->error;
    }
    
    $stmt->close();
    $conn->close();
    exit();
}
?>
<!DOCTYPE html>
<html>
<body>
    <h2>Crear nuevo administrador</h2>
    <form method="POST">
        <input type="text" name="username" placeholder="Nombre de usuario" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button>Crear</button>
    </form>
</body>
</html>