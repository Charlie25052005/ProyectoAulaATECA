<?php
header('Content-Type: application/json; charset=utf-8');

// Configuración de la base de datos (ajusta)
$host = 'localhost';
$db   = 'MiBaseDeDatos';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'DB connection failed']);
    exit;
}

// Leer JSON del cuerpo
$input = json_decode(file_get_contents('php://input'), true);
$email = isset($input['correo']) ? trim($input['correo']) : '';
$password = isset($input['contrasena']) ? $input['contrasena'] : '';

// Validaciones básicas
if ($email === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Faltan credenciales']);
    exit;
}

// Consulta segura
$sql = "SELECT ID_usuario, Nombre, Apellido1, Apellido2, Correo, Contraseña FROM usuarios WHERE Correo = :correo LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([':correo' => $email]);
$user = $stmt->fetch();

if (!$user) {
    echo json_encode(['success' => false, 'error' => 'Usuario no encontrado']);
    exit;
}

// Para pruebas: comparar texto plano. En producción usa password_hash / password_verify
if ($password === $user['Contraseña']) {
    // Eliminar la contraseña del payload de salida
    unset($user['Contraseña']);
    echo json_encode(['success' => true, 'user' => $user]);
} else {
    echo json_encode(['success' => false, 'error' => 'Credenciales incorrectas']);
}