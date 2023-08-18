<?php

include('../conexion/conexion.php');
header("Access-Control-Allow-Origin: http://localhost/dentist/api/api.php");
header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS, DELETE, HEAD, REPORT");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Allow:");
    header("HTTP/1.1 200 OK");
    echo "Solicitud OPTIONS exitosa";
    exit();
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT id_dentista, titulo, descripcion, correo_electronico FROM dentistas";
    $result = $conn->query($sql);

    $tasks = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $tasks[] = array(
                'id_dentista' => $row['id_dentista'],
                'titulo' => $row['titulo'],
                'descripcion' => $row['descripcion'],
                'correo_electronico' => $row['correo_electronico']
            );
        }
    }

    $conn->close();
    header('Content-Type: application/json');
    echo json_encode($tasks);
    exit();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json_input = file_get_contents('php://input');
    $data = json_decode($json_input, true);

    $titulo = isset($data['titulo']) ? $data['titulo'] : '';
    $descripcion = isset($data['descripcion']) ? $data['descripcion'] : '';
    $correo_electronico = isset($data['correo_electronico']) ? $data['correo_electronico'] : '';

    $stmt = $conn->prepare("INSERT INTO dentistas (titulo, descripcion, correo_electronico) VALUES (?, ? , ?)");
    $stmt->bind_param("sss", $titulo, $descripcion, $correo_electronico);

    if ($stmt->execute()) {
        $response = ['message' => 'Datos registrados en la base'];
    } else {
        $response = ['error' => 'Error al registrar los datos en la base de datos: ' . $stmt->error];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    

} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $taskId = isset($_GET['id']) ? $_GET['id'] : '';

    $stmt = $conn->prepare("DELETE FROM dentistas WHERE id_dentista = ?");
    $stmt->bind_param("i", $taskId);

    $response = ['success' => false, 'message' => 'No se pudo eliminar la tarea'];

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Tarea eliminada exitosamente';
    } else {
        $response['error'] = 'Error al eliminar la tarea: ' . $stmt->error;
    }

    $stmt->close();

    header('Content-Type: application/json');
    echo json_encode($response);


} elseif ($_SERVER['REQUEST_METHOD'] === 'HEAD') {
    $sql = "SELECT COUNT(*) AS total FROM dentistas";
    $result = $conn->query($sql);

    $totalDentistas = 0;
    if ($result) {
        $row = $result->fetch_assoc();
        $totalDentistas = $row['total'];
    }

    $conn->close();

    header('Content-Type: application/json');
    header('X-Total-Dentistas: ' . $totalDentistas);

    $response = array(
        'message' => 'Método HEAD ejecutado exitosamente'
    );

    echo json_encode($response);
    http_response_code(200); 
    exit();
}elseif ($_SERVER['REQUEST_METHOD'] === 'CONNECT') {
    $conn->close();
    echo "Solicitud CONNECT exitosa";
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $putData = json_decode(file_get_contents("php://input"), true);
    $id = isset($putData['id_dentista']) ? $putData['id_dentista'] : '';
    $titulo = isset($putData['titulo']) ? $putData['titulo'] : '';
    $descripcion = isset($putData['descripcion']) ? $putData['descripcion'] : '';
    $correo_electronico = isset($putData['correo_electronico']) ? $putData['correo_electronico'] : '';
     
    
    $stmt = $conn->prepare("UPDATE dentistas SET titulo = ?, descripcion = ?,  correo_electronico = ? WHERE id_dentista = ?");
    $stmt->bind_param("sssi", $titulo, $descripcion,$correo_electronico, $id);
    
    if ($stmt->execute()) {
        $response = ['message' => 'Se actualizo correctamente'];
    } else {
        $response = ['error' => 'Error al actualizar la tarea: ' . $stmt->error];
    }
    
    $stmt->close();

    header('Content-Type: application/json');
    echo json_encode($response);
}


function generateCustomReport($id) {
    include('../conexion/conexion.php');
    
    // Depuración: Imprimir el ID recibido
    echo "ID recibido: $id ";
    
    // Consultar los datos en la base de datos utilizando el ID
    $stmt = $conn->prepare("SELECT id_dentista, titulo, descripcion, correo_electronico FROM dentistas WHERE id_dentista = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Verificar si se encontraron resultados
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $customReport = array(
            'id_dentista' => $row['id_dentista'],
            'titulo' => $row['titulo'],
            'descripcion' => $row['descripcion'],
            'correo_electronico' => $row['correo_electronico']
        );
    } else {
        // No se encontraron resultados para el ID especificado
        $customReport = array(
            'error' => 'No se encontraron datos para el ID especificado'
        );
    }

    // Cerrar la conexión a la base de datos
    $stmt->close();
    $conn->close();

    return $customReport; // Devolver el informe generado
}


if ($_SERVER['REQUEST_METHOD'] === 'REPORT') {
    // Obtener el ID de informe solicitado (si es necesario)
    $reportId = isset($_GET['id']) ? $_GET['id'] : '';

    // Generar el informe personalizado basado en el ID
    $report = generateCustomReport($reportId);

    // Enviar el informe como respuesta
    header('Content-Type: application/json');
    echo json_encode($report);
}

?>