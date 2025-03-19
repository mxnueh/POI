<?php
session_start();

if(isset($_POST['submit']) && isset($_POST['usuario']) && isset($_POST['contraseña'])) {
    $usuario = $_POST['usuario'];
    $contraseña = $_POST['contraseña'];
    $_SESSION['usuario'] = $usuario;

    $conexion = mysqli_connect("localhost", "root", "", "poi");

    if (!$conexion) {
        header("location: Login.php?error=conexion");
        exit();
    }

    $consulta = "SELECT * FROM usuarios WHERE usuario='$usuario' AND contraseña='$contraseña'";
    $resultado = mysqli_query($conexion, $consulta);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $filas = mysqli_fetch_array($resultado);

        if ($filas['id_cargo'] == 1) { // administrador
            header("location: admin_side/profile.html");
            exit();
        } else if ($filas['id_cargo'] == 2) { // cliente
            header("location: client_side/profile.php");
            exit();
        } else {
            header("location: Login.php?error=cargo");
            exit();
        }
        mysqli_free_result($resultado);
    } else {
        header("location: Login.php?error=credenciales");
        exit();
    }
    mysqli_close($conexion);
} else {
    header("location: Login.php");
    exit();
}
?>