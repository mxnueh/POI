<?php
require_once 'auth_check.php';
check_auth();

echo get_html_header('Configuración', 'settings.php');
?>

<div class="card">
    <h2>Configuración del Sistema</h2>
    <p>Personaliza tu experiencia y configuraciones de seguridad.</p>
</div>

<div class="card">
    <h3>Configuración de Cuenta</h3>
    <form style="max-width: 400px;">
        <p>
            <label>Nombre de Usuario:</label><br>
            <input type="text" value="<?php echo htmlspecialchars($_SESSION['username']); ?>" disabled style="width: 100%; padding: 8px;">
        </p>
        <p>
            <label>Cambiar Contraseña:</label><br>
            <input type="password" placeholder="Nueva contraseña" style="width: 100%; padding: 8px;">
        </p>
        <p>
            <label>Confirmar Contraseña:</label><br>
            <input type="password" placeholder="Confirmar contraseña" style="width: 100%; padding: 8px;">
        </p>
        <button type="button" onclick="alert('Cambios guardados (demo)')">Guardar Cambios</button>
    </form>
</div>

<div class="card">
    <h3>Configuración de Seguridad</h3>
    <table>
        <tr>
            <td>Autenticación de Dos Factores</td>
            <td>
                <input type="checkbox" onclick="alert('Función de 2FA aquí')"> Activar
            </td>
        </tr>
        <tr>
            <td>Notificaciones de Login</td>
            <td>
                <input type="checkbox" checked onclick="alert('Configuración actualizada')"> Recibir emails
            </td>
        </tr>
        <tr>
            <td>Tiempo de Sesión</td>
            <td>
                <select onchange="alert('Tiempo de sesión actualizado')">
                    <option>15 minutos</option>
                    <option selected>30 minutos</option>
                    <option>1 hora</option>
                    <option>2 horas</option>
                </select>
            </td>
        </tr>
    </table>
</div>

<div class="card info">
    <h3>Información de Sesión Actual</h3>
    <p><strong>ID de Sesión:</strong> <?php echo htmlspecialchars(session_id()); ?></p>
    <p><strong>Tiempo Restante:</strong> <span id="session-timer">Calculando...</span></p>
    <p><strong>Última Regeneración:</strong> <?php echo isset($_SESSION['last_regeneration']) ? date('H:i:s', $_SESSION['last_regeneration']) : 'N/A'; ?></p>
</div>

<script>
// Simple session timer
function updateTimer() {
    const loginTime = <?php echo $_SESSION['last_activity']; ?> * 1000;
    const timeoutDuration = 30 * 60 * 1000; // 30 minutes
    const now = Date.now();
    const remaining = timeoutDuration - (now - loginTime);
    
    if (remaining > 0) {
        const minutes = Math.floor(remaining / 60000);
        const seconds = Math.floor((remaining % 60000) / 1000);
        document.getElementById('session-timer').textContent = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
    } else {
        document.getElementById('session-timer').textContent = 'Sesión expirada';
    }
}

setInterval(updateTimer, 1000);
updateTimer();
</script>

<?php echo get_html_footer(); ?>