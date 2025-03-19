<?php

$conexion = new mysqli("localhost", "root", "", "poi");

if ($conexion->connect_error) {
    die("Connection failed: " . $conexion->connect_error);
}

?>