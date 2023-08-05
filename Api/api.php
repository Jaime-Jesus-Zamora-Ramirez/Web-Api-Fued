<?php
// api.php

include('../conexion/conexion.php'); // Incluir el archivo de conexión

//----------------------METODO GET INICIO-------------------------//

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT id_dentista, titulo, descripcion, imagen_url FROM dentistas";
    $result = $conn->query($sql);

    $tasks = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $tasks[] = array(
                'id_dentista' => $row['id_dentista'],
                'titulo' => $row['titulo'],
                'descripcion' => $row['descripcion'],
                'imagen_url' => $row['imagen_url']
            );
        }
    }

    $conn->close();

    header('Content-Type: application/json');

    //----------------------FINAL-------------------------//


//----------------------METODO POST INICIO-------------------------//
    echo json_encode($tasks);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];

    // Procesar la imagen y guardarla en el servidor
    $targetDir = '../images/'; // Cambia esto a la ruta donde deseas guardar las imágenes
    $nombreArchivo = basename($_FILES['imagen']['name']);
    $targetPath = $targetDir . $nombreArchivo;

    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $targetPath)) {
        $stmt = $conn->prepare("INSERT INTO dentistas (titulo, descripcion, imagen_url) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $titulo, $descripcion, $targetPath);
        
        if ($stmt->execute()) {
            $response = ['message' => 'Datos registrados en la base de datos y archivo subido exitosamente'];
        } else {
            $response = ['error' => 'Error al registrar los datos en la base de datos: ' . $stmt->error];
        }
        
        $stmt->close();
    } else {
        $response = ['error' => 'Error al subir el archivo'];
    }

    $conn->close();

    header('Content-Type: application/json');
    echo json_encode($response);
}
//----------------------FINAL-------------------------//


//----------------------METODO DELETE INICIO-------------------------//
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = $_GET['id']; // Obtén el ID de la tarea a eliminar
    
    // Realiza la lógica para eliminar la tarea con el ID proporcionado
    // Por ejemplo:
    $stmt = $conn->prepare("DELETE FROM dentistas WHERE id_dentista = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $response = ['message' => 'Tarea eliminada exitosamente'];
    } else {
        $response = ['error' => 'Error al eliminar la tarea: ' . $stmt->error];
    }
    
    $stmt->close();

    header('Content-Type: application/json');
    echo json_encode($response);
}
//----------------------FINAL-------------------------//


//----------------------METODO PUT INICIO-------------------------//
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $putData = json_decode(file_get_contents("php://input"), true); // Obtén los datos del cuerpo de la petición

    $id = $putData['id_dentista']; // Obtén el ID del dentista a actualizar
    $titulo = $putData['titulo']; // Obtén el nuevo título del dentista
    $descripcion = $putData['descripcion']; // Obtén la nueva descripción del dentista
    
    // Realiza la lógica para actualizar la tarea con el ID proporcionado
    // Por ejemplo:
    $stmt = $conn->prepare("UPDATE dentistas SET titulo = ?, descripcion = ? WHERE id_dentista = ?");
    $stmt->bind_param("ssi", $titulo, $descripcion, $id);
    
    if ($stmt->execute()) {
        $response = ['message' => 'Tarea actualizada exitosamente'];
    } else {
        $response = ['error' => 'Error al actualizar la tarea: ' . $stmt->error];
    }
    
    $stmt->close();

    header('Content-Type: application/json');
    echo json_encode($response);

    //----------------------FINAL-------------------------//
}
?>


