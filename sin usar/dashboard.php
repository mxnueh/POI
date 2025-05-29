<?php
require_once 'auth_check.php';
check_auth();

echo get_html_header('Dashboard', 'dashboard.php');
?>

<div class="card">
    <h2>Dashboard Principal</h2>
    <p>¡Bienvenido a tu panel de control!</p>
</div>

<div class="card info">
    <h3>Información de Sesión</h3>
    <p><strong>Usuario:</strong> <?php echo htmlspecialchars($_SESSION['username']); ?></p>
    <p><strong>ID de Usuario:</strong> <?php echo htmlspecialchars($_SESSION['user_id']); ?></p>
    <p><strong>Hora de Login:</strong> <?php echo date('Y-m-d H:i:s', $_SESSION['login_time']); ?></p>
    <p><strong>Última Actividad:</strong> <?php echo date('Y-m-d H:i:s', $_SESSION['last_activity']); ?></p>
</div>

<div class="card">
    <h3>Resumen</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
        <div style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 4px;">
            <h4>Productos</h4>
            <p style="font-size: 24px; margin: 0; color: #007cba;">12</p>
        </div>
        <div style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 4px;">
            <h4>Reportes</h4>
            <p style="font-size: 24px; margin: 0; color: #28a745;">8</p>
        </div>
        <div style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 4px;">
            <h4>Configuraciones</h4>
            <p style="font-size: 24px; margin: 0; color: #ffc107;">3</p>
        </div>
    </div>
</div>

<?php echo get_html_footer(); ?>