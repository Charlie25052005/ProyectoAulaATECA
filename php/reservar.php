<?php
// Verificar si ya existe una reserva para la misma fecha y hora
$check_sql = "SELECT COUNT(*) as count 
                FROM Reserva 
                WHERE Fecha_reservada = ? 
                AND Hora_reservada = ?";

$check_stmt = $mysqli->prepare($check_sql);
$check_stmt->bind_param('ss', $_POST['fecha_reservada'], $_POST['hora_reservada']);
$check_stmt->execute();
$check_stmt->bind_result($count);
$check_stmt->fetch();
$check_stmt->close();

if ($count > 0) {
    echo json_encode(['success' => false, 'error' => 'La reserva ya existe para esta fecha y hora']);
    exit;
}

// Si no existe, entonces insertar
$sql = "INSERT INTO Reserva (Fecha_reservada, Hora_reservada, id_usuario, id_curso) VALUES (?, ?, ?, ?)";
$stmt = $mysqli->prepare($sql);

if ($stmt) {
    $stmt->bind_param('ssii', $_POST['fecha_reservada'], $_POST['hora_reservada'], $_POST['id_usuario'], $_POST['id_curso']);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Reserva creada exitosamente']);
    } else {
        echo json_encode(['success' => false, 'error' => 'No se pudo crear la reserva']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Error en la preparación de la consulta']);
}
