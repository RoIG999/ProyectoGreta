<?php
// conexion.php - VERSIÓN PARA NGROK

// PARA NGROK (acceso externo)
$servername = "localhost"; // O la IP de tu máquina
$username = "root"; 
$password = ""; // Tu password de XAMPP
$dbname = "abmgreta"; // Tu base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$conn->set_charset("utf8");
?>