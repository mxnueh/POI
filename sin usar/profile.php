<?php
require_once 'auth_check.php';
check_auth();

$message = '';
$message_type = '';

// Handle form submission
if ($_POST) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    $errors = [];
    
    // Validate inputs
    if (empty($name)) {
        $errors[] = 'El nombre es requerido';
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email válido es requerido';
    }
    
    // Password change validation
    if (!empty($new_password)) {
        if (empty($current_password)) {
            $errors[] = 'Contraseña actual es requerida para cambiar la contraseña';
        }
        
        if ($new_password !== $confirm_password) {
            $errors[] = 'Las contraseñas nuevas no coinciden';
        }
        
        if (strlen($new_password) < 6) {
            $errors[] = 'La contraseña debe tener al menos 6 caracteres';
        }
    }
    
    if (empty($errors)) {
        if ($db->isConnected()) {
            try {
                // Verify current password if changing password
                if (!empty($new_password)) {
                    $stmt = $db->getConnection()->prepare("SELECT password FROM users WHERE id = ?");
                    $stmt->execute([$_SESSION['user_id']]);
                    $user = $stmt->fetch();
                    
                    if (!$user || !password_verify($current_password, $user['password'])) {
                        $errors[] = 'Contraseña actual incorrecta';
                    }
                }
                
                if (empty($errors)) {
                    // Update user information
                    if (!empty($new_password)) {
                        $stmt = $db->getConnection()->prepare("
                            UPDATE users SET name = ?, email = ?, password = ?, updated_at = NOW() WHERE id = ?
                        ");
                        $stmt->execute([$name, $email, password_hash($new_password, PASSWORD_DEFAULT), $_SESSION['user_id']]);
                    } else {
                        $stmt = $db->getConnection()->prepare("
                            UPDATE users SET name = ?, email = ?, updated_at = NOW() WHERE id = ?
                        ");
                        $stmt->execute([$name, $email, $_SESSION['user_id']]);
                    }
                    
                    // Update session data
                    $_SESSION['user_name'] = $name;
                    $_SESSION['user_email'] = $email;
                    
                    // Log activity
                    log_activity('profile_update', 'User updated profile information');
                    
                    $message = 'Perfil actualizado correctamente';
                    $message_type = 'success';
                }
            } catch (PDOException $e) {
                error_log("Profile update error: " . $e->getMessage());
                $errors[] = 'Error al actualizar el perfil';
            }
        } else {
            // File-based storage fallback
            $users = $storage->load('users');
            $userIndex = -1;
            
            foreach ($users as $index => $user) {
                if ($user['username'] === $_SESSION['username']) {
                    $userIndex = $index;
                    break;
                }
            }
            
            if ($userIndex >= 0) {
                $users[$userIndex]['name'] = $name;
                $users[$userIndex]['email'] = $email;
                
                if (!empty($new_password)) {
                    $users[$userIndex]['password'] = password_hash($new_password, PASSWORD_DEFAULT);
                }
                
                $storage->save('users', $users);
                
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;
                
                log_activity('profile_update', 'User updated profile information');
                
                $message = 'Perfil actualizado correctamente';
                $message_type = 'success';
            }
        }
    }
    
    if (!empty($errors)) {
        $message = implode('<br>', $errors);
        $message_type = 'error';
    }
}

// Get current user data
$current_user = [];
if ($db->isConnected()) {
    try {
        $stmt = $db->getConnection()->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $current_user = $stmt->fetch();
    } catch (PDOException $e) {
        error_log("User data fetch error: " . $e->getMessage());
    }
} else {
    // Fallback to session data
    $current_user = [
        'name' => $_SESSION['user_name'] ?? $_SESSION['username'],
        'email' => $_SESSION['user_email'] ?? $_SESSION['username'] . '@example.com',
        'username' => $_SESSION['username'],
        'role' => $_SESSION['user_role'] ?? 'user',
        'created_at' => '2023-01-01 00:00:00'
    ];
}

echo get_html_header('Mi Perfil', 'profile.php');
?>

<?php if ($message): ?>
<div class="card <?php echo $message_type === 'success' ? 'success' : 'warning'; ?>">
    <?php echo $message; ?>
</div>
<?php endif; ?>

<div class="card">
    <h2>Editar Perfil</h2>
    
    <form method="POST" style="max-width: 500px;">
        <div style="margin-bottom: 15px;">
            <label><strong>Nombre Completo:</strong></label><br>
            <input type="text" name="name" value="<?php echo htmlspecialchars($current_user['name'] ?? ''); ?>" 
                   required style="width: 100%; padding: 8px; margin-top: 5px;">
        </div>
        
        <div style="margin-bottom: 15px;">
            <label><strong>Email:</strong></label><br>
            <input type="email" name="email" value="<?php echo htmlspecialchars($current_user['email'] ?? ''); ?>" 
                   required style="width: 100%; padding: 8px; margin-top: 5px;">
        </div>
        
        <div style="margin-bottom: 15px;">
            <label><strong>Nombre de Usuario:</strong></label><br>
            <input type="text" value="<?php echo htmlspecialchars($current_user['username'] ?? ''); ?>" 
                   disabled style="width: 100%; padding: 8px; margin-top: 5px; background: #f5f5f5;">
            <small>El nombre de usuario no se puede cambiar</small>
        </div>
        
        <hr style="margin: 20px 0;">
        
        <h3>Cambiar Contraseña (Opcional)</h3>
        
        <div style="margin-bottom: 15px;">
            <label><strong>Contraseña Actual:</strong></label><br>
            <input type="password" name="current_password" 
                   style="width: 100%; padding: 8px; margin-top: 5px;">
        </div>
        
        <div style="margin-bottom: 15px;">
            <label><strong>Nueva Contraseña:</strong></label><br>
            <input type="password" name="new_password" 
                   style="width: 100%; padding: 8px; margin-top: 5px;">
        </div>
        
        <div style="margin-bottom: 15px;">
            <label><strong>Confirmar Nueva Contraseña:</strong></label><br>
            <input type="password" name="confirm_password" 
                   style="width: 100%; padding: 8px; margin-top: 5px;">
        </div>
        
        <button type="submit" style="background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">
            Guardar Cambios
        </button>
    </form>
</div>

<div class="card info">
    <h3>Información de Cuenta</h3>
    <table>
        <tr>
            <th>Campo</th>
            <th>Valor</th>
        </tr>
        <tr>
            <td>ID de Usuario</td>
            <td><?php echo htmlspecialchars($_SESSION['user_id']); ?></td>
        </tr>
        <tr>
            <td>Rol</td>
            <td><?php echo htmlspecialchars($current_user['role'] ?? 'user'); ?></td>
        </tr>
        <tr>
            <td>Fecha de Registro</td>
            <td><?php echo htmlspecialchars($current_user['created_at'] ?? 'No disponible'); ?></td>
        </tr>
        <tr>
            <td>Última Actualización</td>
            <td><?php echo htmlspecialchars($current_user['updated_at'] ?? 'No disponible'); ?></td>
        </tr>
    </table>
</div>

<div class="card">
    <h3>Actividad de Sesión</h3>
    <p><strong>Tiempo en línea:</strong> <?php echo gmdate('H:i:s', time() - $_SESSION['login_time']); ?></p>
    <p><strong>IP del cliente:</strong> <?php echo htmlspecialchars($_SERVER['REMOTE_ADDR'] ?? 'No disponible'); ?></p>
    <p><strong>Navegador:</strong> <?php echo htmlspecialchars(substr($_SERVER['HTTP_USER_AGENT'] ?? 'No disponible', 0, 100)); ?>...</p>
    <p><strong>Última actividad:</strong> <?php echo date('Y-m-d H:i:s', $_SESSION['last_activity']); ?></p>
</div>

<?php echo get_html_footer(); ?>
```html_footer(); ?>