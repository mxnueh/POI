<?php
// enviar_notificacion.php - Página para el administrador
session_start();
require_once 'conexion.php';

$tabla_usuarios = 'usuarios';
$tabla_cargos = 'cargo';

// Obtener el ID del usuario actual
$remitente_id = isset($_SESSION['ID']) ? $_SESSION['ID'] : (isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 0);

// Verificar que el ID de remitente existe en la tabla usuarios
$verificar_remitente = "SELECT ID FROM $tabla_usuarios WHERE ID = ?";
$stmt_verificar = $conn->prepare($verificar_remitente);
$stmt_verificar->bind_param("i", $remitente_id);
$stmt_verificar->execute();
$result_remitente = $stmt_verificar->get_result();

if ($result_remitente->num_rows == 0) {
    // El ID del remitente no existe en la tabla usuarios
    // Intentar obtener un ID válido del administrador
    $sql_admin = "SELECT u.ID FROM $tabla_usuarios u 
                 INNER JOIN $tabla_cargos c ON u.id_cargos = c.id 
                 WHERE c.descripcion = 'administrador' LIMIT 1";
    $result_admin = $conn->query($sql_admin);
    
    if ($result_admin->num_rows > 0) {
        $admin = $result_admin->fetch_assoc();
        $remitente_id = $admin['ID'];
        // Actualizar la sesión con el ID correcto
        $_SESSION['ID'] = $remitente_id;
    } else {
        die("Error: No se encontró un usuario administrador válido. Por favor, inicie sesión nuevamente.");
    }
}

// Obtener lista de usuarios (clientes)
$sql = "SELECT u.ID, u.nombres FROM $tabla_usuarios u 
        INNER JOIN $tabla_cargos c ON u.id_cargos = c.id 
        WHERE c.descripcion != 'administrador'";
$result = $conn->query($sql);
$usuarios = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $usuarios[] = $row;
    }
}

// Procesar el envío de notificación
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Usar la tabla existente de notificaciones
    $mensaje = $_POST['mensaje'];
    
    // Verificar si se envía a todos o a usuarios específicos
    if (isset($_POST['enviar_todos']) && $_POST['enviar_todos'] == 1) {
        // Enviar a todos los clientes
        $error_count = 0;
        foreach ($usuarios as $usuario) {
            $destinatario = $usuario['ID'];
            try {
                $sql = "INSERT INTO notificaciones (id_remitente, id_destinatario, mensaje, fecha_creacion, leido) 
                       VALUES (?, ?, ?, CURRENT_TIMESTAMP, 0)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iis", $remitente_id, $destinatario, $mensaje);
                $stmt->execute();
            } catch (Exception $e) {
                $error_count++;
                // Opcional: registrar el error
                error_log("Error al enviar notificación: " . $e->getMessage());
            }
        } 
        
        if ($error_count == 0) {
            $mensaje_exito = "Notificación enviada a todos los clientes";
        } else {
            $mensaje_exito = "Notificación enviada parcialmente. $error_count mensajes no pudieron ser enviados.";
        }
    } elseif (isset($_POST['destinatarios']) && !empty($_POST['destinatarios'])) {
        // Enviar a usuarios seleccionados
        $error_count = 0;
        foreach ($_POST['destinatarios'] as $destinatario) {
            try {
                $sql = "INSERT INTO notificaciones (id_remitente, id_destinatario, mensaje, fecha_creacion, leido) 
                       VALUES (?, ?, ?, CURRENT_TIMESTAMP, 0)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iis", $remitente_id, $destinatario, $mensaje);
                $stmt->execute();
            } catch (Exception $e) {
                $error_count++;
                // Opcional: registrar el error
                error_log("Error al enviar notificación: " . $e->getMessage());
            }
        }
        
        if ($error_count == 0) {
            $mensaje_exito = "Notificación enviada a los usuarios seleccionados";
        } else {
            $mensaje_exito = "Notificación enviada parcialmente. $error_count mensajes no pudieron ser enviados.";
        }
    } else {
        $mensaje_error = "Debe seleccionar al menos un destinatario";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar Notificaciones</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <div class="container">
        <h2>Enviar Notificaciones</h2>
        
        <?php if (isset($mensaje_exito)): ?>
            <div class="alerta exito"><?php echo $mensaje_exito; ?></div>
        <?php endif; ?>
        
        <?php if (isset($mensaje_error)): ?>
            <div class="alerta error"><?php echo $mensaje_error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="mensaje">Mensaje:</label>
                <textarea id="mensaje" name="mensaje" rows="4" required></textarea>
            </div>
            
            <div class="form-group">
                <label>Destinatarios:</label>
                <div class="opciones">
                    <label>
                        <input type="checkbox" id="todos" name="enviar_todos" value="1" onchange="toggleDestinatarios()">
                        Enviar a todos los clientes
                    </label>
                </div>
                
                <div id="lista-destinatarios">
                    <?php foreach ($usuarios as $usuario): ?>
                    <div>
                        <label>
                            <input type="checkbox" name="destinatarios[]" value="<?php echo $usuario['ID']; ?>">
                            <?php echo $usuario['nombres']; ?>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <button type="submit" class="btn">Enviar Notificación</button>
        </form>
        
        <a href="profile.html" class="btn-link">Volver al Panel</a>
    </div>
    
    <script>
        function toggleDestinatarios() {
            const todosCheckbox = document.getElementById('todos');
            const listaDestinatarios = document.getElementById('lista-destinatarios');
            
            if (todosCheckbox.checked) {
                listaDestinatarios.style.display = 'none';
            } else {
                listaDestinatarios.style.display = 'block';
            }
        }
    </script>
</body>
</html>