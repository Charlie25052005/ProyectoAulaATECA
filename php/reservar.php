<?php
// Establecer el encabezado para respuesta JSON
header('Content-Type: application/json');

// Conexión a la base de datos (reemplaza con tus credenciales)
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'MiBaseDeDatos';

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($mysqli->connect_errno) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos: ' . $mysqli->connect_error]);
    exit;
}
$mysqli->set_charset('utf8mb4');

// 1. Validar que se recibieron los datos necesarios
if (
    !isset($_POST['fecha_reservada']) || !isset($_POST['hora_reservada']) ||
    !isset($_POST['id_usuario']) || !isset($_POST['id_curso'])
) {
    echo json_encode(['success' => false, 'error' => 'Faltan datos obligatorios']);
    exit;
}

// 2. Limpiar y tipar los datos
$fecha = $_POST['fecha_reservada'];
$hora = $_POST['hora_reservada'];
$id_usuario = (int)$_POST['id_usuario'];
$id_curso = (int)$_POST['id_curso'];

// Verificar conexión a la base de datos (redundante, pero seguro)
if (!$mysqli) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos']);
    exit;
}

// Validación básica de formato de fecha (YYYY-MM-DD)
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
    echo json_encode(['success' => false, 'error' => 'Formato de fecha inválido (YYYY-MM-DD)']);
    exit;
}

// 3. Verificar si ya existe una reserva para la misma fecha, hora Y curso
// (Si quieres bloquear la hora globalmente para todos los cursos, elimina "AND id_curso = ?")
$check_sql = "SELECT COUNT(*) as count 
                FROM Reserva 
                WHERE Fecha_reservada = ? 
                AND Hora_reservada = ? 
                AND id_curso = ?";

$check_stmt = $mysqli->prepare($check_sql);

if (!$check_stmt) {
    echo json_encode(['success' => false, 'error' => 'Error al preparar verificación: ' . $mysqli->error]);
    exit;
}

// Nota los tipos: 's' para fecha/hora (string), 'i' para id_curso (integer)
$check_stmt->bind_param('ssi', $fecha, $hora, $id_curso);
if (!$check_stmt->execute()) {
    echo json_encode(['success' => false, 'error' => 'Error al ejecutar verificación: ' . $check_stmt->error]);
    $check_stmt->close();
    exit;
}
$check_stmt->bind_result($count);
$check_stmt->fetch();
$check_stmt->close();

if ($count > 0) {
    echo json_encode(['success' => false, 'error' => 'El curso ya está reservado para esta fecha y hora']);
    exit;
}

// 4. Si no existe, proceder a insertar
$sql = "INSERT INTO Reserva (Fecha_reservada, Hora_reservada, id_usuario, id_curso) VALUES (?, ?, ?, ?)";
$stmt = $mysqli->prepare($sql);

if ($stmt) {
    // Tipos: 'ss' para string (fecha, hora), 'ii' para integers (usuario, curso)
    $stmt->bind_param('ssii', $fecha, $hora, $id_usuario, $id_curso);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Reserva creada exitosamente']);
    } else {
        // Error específico de MySQL (ej. violación de clave foránea)
        echo json_encode(['success' => false, 'error' => 'Error al ejecutar la inserción: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Error en la preparación de la consulta de inserción: ' . $mysqli->error]);
}

// Cerrar conexión
$mysqli->close();
