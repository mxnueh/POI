<?php
// ver_notificaciones.php - Página para cualquier usuario
session_start();
require_once '../db.php'; // Asegúrate de que la ruta sea correcta

$_SESSION['usuario_id'] = 2; // Reemplaza con un ID válido
$_SESSION['cargo'] = 'cliente'; // o 'administrador'


$usuario_id = $_SESSION['usuario_id']; 

// Marcar notificación como leída si se solicita
if (isset($_GET['marcar_leido']) && is_numeric($_GET['marcar_leido'])) {
    $notificacion_id = $_GET['marcar_leido'];
    $sql = "UPDATE notificaciones SET leido = 1
            WHERE id = ? AND id_destinatario = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $notificacion_id, $usuario_id);
    $stmt->execute();
   
    header('Location: ver_notificaciones.php');
    exit;
}

// Obtener notificaciones del usuario
$sql = "SELECT n.*, u.nombres as remitente_nombre
        FROM notificaciones n
        INNER JOIN usuarios u ON n.id_remitente = u.ID
        WHERE n.id_destinatario = ?
        ORDER BY n.fecha_creacion DESC";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$notificaciones = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $notificaciones[] = $row;
    }
}

// Contar notificaciones no leídas
$sql = "SELECT COUNT(*) as total FROM notificaciones
        WHERE id_destinatario = ? AND leido = 0";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$notificaciones_no_leidas = $row['total'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Notificaciones</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <div class="container">
        <h2>Mis Notificaciones <span class="contador"><?php echo $notificaciones_no_leidas; ?></span></h2>
       
        <?php if (empty($notificaciones)): ?>
            <p>No tienes notificaciones.</p>
        <?php else: ?>
            <div class="notificaciones-lista">
                <?php foreach ($notificaciones as $notificacion): ?>
                    <div class="notificacion <?php echo $notificacion['leido'] == 0 ? 'no-leida' : ''; ?>">
                        <div class="notificacion-header">
                            <span class="remitente"><?php echo htmlspecialchars($notificacion['remitente_nombre']); ?></span>
                            <span class="fecha"><?php echo date('d/m/Y H:i', strtotime($notificacion['fecha_creacion'])); ?></span>
                        </div>
                        <div class="notificacion-body">
                            <?php echo htmlspecialchars($notificacion['mensaje']); ?>
                        </div>
                        <?php if ($notificacion['leido'] == 0): ?>
                            <div class="notificacion-footer">
                                <a href="?marcar_leido=<?php echo $notificacion['id']; ?>" class="btn-small">Marcar como leída</a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
       
        <a href="<?php echo isset($_SESSION['cargo']) && $_SESSION['cargo'] == 'administrador' ? 'profile.php' : 'profile.php'; ?>" class="btn-link">Volver al Panel</a>
    </div>
</body>
</html>