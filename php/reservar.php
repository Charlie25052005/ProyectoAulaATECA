<?php
// Habilitar la visualización de errores (solo para desarrollo)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuración de la base de datos
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'MiBaseDeDatos';

// Conexión a la base de datos
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($mysqli->connect_errno) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión: ' . $mysqli->connect_error]);
    exit;
}

// Comprobar si se ha enviado una solicitud POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método de solicitud no válido']);
    exit;
}

// Comprobación de parámetros
$required_fields = ['fecha_reservada', 'hora_reservada', 'id_usuario', 'id_curso'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => "Falta el parámetro: $field"]);
        exit;
    }
}

// Preparar y ejecutar la inserción
$sql = "INSERT INTO Reserva (Fecha_reservada, Hora_reservada, id_usuario, id_curso) VALUES (?, ?, ?, ?)";
$stmt = $mysqli->prepare($sql);

if ($stmt) {
    $stmt->bind_param('ssii', $_POST['fecha_reservada'], $_POST['hora_reservada'], $_POST['id_usuario'], $_POST['id_curso']);
    $stmt->execute();

    // Comprobar si la inserción fue exitosa
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Reserva creada exitosamente']);
    } else {
        echo json_encode(['success' => false, 'error' => 'No se pudo crear la reserva']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Error en la preparación de la consulta']);
}

$mysqli->close();
