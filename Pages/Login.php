<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Administracion POI</title>
    <link rel="stylesheet" href="LoginAdmin.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        .error-message {
            color: #D13239;
            text-align: center;
            margin-bottom: 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <form method="POST" action="validar.php">
            <h2 class="title">BIENVENIDO</h2>
            <h2 class="subtitle">SALESIANO</h2>
            
            <?php
            if(isset($_GET['error']) && $_GET['error'] != '') {
                $error = $_GET['error'];
                echo '<div class="error-message">';
                
                switch($error) {
                    case 'conexion':
                        echo 'Error de conexión a la base de datos';
                        break;
                    case 'credenciales':
                        echo 'Usuario o contraseña incorrectos';
                        break;
                    case 'cargo':
                        echo 'Tipo de usuario no válido';
                        break;
                    default:
                        echo '';  // No mostrar nada si el error no coincide
                }
                
                echo '</div>';
            }
            ?>
            
            <div class="input-group">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="16" height="16" fill = "#D13239"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512l388.6 0c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304l-91.4 0z"/></svg>                
                <input type="text" id="usuario" name="usuario" placeholder="Usuario">
            </div>
        
            <div class="input-group">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="16" height="16" fill = "#D13239"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M144 144l0 48 160 0 0-48c0-44.2-35.8-80-80-80s-80 35.8-80 80zM80 192l0-48C80 64.5 144.5 0 224 0s144 64.5 144 144l0 48 16 0c35.3 0 64 28.7 64 64l0 192c0 35.3-28.7 64-64 64L64 512c-35.3 0-64-28.7-64-64L0 256c0-35.3 28.7-64 64-64l16 0z"/></svg>                
                <input type="password" id="password" name="contraseña" placeholder="Contraseña">
            </div>
        
            <input type="submit" name="submit" value="Ingresar" class="btn-login">
        
            <div class="divider">
                <hr><span>Or</span><hr>
            </div>
        
            <button type="button" class="btn-id">ID</button>
        </form>
    </div>
</body>
</html>