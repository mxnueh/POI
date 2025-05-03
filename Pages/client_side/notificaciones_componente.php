<?php
// notificaciones_componente.php - Para incluir en las páginas donde se necesita mostrar el contador
function obtener_notificaciones_no_leidas($conn, $usuario_id) {
    $sql = "SELECT COUNT(*) as total FROM poi_db_notificaciones 
            WHERE id_destinatario = ? AND leido = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total'];
}
?>

<!-- Para incluir en la barra de navegación de cualquier página -->
<div class="nav-item">
    <a href="ver_notificaciones.php" class="nav-link">
        Notificaciones
        <?php 
        $notificaciones_no_leidas = obtener_notificaciones_no_leidas($conn, $_SESSION['usuario_id']);
        if ($notificaciones_no_leidas > 0):
        ?>
        <span class="badge"><?php echo $notificaciones_no_leidas; ?></span>
        <?php endif; ?>
    </a>
</div>