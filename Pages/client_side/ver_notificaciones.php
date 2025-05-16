<?php
// ver_notificaciones.php - Improved notification system
session_start();
require_once '../db.php';

// Depuración de la sesión (puedes eliminar estas líneas después)
// error_log('DEBUG - SESSION: ' . print_r($_SESSION, true));

// Security check - redirect if not logged in
if (!isset($_SESSION['usuario_id']) || empty($_SESSION['usuario_id'])) {
    // Depuración (puedes eliminar después)
    error_log('Redirigiendo a login - SESSION vacía: ' . print_r($_SESSION, true));
    
    // Guardamos la URL actual para redirigir después del login
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    
    header('Location: ../login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$cargo = isset($_SESSION['cargo']) ? $_SESSION['cargo'] : 'cliente'; // Valor por defecto

// Process notification actions
if (isset($_GET['action']) && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $notificacion_id = $_GET['id'];
    
    if ($_GET['action'] === 'mark_read') {
        // Mark single notification as read
        $sql = "UPDATE notificaciones SET leido = 1
                WHERE id = ? AND id_destinatario = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ii", $notificacion_id, $usuario_id);
        $stmt->execute();
    } 
    elseif ($_GET['action'] === 'delete') {
        // Delete notification (optional feature)
        $sql = "DELETE FROM notificaciones WHERE id = ? AND id_destinatario = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ii", $notificacion_id, $usuario_id);
        $stmt->execute();
    }
    
    // Redirect to clear the GET parameters
    header('Location: ver_notificaciones.php');
    exit;
}

// Mark all as read action
if (isset($_GET['mark_all_read'])) {
    $sql = "UPDATE notificaciones SET leido = 1 WHERE id_destinatario = ? AND leido = 0";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    
    header('Location: ver_notificaciones.php');
    exit;
}

// Pagination settings
$notifications_per_page = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $notifications_per_page;

// Get total number of notifications for pagination
$sql = "SELECT COUNT(*) as total FROM notificaciones WHERE id_destinatario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$total_notifications = $row['total'];
$total_pages = ceil($total_notifications / $notifications_per_page);

// Get unread notifications count
$sql = "SELECT COUNT(*) as total FROM notificaciones 
        WHERE id_destinatario = ? AND leido = 0";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$notificaciones_no_leidas = $row['total'];

// Filter options
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$filterSql = '';
$filterParams = [$usuario_id];
$filterTypes = "i";

if ($filter === 'unread') {
    $filterSql = " AND n.leido = 0";
}

// Get notifications with pagination and filtering
$sql = "SELECT n.*, u.nombres as remitente_nombre, c.descripcion as cargo_remitente
        FROM notificaciones n
        INNER JOIN usuarios u ON n.id_remitente = u.ID
        LEFT JOIN cargo c ON u.id_cargos = c.id
        WHERE n.id_destinatario = ?$filterSql
        ORDER BY n.fecha_creacion DESC
        LIMIT ? OFFSET ?";

// Añadir los parámetros adicionales al array
$filterParams[] = $notifications_per_page;
$filterParams[] = $offset;

$stmt = $conexion->prepare($sql);
$stmt->bind_param($filterTypes . "ii", ...$filterParams);
$stmt->execute();
$result = $stmt->get_result();

$notificaciones = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $notificaciones[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Notificaciones</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <style>
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .notificaciones-lista {
            margin-top: 20px;
        }
        .notificacion {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
            background-color: #f9f9f9;
        }
        .notificacion.no-leida {
            background-color: #f0f7ff;
            border-left: 4px solid #0066cc;
        }
        .notificacion-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 0.9em;
        }
        .remitente {
            font-weight: bold;
        }
        .cargo {
            color: #666;
            margin-left: 5px;
        }
        .fecha {
            color: #888;
        }
        .notificacion-body {
            margin-bottom: 10px;
            line-height: 1.4;
        }
        .notificacion-footer {
            text-align: right;
            padding-top: 8px;
            border-top: 1px solid #eee;
        }
        .btn-small {
            background-color: #0066cc;
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            text-decoration: none;
            font-size: 0.8em;
            margin-left: 10px;
        }
        .btn-small.delete {
            background-color: #cc3300;
        }
        .btn-link {
            display: inline-block;
            margin-top: 20px;
            color: #0066cc;
            text-decoration: none;
        }
        .contador {
            background-color: #cc3300;
            color: white;
            border-radius: 50%;
            padding: 2px 8px;
            font-size: 0.8em;
            margin-left: 5px;
        }
        .filters {
            margin: 15px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .pagination {
            margin-top: 20px;
            text-align: center;
        }
        .pagination a, .pagination span {
            display: inline-block;
            padding: 5px 10px;
            margin: 0 3px;
            border: 1px solid #ddd;
            border-radius: 3px;
            text-decoration: none;
        }
        .pagination .current {
            background-color: #0066cc;
            color: white;
            border-color: #0066cc;
        }
        .no-notificaciones {
            text-align: center;
            padding: 30px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Mis Notificaciones 
            <?php if ($notificaciones_no_leidas > 0): ?>
                <span class="contador"><?php echo $notificaciones_no_leidas; ?></span>
            <?php endif; ?>
        </h2>
        
        <div class="filters">
            <div>
                <a href="?filter=all" class="<?php echo $filter === 'all' ? 'active' : ''; ?>">Todas</a> | 
                <a href="?filter=unread" class="<?php echo $filter === 'unread' ? 'active' : ''; ?>">No leídas</a>
            </div>
            <?php if ($notificaciones_no_leidas > 0): ?>
                <a href="?mark_all_read=1" class="btn-small">Marcar todas como leídas</a>
            <?php endif; ?>
        </div>
        
        <?php if (empty($notificaciones)): ?>
            <div class="no-notificaciones">
                <p>No tienes notificaciones<?php echo $filter === 'unread' ? ' sin leer' : ''; ?>.</p>
            </div>
        <?php else: ?>
            <div class="notificaciones-lista">
                <?php foreach ($notificaciones as $notificacion): ?>
                    <div class="notificacion <?php echo $notificacion['leido'] == 0 ? 'no-leida' : ''; ?>">
                        <div class="notificacion-header">
                            <div>
                                <span class="remitente"><?php echo htmlspecialchars($notificacion['remitente_nombre']); ?></span>
                                <?php if (!empty($notificacion['cargo_remitente'])): ?>
                                    <span class="cargo">(<?php echo htmlspecialchars($notificacion['cargo_remitente']); ?>)</span>
                                <?php endif; ?>
                            </div>
                            <span class="fecha"><?php echo date('d/m/Y H:i', strtotime($notificacion['fecha_creacion'])); ?></span>
                        </div>
                        <div class="notificacion-body">
                            <?php echo nl2br(htmlspecialchars($notificacion['mensaje'])); ?>
                        </div>
                        <div class="notificacion-footer">
                            <?php if ($notificacion['leido'] == 0): ?>
                                <a href="?action=mark_read&id=<?php echo $notificacion['id']; ?>" class="btn-small">Marcar como leída</a>
                            <?php endif; ?>
                            <a href="?action=delete&id=<?php echo $notificacion['id']; ?>" class="btn-small delete" onclick="return confirm('¿Estás seguro de eliminar esta notificación?')">Eliminar</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>&filter=<?php echo $filter; ?>">&laquo; Anterior</a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if ($i == $page): ?>
                            <span class="current"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?page=<?php echo $i; ?>&filter=<?php echo $filter; ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>&filter=<?php echo $filter; ?>">Siguiente &raquo;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <a href="<?php echo $cargo == 'administrador' ? 'admin_panel.php' : 'profile.php'; ?>" class="btn-link">Volver al Panel</a>
    </div>
</body>
</html>