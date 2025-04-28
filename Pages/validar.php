<?php
session_start();

if (isset($_POST['submit']) && isset($_POST['usuario']) && isset($_POST['contraseña'])) {
    $usuario = $_POST['usuario'];
    $contraseña = $_POST['contraseña'];
    
    $conexion = mysqli_connect("localhost", "root", "", "poi_db");

    if (!$conexion) {
        header("Location: Login.php?error=conexion");
        exit();
    }

    // Consulta segura con prepared statements
    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE usuarios = ? AND contraseñas = ?");
    $stmt->bind_param("ss", $usuario, $contraseña); // "ss" = string, string

    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado && $resultado->num_rows > 0) {
        $filas = $resultado->fetch_assoc();

        $_SESSION['usuario'] = $usuario;
        $_SESSION['id_usuario'] = $filas['ID'];
        $_SESSION['id_cargo'] = $filas['id_cargos'];

        if ($filas['id_cargos'] == 1) {
            header("Location: admin_side/profile.html");
            exit();
        } elseif ($filas['id_cargos'] == 2) {
            header("Location: client_side/profile.php");
            exit();
        } else {
            header("Location: Login.php?error=cargo");
            exit();
        }
    } else {
        header("Location: Login.php?error=credenciales");
        exit();
    }

    $stmt->close();
    $conexion->close();
} else {
    header("Location: Login.php");
    exit();
}
?>
