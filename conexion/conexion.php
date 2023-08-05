<?php
$servername = "localhost";  // Puede ser "localhost" si estás en el mismo servidor
$username = "root";    // Nombre de usuario de la base de datos
$password = "";        // Contraseña del usuario
$dbname = "dentista_sql";   // Nombre de la base de datos

// Crear una conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Ahora puedes realizar consultas y operaciones en la base de datos aquí


?>
