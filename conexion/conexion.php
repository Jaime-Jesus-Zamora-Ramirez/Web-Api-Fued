<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dentista_sql";   // Nombre de la base de datos

// Crear una conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}



?>
