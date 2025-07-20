<?php
require_once '../bancol/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getConnection();
    
    $username = $conn->real_escape_string($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Buscar usuario en la base de datos
    $stmt = $conn->prepare("SELECT id, password_hash FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            header("Location: panel_u2.php");
            exit();
        }
    }
    
    // Si llega aquí, las credenciales son incorrectas
    $error = "Credenciales inválidas";
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Administrador</title>
    <style>
        .error { color: red; }
    </style>
</head>
<body>
    <h2>Acceso al panel U2</h2>
    <?php if(isset($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div>
            <label>Usuario:</label>
            <input type="text" name="username" required>
        </div>
        <div>
            <label>Contraseña:</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit">Ingresar</button>
    </form>
</body>
</html>