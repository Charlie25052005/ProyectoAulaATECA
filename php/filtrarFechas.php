<?php
// Habilitar la visualización de errores (solo para desarrollo)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cabecera JSON
header('Content-Type: application/json');

// Configuración de la base de datos
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'MiBaseDeDatos';

// Conexión
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($mysqli->connect_errno) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión: ' . $mysqli->connect_error]);
    exit;
}

// Comprobar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método de solicitud no válido']);
    exit;
}

// Consulta base
$sql = "SELECT 
            Reserva.Fecha_reservada AS fecha,
            Reserva.Hora_reservada AS hora,
            Usuarios.Nombre AS nombre
        FROM Reserva
        INNER JOIN Usuarios
            ON Reserva.id_usuario = Usuarios.id_usuario";

$conditions = [];
$params = [];
$types = "";

// FILTRO DINÁMICO: FECHA
if (isset($_POST['fecha_reservada']) && $_POST['fecha_reservada'] !== '') {
    $conditions[] = "Reserva.Fecha_reservada = ?";
    $params[] = $_POST['fecha_reservada'];
    $types .= "s";
}

// FILTRO DINÁMICO: HORA
if (isset($_POST['hora_reservada']) && $_POST['hora_reservada'] !== '') {
    $conditions[] = "Reserva.Hora_reservada = ?";
    $params[] = $_POST['hora_reservada'];
    $types .= "s";
}

// Añadir WHERE si hay filtros
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

// Ordenación
$sql .= " ORDER BY Reserva.Fecha_reservada, Reserva.Hora_reservada";

// Preparar
$stmt = $mysqli->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la preparación de la consulta']);
    exit;
}

// Bind dinámico
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();

$result = $stmt->get_result();

$reservas = [];

while ($row = $result->fetch_assoc()) {
    $reservas[] = $row;
}

// Respuesta JSON
echo json_encode($reservas);

$stmt->close();
$mysqli->close();
