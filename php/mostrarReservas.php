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
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión: ' . $mysqli->connect_error]);
    exit;
}

// Consulta
$sql = "SELECT
            r.Fecha_reservada AS fecha,
            r.Hora_reservada AS hora,
            GROUP_CONCAT(
                DISTINCT u.Nombre
                ORDER BY u.Nombre
                SEPARATOR ', '
            ) AS nombres
        FROM Reserva r
        JOIN Usuarios u
            ON r.id_usuario = u.id_usuario
        GROUP BY r.Fecha_reservada, r.Hora_reservada
        ORDER BY r.Fecha_reservada, r.Hora_reservada";

$result = $mysqli->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la consulta']);
    exit;
}

$reservas = [];

while ($row = $result->fetch_assoc()) {
    $reservas[] = $row;
}

header('Content-Type: application/json');
echo json_encode($reservas);

$mysqli->close();
