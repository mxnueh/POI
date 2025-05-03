<?php
// conexion.php - Archivo para la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "poi_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>