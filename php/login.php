<?php
// DEV only: mostrar errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

// Config - ajusta estos valores a tu entorno
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'MiBaseDeDatos';
$table   = 'Usuarios'; // <- cambia por el nombre real de tu tabla

// Conexión
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($mysqli->connect_errno) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'DB connection failed: '.$mysqli->connect_error]);
    exit;
}
$mysqli->set_charset('utf8mb4');

// Método POST sólo
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

// Comprobación de parámetros
if (!isset($_POST['email']) || !isset($_POST['passwd'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
    exit;
}

$email = trim($_POST['email']);
$password = trim($_POST['passwd']);

if ($email === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Empty email or password']);
    exit;
}

// Consulta preparada: uso de alias para evitar problemas con nombres con acentos
$sql = "SELECT `ID_usuario`, `Nombre`, `Apellido1`, `Contraseña` AS `Contrasena` FROM `{$table}` WHERE `Correo` = ? LIMIT 1";
$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Prepare failed: '.$mysqli->error]);
    exit;
}

$stmt->bind_param('s', $email);
if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Execute failed: '.$stmt->error]);
    $stmt->close();
    $mysqli->close();
    exit;
}

$result = $stmt->get_result();
if ($result === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'get_result failed: '.$stmt->error]);
    $stmt->close();
    $mysqli->close();
    exit;
}

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'User not found']);
    $stmt->close();
    $mysqli->close();
    exit;
}

$row = $result->fetch_assoc();

// Comparación directa para pruebas (NO usar en producción)
$stored = isset($row['Contrasena']) ? $row['Contrasena'] : null;
if ($stored === null) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Password column not found (check column name)']);
    $stmt->close();
    $mysqli->close();
    exit;
}

if ($stored === $password) {
    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $row['ID_usuario'],
            'nombre' => $row['Nombre'],
            'apellido' => $row['Apellido1']
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid credentials']);
}

$stmt->close();
$mysqli->close();